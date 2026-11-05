from datetime import datetime
from collections import defaultdict

class CohortAnalyzer:
    def __init__(self, db_conn):
        self.conn = db_conn

    def compute_cohorts(self):
        cursor = self.conn.cursor()

        # Step 1: Find first purchase month for each customer
        query = """
            SELECT
                customer_id,
                MIN(order_date) as first_order_date
            FROM orders
            GROUP BY customer_id
        """
        first_orders = cursor.execute(query).fetchall()
        cust_cohort_map = {}
        for row in first_orders:
            first_dt = datetime.strptime(row['first_order_date'], '%Y-%m-%d')
            cust_cohort_map[row['customer_id']] = first_dt.strftime('%Y-%m')

        # Step 2: Fetch all customer orders with order month
        orders_query = """
            SELECT customer_id, order_date, total_amount
            FROM orders
            ORDER BY order_date ASC
        """
        orders = cursor.execute(orders_query).fetchall()

        # Group by cohort_month and period_month
        cohort_sizes = defaultdict(set)
        cohort_period_active = defaultdict(lambda: defaultdict(set))
        cohort_period_revenue = defaultdict(lambda: defaultdict(float))

        for o in orders:
            cid = o['customer_id']
            if cid not in cust_cohort_map:
                continue
            c_month = cust_cohort_map[cid]
            o_dt = datetime.strptime(o['order_date'], '%Y-%m-%d')
            c_dt = datetime.strptime(c_month, '%Y-%m')

            # Calculate period index (0, 1, 2, ...)
            period = (o_dt.year - c_dt.year) * 12 + (o_dt.month - c_dt.month)
            if period >= 0:
                cohort_sizes[c_month].add(cid)
                cohort_period_active[c_month][period].add(cid)
                cohort_period_revenue[c_month][period] += float(o['total_amount'])

        cursor.execute("DELETE FROM customer_cohorts")

        results = []
        for c_month in sorted(cohort_sizes.keys()):
            size = len(cohort_sizes[c_month])
            if size == 0:
                continue

            for period in sorted(cohort_period_active[c_month].keys()):
                active_count = len(cohort_period_active[c_month][period])
                retention_rate = round((active_count / size) * 100.0, 2)
                rev = round(cohort_period_revenue[c_month][period], 2)

                cursor.execute(
                    """INSERT INTO customer_cohorts
                       (cohort_month, period_month, active_customers, retention_rate, cohort_revenue)
                       VALUES (?, ?, ?, ?, ?)""",
                    (c_month, period, active_count, retention_rate, rev)
                )

                results.append({
                    'cohort_month': c_month,
                    'period_month': period,
                    'cohort_size': size,
                    'active_customers': active_count,
                    'retention_rate': retention_rate,
                    'cohort_revenue': rev
                })

        self.conn.commit()
        return results
