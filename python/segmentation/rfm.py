from datetime import datetime

class RFMAnalyzer:
    def __init__(self, db_conn):
        self.conn = db_conn

    def compute_rfm(self):
        cursor = self.conn.cursor()

        # Get reference max order date
        max_date_row = cursor.execute("SELECT MAX(order_date) as max_date FROM orders").fetchone()
        if not max_date_row or not max_date_row['max_date']:
            return []

        ref_date = datetime.strptime(max_date_row['max_date'], '%Y-%m-%d')

        # Aggregate per customer: Recency, Frequency, Monetary
        query = """
            SELECT
                c.id as customer_id,
                MAX(o.order_date) as last_order_date,
                COUNT(o.id) as frequency,
                COALESCE(SUM(o.total_amount), 0.0) as monetary
            FROM customers c
            JOIN orders o ON c.id = o.customer_id
            GROUP BY c.id
        """
        rows = cursor.execute(query).fetchall()
        if not rows:
            return []

        data = []
        recencies = []
        frequencies = []
        monetaries = []

        for r in rows:
            last_dt = datetime.strptime(r['last_order_date'], '%Y-%m-%d')
            rec_days = max(0, (ref_date - last_dt).days)
            freq = int(r['frequency'])
            mon = float(r['monetary'])

            data.append({
                'customer_id': r['customer_id'],
                'recency': rec_days,
                'frequency': freq,
                'monetary': mon
            })
            recencies.append(rec_days)
            frequencies.append(freq)
            monetaries.append(mon)

        # Calculate quintiles (1-5 score)
        r_quintiles = self._quintiles(recencies, reverse=True) # Lower recency days = higher score 5
        f_quintiles = self._quintiles(frequencies, reverse=False)
        m_quintiles = self._quintiles(monetaries, reverse=False)

        cursor.execute("DELETE FROM customer_segments")

        results = []
        for d in data:
            r_score = self._score(d['recency'], r_quintiles, reverse=True)
            f_score = self._score(d['frequency'], f_quintiles, reverse=False)
            m_score = self._score(d['monetary'], m_quintiles, reverse=False)
            rfm_code = f"{r_score}{f_score}{m_score}"
            segment = self._classify_segment(r_score, f_score, m_score)

            cursor.execute(
                """INSERT INTO customer_segments
                   (customer_id, recency_days, frequency, monetary, r_score, f_score, m_score, rfm_score, segment_name)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)""",
                (d['customer_id'], d['recency'], d['frequency'], d['monetary'], r_score, f_score, m_score, rfm_code, segment)
            )

            # Update customer table segment
            cursor.execute("UPDATE customers SET segment = ? WHERE id = ?", (segment, d['customer_id']))

            results.append({
                'customer_id': d['customer_id'],
                'recency_days': d['recency'],
                'frequency': d['frequency'],
                'monetary': round(d['monetary'], 2),
                'r_score': r_score,
                'f_score': f_score,
                'm_score': m_score,
                'rfm_score': rfm_code,
                'segment_name': segment
            })

        self.conn.commit()
        return results

    def _quintiles(self, values, reverse=False):
        s = sorted(values)
        n = len(s)
        if n == 0:
            return [0, 0, 0, 0]
        q = [s[int(n * 0.2)], s[int(n * 0.4)], s[int(n * 0.6)], s[int(n * 0.8)]]
        return q

    def _score(self, val, q, reverse=False):
        if not reverse:
            if val <= q[0]: return 1
            elif val <= q[1]: return 2
            elif val <= q[2]: return 3
            elif val <= q[3]: return 4
            else: return 5
        else:
            if val <= q[0]: return 5
            elif val <= q[1]: return 4
            elif val <= q[2]: return 3
            elif val <= q[3]: return 2
            else: return 1

    def _classify_segment(self, r, f, m):
        fm = (f + m) / 2.0
        if r >= 4 and fm >= 4:
            return 'Champions'
        elif r >= 3 and fm >= 3:
            return 'Loyal Customers'
        elif r >= 4 and fm <= 2:
            return 'New Customers'
        elif r >= 3 and fm <= 3:
            return 'Potential Loyalists'
        elif r <= 2 and fm >= 4:
            return "Can't Lose Them"
        elif r <= 2 and fm >= 2:
            return 'At Risk'
        else:
            return 'Lost Customers'

