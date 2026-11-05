import json
from .cleaner import DataCleaner
from .validator import DataValidator

class DataTransformer:
    def __init__(self, db_conn):
        self.conn = db_conn

    def process_import(self, import_id, rows):
        validator_result = DataValidator.validate_dataset(rows)
        score = validator_result['quality_score']
        metrics = validator_result['metrics']

        # Record Data Quality Logs
        cursor = self.conn.cursor()
        for metric_name, m_score in metrics.items():
            cursor.execute(
                """INSERT INTO data_quality_logs (import_id, metric_name, score, issue_count, details)
                   VALUES (?, ?, ?, ?, ?)""",
                (import_id, metric_name.capitalize(), m_score,
                 validator_result['missing_count'] if metric_name == 'completeness' else (
                     validator_result['duplicate_count'] if metric_name == 'uniqueness' else (
                         validator_result['invalid_date_count'] + validator_result['invalid_numeric_count']
                     )
                 ),
                 json.dumps(validator_result.get('issues', [])[:10]))
            )

        if score < 50.0:
            cursor.execute(
                "UPDATE data_imports SET status = 'FAILED', quality_score = ?, error_log = ? WHERE id = ?",
                (score, json.dumps(validator_result.get('issues', [])), import_id)
            )
            self.conn.commit()
            return {'success': False, 'message': 'Dataset rejected due to low data quality score', 'report': validator_result}

        # Cache existing customers & products
        cust_map = {}
        for row in cursor.execute("SELECT id, customer_code, region_id FROM customers").fetchall():
            cust_map[row['customer_code']] = {'id': row['id'], 'region_id': row['region_id']}

        prod_map = {}
        for row in cursor.execute("SELECT id, sku, cost_price, selling_price FROM products").fetchall():
            prod_map[row['sku']] = {'id': row['id'], 'cost': float(row['cost_price']), 'price': float(row['selling_price'])}

        default_region_id = 1
        imported_orders = 0
        imported_items = 0
        failed_count = 0

        # Group rows by order_number
        orders_dict = {}
        for r in rows:
            clean_r = DataCleaner.clean_row(r)
            ord_num = clean_r.get('order_number')
            if not ord_num:
                failed_count += 1
                continue
            if ord_num not in orders_dict:
                orders_dict[ord_num] = []
            orders_dict[ord_num].append(clean_r)

        for ord_num, items in orders_dict.items():
            first = items[0]
            cust_code = first.get('customer_code', 'CUST-GENERAL')
            if cust_code not in cust_map:
                # Create customer if doesn't exist
                cursor.execute(
                    """INSERT INTO customers (customer_code, name, email, city, state, region_id, customer_since)
                       VALUES (?, ?, ?, 'Imported City', 'NA', ?, ?)""",
                    (cust_code, f"Customer {cust_code}", f"{cust_code.lower()}@imported.com", default_region_id, first.get('order_date', '2026-01-01'))
                )
                new_cust_id = cursor.lastrowid
                cust_map[cust_code] = {'id': new_cust_id, 'region_id': default_region_id}

            cust_info = cust_map[cust_code]
            order_date = first.get('order_date', '2026-01-01')
            channel = first.get('channel', 'Direct Sales')

            # Insert order
            cursor.execute(
                """INSERT INTO orders (order_number, customer_id, salesperson_id, region_id, order_date, channel, status, subtotal, discount, tax, total_amount)
                   VALUES (?, ?, NULL, ?, ?, ?, 'Completed', 0, 0, 0, 0)""",
                (ord_num, cust_info['id'], cust_info['region_id'], order_date, channel)
            )
            order_id = cursor.lastrowid
            imported_orders += 1

            subtotal = 0.0
            for item in items:
                sku = item.get('sku', '')
                qty = item.get('quantity', 1)
                unit_price = item.get('unit_price', 0.0)
                item_discount = item.get('discount', 0.0)

                if sku not in prod_map:
                    # Auto-create product if new
                    cursor.execute(
                        "INSERT INTO products (sku, product_name, category_id, cost_price, selling_price, active) VALUES (?, ?, 1, ?, ?, 1)",
                        (sku, f"Imported Product {sku}", round(unit_price * 0.5, 2), unit_price)
                    )
                    new_prod_id = cursor.lastrowid
                    prod_map[sku] = {'id': new_prod_id, 'cost': round(unit_price * 0.5, 2), 'price': unit_price}

                prod_info = prod_map[sku]
                cost = round(prod_info['cost'] * qty, 2)
                line_total = round((unit_price - item_discount) * qty, 2)
                profit = round(line_total - cost, 2)

                cursor.execute(
                    """INSERT INTO order_items (order_id, product_id, quantity, unit_price, discount, cost, profit)
                       VALUES (?, ?, ?, ?, ?, ?, ?)""",
                    (order_id, prod_info['id'], qty, unit_price, item_discount * qty, cost, profit)
                )
                imported_items += 1
                subtotal += line_total

            tax = round(subtotal * 0.08, 2)
            total = round(subtotal + tax, 2)
            cursor.execute(
                "UPDATE orders SET subtotal = ?, tax = ?, total_amount = ? WHERE id = ?",
                (subtotal, tax, total, order_id)
            )

        cursor.execute(
            """UPDATE data_imports
               SET status = 'COMPLETED', quality_score = ?, imported_rows = ?, failed_rows = ?, total_rows = ?
               WHERE id = ?""",
            (score, imported_items, failed_count, len(rows), import_id)
        )
        self.conn.commit()

        return {
            'success': True,
            'imported_orders': imported_orders,
            'imported_items': imported_items,
            'failed_count': failed_count,
            'quality_score': score,
            'metrics': metrics
        }

