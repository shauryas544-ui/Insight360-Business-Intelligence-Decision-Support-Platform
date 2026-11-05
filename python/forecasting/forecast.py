import math
from datetime import datetime, timedelta

class ForecastEngine:
    def __init__(self, db_conn):
        self.conn = db_conn

    def generate_forecasts(self):
        cursor = self.conn.cursor()

        # Aggregate monthly data
        query = """
            SELECT
                strftime('%Y-%m', order_date) as ym,
                COALESCE(SUM(total_amount), 0.0) as revenue,
                COUNT(id) as orders
            FROM orders
            GROUP BY strftime('%Y-%m', order_date)
            ORDER BY ym ASC
        """
        rows = cursor.execute(query).fetchall()
        if len(rows) < 4:
            return {'success': False, 'message': 'Insufficient historical periods for forecasting (minimum 4 months required).'}

        rev_series = [float(r['revenue']) for r in rows]
        order_series = [float(r['orders']) for r in rows]
        last_ym = rows[-1]['ym']

        cursor.execute("DELETE FROM forecast_results")

        rev_forecasts = self._forecast_series(rev_series, last_ym, 'Revenue')
        order_forecasts = self._forecast_series(order_series, last_ym, 'Orders')

        for item in rev_forecasts + order_forecasts:
            cursor.execute(
                """INSERT INTO forecast_results
                   (metric_name, forecast_date, horizon_days, predicted_value, lower_bound, upper_bound, confidence_level, method)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)""",
                (item['metric_name'], item['forecast_date'], item['horizon_days'],
                 item['predicted_value'], item['lower_bound'], item['upper_bound'],
                 item['confidence_level'], item['method'])
            )

        self.conn.commit()
        return {'revenue': rev_forecasts, 'orders': order_forecasts}

    def _forecast_series(self, series, last_ym, metric_name):
        n = len(series)
        # Linear regression trend estimation: y = a + b * x
        x_vals = list(range(n))
        x_mean = sum(x_vals) / n
        y_mean = sum(series) / n

        numerator = sum((x_vals[i] - x_mean) * (series[i] - y_mean) for i in range(n))
        denominator = sum((x_vals[i] - x_mean) ** 2 for i in range(n))
        b = numerator / denominator if denominator != 0 else 0.0
        a = y_mean - (b * x_mean)

        # Standard error of regression
        residuals = [series[i] - (a + b * x_vals[i]) for i in range(n)]
        sse = sum(r ** 2 for r in residuals)
        std_err = math.sqrt(sse / max(1, n - 2)) if n > 2 else y_mean * 0.1

        # Forecast horizons: 30 days (1 month), 60 days (2 months), 90 days (3 months)
        horizons = [(30, 1), (60, 2), (90, 3)]
        results = []

        last_dt = datetime.strptime(last_ym + '-01', '%Y-%m-%d')

        for days, m_ahead in horizons:
            future_x = n - 1 + m_ahead
            # Seasonal dampening / baseline projection
            base_pred = a + (b * future_x)

            # Confidence interval (95% approx 1.96 * SE * sqrt(1 + 1/n + (x0 - xbar)^2 / denom))
            margin = 1.96 * std_err * math.sqrt(1 + (1.0 / n) + ((future_x - x_mean)**2 / max(1, denominator)))

            # Next month date
            future_year = last_dt.year + ((last_dt.month - 1 + m_ahead) // 12)
            future_month = ((last_dt.month - 1 + m_ahead) % 12) + 1
            f_date = f"{future_year:04d}-{future_month:02d}-01"

            predicted = max(0.0, round(base_pred, 2))
            lower = max(0.0, round(base_pred - margin, 2))
            upper = round(base_pred + margin, 2)

            results.append({
                'metric_name': metric_name,
                'forecast_date': f_date,
                'horizon_days': days,
                'predicted_value': predicted,
                'lower_bound': lower,
                'upper_bound': upper,
                'confidence_level': 95.0,
                'method': 'Linear Trend Regression with Standard Error Bounds'
            })

        return results

