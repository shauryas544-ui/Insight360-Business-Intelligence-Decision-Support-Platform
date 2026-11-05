class ParetoAnalyzer:
    def __init__(self, db_conn):
        self.conn = db_conn

    def compute_product_pareto(self):
        cursor = self.conn.cursor()

        query = """
            SELECT
                p.id as product_id,
                p.product_name,
                p.sku,
                c.name as category_name,
                COALESCE(SUM(oi.quantity), 0) as units_sold,
                COALESCE(SUM(oi.unit_price * oi.quantity - oi.discount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit,
                (SELECT COUNT(*) FROM returns r JOIN order_items oi2 ON r.order_item_id = oi2.id WHERE oi2.product_id = p.id) as return_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            GROUP BY p.id
            ORDER BY revenue DESC
        """
        rows = cursor.execute(query).fetchall()
        total_revenue = sum(float(r['revenue']) for r in rows)

        if total_revenue <= 0:
            return []

        cumulative_rev = 0.0
        results = []
        cursor.execute("DELETE FROM product_metrics")

        for r in rows:
            rev = float(r['revenue'])
            profit = float(r['profit'])
            units = int(r['units_sold'])
            ret_count = int(r['return_count'])
            margin_pct = round((profit / rev * 100.0), 2) if rev > 0 else 0.0
            return_rate = round((ret_count / units * 100.0), 2) if units > 0 else 0.0

            cumulative_rev += rev
            cum_pct = (cumulative_rev / total_revenue) * 100.0

            if cum_pct <= 80.0:
                pareto_class = 'A'
            elif cum_pct <= 95.0:
                pareto_class = 'B'
            else:
                pareto_class = 'C'

            cursor.execute(
                """INSERT INTO product_metrics
                   (product_id, period_month, units_sold, revenue, profit, margin_pct, return_rate, pareto_class)
                   VALUES (?, 'ALL', ?, ?, ?, ?, ?, ?)""",
                (r['product_id'], units, round(rev, 2), round(profit, 2), margin_pct, return_rate, pareto_class)
            )

            results.append({
                'product_id': r['product_id'],
                'product_name': r['product_name'],
                'sku': r['sku'],
                'category_name': r['category_name'],
                'revenue': round(rev, 2),
                'profit': round(profit, 2),
                'units_sold': units,
                'margin_pct': margin_pct,
                'return_rate': return_rate,
                'cumulative_pct': round(cum_pct, 2),
                'pareto_class': pareto_class
            })

        self.conn.commit()
        return results

    def compute_category_pareto(self):
        cursor = self.conn.cursor()
        query = """
            SELECT
                c.id,
                c.name,
                COALESCE(SUM(oi.unit_price * oi.quantity - oi.discount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit
            FROM categories c
            JOIN products p ON c.id = p.category_id
            JOIN order_items oi ON p.id = oi.product_id
            GROUP BY c.id
            ORDER BY revenue DESC
        """
        rows = cursor.execute(query).fetchall()
        total_rev = sum(float(r['revenue']) for r in rows)
        if total_rev == 0: return []

        cum = 0.0
        results = []
        for r in rows:
            rev = float(r['revenue'])
            cum += rev
            results.append({
                'id': r['id'],
                'name': r['name'],
                'revenue': round(rev, 2),
                'profit': round(float(r['profit']), 2),
                'share_pct': round((rev / total_rev) * 100.0, 2),
                'cumulative_pct': round((cum / total_rev) * 100.0, 2)
            })
        return results
