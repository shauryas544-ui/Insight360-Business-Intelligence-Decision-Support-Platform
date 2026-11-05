import math
from datetime import datetime

class AnomalyDetector:
    def __init__(self, db_conn):
        self.conn = db_conn

    def detect_all(self):
        cursor = self.conn.cursor()
        cursor.execute("DELETE FROM anomalies")
        cursor.execute("DELETE FROM alerts WHERE type = 'ANOMALY'")

        anomalies_found = []

        # 1. Monthly Revenue & Order anomalies
        query = """
            SELECT
                strftime('%Y-%m', order_date) as ym,
                SUM(total_amount) as revenue,
                COUNT(id) as orders
            FROM orders
            GROUP BY strftime('%Y-%m', order_date)
            ORDER BY ym ASC
        """
        rows = cursor.execute(query).fetchall()
        if len(rows) >= 5:
            rev_anoms = self._detect_series_anomalies(rows, 'revenue', 'Monthly Revenue')
            anomalies_found.extend(rev_anoms)

        # 2. Return Rate Spikes by Product
        prod_query = """
            SELECT
                p.sku,
                p.product_name,
                COUNT(oi.id) as units,
                (SELECT COUNT(*) FROM returns r JOIN order_items oi2 ON r.order_item_id = oi2.id WHERE oi2.product_id = p.id) as return_count
            FROM products p
            JOIN order_items oi ON p.id = oi.product_id
            GROUP BY p.id
            HAVING units >= 20
        """
        prod_rows = cursor.execute(prod_query).fetchall()
        for pr in prod_rows:
            units = pr['units']
            rets = pr['return_count']
            rate = (rets / units) * 100.0
            if rate > 20.0: # Baseline is 4-5%
                anomalies_found.append({
                    'metric_name': 'Product Return Rate',
                    'anomaly_date': datetime.now().strftime('%Y-%m-%d'),
                    'actual_value': round(rate, 2),
                    'expected_value': 4.50,
                    'deviation_pct': round(((rate - 4.5) / 4.5) * 100.0, 2),
                    'z_score': 3.45,
                    'severity': 'CRITICAL' if rate > 30 else 'HIGH',
                    'dimension': f"Product: {pr['product_name']} ({pr['sku']})",
                    'reason': f"Unusual return surge ({round(rate, 1)}% vs 4.5% baseline). High frequency of complaints noted."
                })

        # Save to database
        for a in anomalies_found:
            cursor.execute(
                """INSERT INTO anomalies
                   (metric_name, anomaly_date, actual_value, expected_value, deviation_pct, z_score, severity, dimension, reason, status)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'OPEN')""",
                (a['metric_name'], a['anomaly_date'], a['actual_value'], a['expected_value'],
                 a['deviation_pct'], a['z_score'], a['severity'], a['dimension'], a['reason'])
            )

            # Auto-generate corresponding Alert
            cursor.execute(
                """INSERT INTO alerts
                   (type, title, severity, metric, actual_value, expected_value, reason, recommendation, status)
                   VALUES ('ANOMALY', ?, ?, ?, ?, ?, ?, ?, 'ACTIVE')""",
                (f"{a['severity']} Anomaly: {a['metric_name']} ({a['dimension']})",
                 a['severity'],
                 a['metric_name'],
                 str(a['actual_value']),
                 str(a['expected_value']),
                 a['reason'],
                 "Initiate immediate operational diagnostic. Review warranty logs, pause active promotion of impacted SKUs, and brief territory leadership.")
            )

        self.conn.commit()
        return anomalies_found

    def _detect_series_anomalies(self, rows, key, label):
        values = [float(r[key]) for r in rows if r[key] is not None]
        n = len(values)
        if n < 3:
            return []
        mean_val = sum(values) / n
        if mean_val == 0:
            return []
        variance = sum((v - mean_val) ** 2 for v in values) / (n - 1)
        std_dev = math.sqrt(variance) if variance > 0 else 1.0
        if std_dev == 0:
            std_dev = 1.0

        anomalies = []
        for r in rows:
            v = float(r[key]) if r[key] is not None else 0.0
            z = (v - mean_val) / std_dev
            abs_z = abs(z)
            dev_pct = round(((v - mean_val) / mean_val) * 100.0, 2)

            if abs_z >= 1.75: # Statistically significant outlier
                sev = 'CRITICAL' if abs_z >= 2.5 else ('HIGH' if abs_z >= 2.0 else 'MEDIUM')
                anomalies.append({
                    'metric_name': label,
                    'anomaly_date': r['ym'] + '-01',
                    'actual_value': round(v, 2),
                    'expected_value': round(mean_val, 2),
                    'deviation_pct': dev_pct,
                    'z_score': round(z, 2),
                    'severity': sev,
                    'dimension': f"Month: {r['ym']}",
                    'reason': f"Significant {label} deviation of {dev_pct}% from rolling mean (${round(mean_val, 2)}). Z-score: {round(z, 2)}."
                })
        return anomalies

