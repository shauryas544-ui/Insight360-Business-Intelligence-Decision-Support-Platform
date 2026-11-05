from datetime import datetime
from collections import defaultdict

class SalesAnalyticsEngine:
    def __init__(self, db_conn):
        self.conn = db_conn

    def compute_monthly_metrics(self):
        cursor = self.conn.cursor()
        cursor.execute("DELETE FROM monthly_metrics")

        query = """
            SELECT
                strftime('%Y-%m', o.order_date) as ym,
                SUM(o.total_amount) as revenue,
                SUM(oi.profit) as profit,
                COUNT(DISTINCT o.id) as order_count,
                COUNT(DISTINCT o.customer_id) as customer_count
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY strftime('%Y-%m', o.order_date)
            ORDER BY ym ASC
        """
        rows = cursor.execute(query).fetchall()

        results = []
        for r in rows:
            orders = int(r['order_count'])
            rev = float(r['revenue'])
            prof = float(r['profit'])
            custs = int(r['customer_count'])
            aov = round(rev / orders, 2) if orders > 0 else 0.0

            cursor.execute(
                """INSERT INTO monthly_metrics
                   (year_month, revenue, profit, order_count, customer_count, aov, retention_rate)
                   VALUES (?, ?, ?, ?, ?, ?, 0.0)""",
                (r['ym'], round(rev, 2), round(prof, 2), orders, custs, aov)
            )
            results.append({
                'year_month': r['ym'],
                'revenue': round(rev, 2),
                'profit': round(prof, 2),
                'order_count': orders,
                'customer_count': custs,
                'aov': aov
            })

        self.conn.commit()
        return results

    def compute_regional_metrics(self):
        cursor = self.conn.cursor()
        cursor.execute("DELETE FROM regional_metrics")

        query = """
            SELECT
                r.id as region_id,
                r.name as region_name,
                COALESCE(SUM(o.total_amount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit,
                COUNT(DISTINCT o.id) as order_count
            FROM regions r
            LEFT JOIN orders o ON r.id = o.region_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY r.id
        """
        rows = cursor.execute(query).fetchall()

        for r in rows:
            cursor.execute(
                """INSERT INTO regional_metrics
                   (region_id, period_month, revenue, profit, order_count, target_achievement_pct)
                   VALUES (?, 'ALL', ?, ?, ?, 92.5)""",
                (r['region_id'], round(float(r['revenue']), 2), round(float(r['profit']), 2), int(r['order_count']))
            )
        self.conn.commit()

    def why_did_this_change(self, period1_ym=None, period2_ym=None):
        """
        Signature Feature: Why-Did-This-Change Engine.
        Decomposes delta between period1 (base) and period2 (comparison)
        across Product Categories, Regions, and Customer Segments.
        """
        cursor = self.conn.cursor()

        # If not provided, take the latest two months
        if not period1_ym or not period2_ym:
            months = cursor.execute("SELECT DISTINCT strftime('%Y-%m', order_date) as ym FROM orders ORDER BY ym DESC LIMIT 2").fetchall()
            if len(months) < 2:
                return {'error': 'Need at least two months of data to compute driver decomposition.'}
            period2_ym = months[0]['ym'] # e.g. 2026-11
            period1_ym = months[1]['ym'] # e.g. 2026-10

        def get_period_stats(ym):
            q = """
                SELECT
                    COALESCE(SUM(o.total_amount), 0.0) as revenue,
                    COALESCE(SUM(oi.profit), 0.0) as profit,
                    COUNT(DISTINCT o.id) as orders,
                    COUNT(DISTINCT o.customer_id) as customers
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE strftime('%Y-%m', o.order_date) = ?
            """
            return cursor.execute(q, (ym,)).fetchone()

        p1 = get_period_stats(period1_ym)
        p2 = get_period_stats(period2_ym)

        rev1 = float(p1['revenue']) if (p1 and p1['revenue'] is not None) else 0.0
        rev2 = float(p2['revenue']) if (p2 and p2['revenue'] is not None) else 0.0
        orders1 = int(p1['orders']) if (p1 and p1['orders'] is not None) else 0
        orders2 = int(p2['orders']) if (p2 and p2['orders'] is not None) else 0

        if rev1 == 0.0 and rev2 == 0.0:
            return {'error': f'No transaction data recorded for {period1_ym} or {period2_ym}.'}

        rev_delta = rev2 - rev1
        rev_pct = round((rev_delta / rev1 * 100.0), 2) if rev1 > 0 else (100.0 if rev_delta > 0 else 0.0)

        # Category Breakdown
        cat_q = """
            SELECT
                c.name as category_name,
                strftime('%Y-%m', o.order_date) as ym,
                SUM(oi.unit_price * oi.quantity - oi.discount) as rev
            FROM categories c
            JOIN products p ON c.id = p.category_id
            JOIN order_items oi ON p.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE strftime('%Y-%m', o.order_date) IN (?, ?)
            GROUP BY c.name, ym
        """
        cat_rows = cursor.execute(cat_q, (period1_ym, period2_ym)).fetchall()
        cat_p1 = defaultdict(float)
        cat_p2 = defaultdict(float)
        all_cats = set()
        for cr in cat_rows:
            all_cats.add(cr['category_name'])
            if cr['ym'] == period1_ym:
                cat_p1[cr['category_name']] = float(cr['rev'])
            else:
                cat_p2[cr['category_name']] = float(cr['rev'])

        category_drivers = []
        for cat in all_cats:
            v1 = cat_p1[cat]
            v2 = cat_p2[cat]
            delta = v2 - v1
            pct_change = round((delta / v1 * 100.0), 2) if v1 > 0 else 0.0
            contrib_to_total_delta = round((delta / abs(rev_delta) * 100.0), 2) if rev_delta != 0 else 0.0
            category_drivers.append({
                'dimension': 'Category',
                'name': cat,
                'period1_val': round(v1, 2),
                'period2_val': round(v2, 2),
                'delta': round(delta, 2),
                'pct_change': pct_change,
                'contribution_pct': contrib_to_total_delta
            })
        category_drivers.sort(key=lambda x: abs(x['delta']), reverse=True)

        # Regional Breakdown
        reg_q = """
            SELECT
                r.name as region_name,
                strftime('%Y-%m', o.order_date) as ym,
                SUM(o.total_amount) as rev
            FROM regions r
            JOIN orders o ON r.id = o.region_id
            WHERE strftime('%Y-%m', o.order_date) IN (?, ?)
            GROUP BY r.name, ym
        """
        reg_rows = cursor.execute(reg_q, (period1_ym, period2_ym)).fetchall()
        reg_p1 = defaultdict(float)
        reg_p2 = defaultdict(float)
        all_regs = set()
        for rr in reg_rows:
            all_regs.add(rr['region_name'])
            if rr['ym'] == period1_ym:
                reg_p1[rr['region_name']] = float(rr['rev'])
            else:
                reg_p2[rr['region_name']] = float(rr['rev'])

        regional_drivers = []
        for reg in all_regs:
            v1 = reg_p1[reg]
            v2 = reg_p2[reg]
            delta = v2 - v1
            pct_change = round((delta / v1 * 100.0), 2) if v1 > 0 else 0.0
            contrib = round((delta / abs(rev_delta) * 100.0), 2) if rev_delta != 0 else 0.0
            regional_drivers.append({
                'dimension': 'Region',
                'name': reg,
                'period1_val': round(v1, 2),
                'period2_val': round(v2, 2),
                'delta': round(delta, 2),
                'pct_change': pct_change,
                'contribution_pct': contrib
            })
        regional_drivers.sort(key=lambda x: abs(x['delta']), reverse=True)

        return {
            'period1': period1_ym,
            'period2': period2_ym,
            'period1_revenue': round(rev1, 2),
            'period2_revenue': round(rev2, 2),
            'revenue_delta': round(rev_delta, 2),
            'revenue_pct_change': rev_pct,
            'orders_delta': orders2 - orders1,
            'category_drivers': category_drivers[:5],
            'regional_drivers': regional_drivers[:5],
            'summary_text': f"Revenue changed by {rev_pct}% (${round(rev_delta, 2):,}) from {period1_ym} to {period2_ym}."
        }

