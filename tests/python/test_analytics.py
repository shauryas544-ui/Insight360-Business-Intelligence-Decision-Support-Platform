import unittest
import sys
from pathlib import Path

# Add project root to sys.path
BASE_DIR = Path(__file__).resolve().parent.parent.parent
sys.path.insert(0, str(BASE_DIR / 'python'))

from config import get_db_connection
from etl.cleaner import DataCleaner
from etl.validator import DataValidator
from segmentation.rfm import RFMAnalyzer
from stats.cohorts import CohortAnalyzer
from stats.pareto import ParetoAnalyzer
from forecasting.forecast import ForecastEngine
from anomaly.detector import AnomalyDetector

class TestInsightFlowAnalytics(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.conn = get_db_connection()

    @classmethod
    def tearDownClass(cls):
        cls.conn.close()

    def test_cleaner_currency_and_date(self):
        cleaned_price = DataCleaner.clean_float("$1,450.50")
        self.assertEqual(cleaned_price, 1450.50)

        cleaned_date = DataCleaner.normalize_date("11/05/2026")
        self.assertEqual(cleaned_date, "2026-11-05")

    def test_validator_detects_missing(self):
        sample_rows = [
            {'order_number': 'ORD-1', 'order_date': '2026-11-01', 'customer_code': 'C-1', 'sku': 'S-1', 'quantity': 1, 'unit_price': 100},
            {'order_number': 'ORD-2', 'order_date': '2026-11-02', 'customer_code': '', 'sku': 'S-1', 'quantity': 2, 'unit_price': 50}
        ]
        res = DataValidator.validate_dataset(sample_rows)
        self.assertEqual(res['missing_count'], 1)
        self.assertGreater(res['quality_score'], 0)

    def test_rfm_segments_assignment(self):
        analyzer = RFMAnalyzer(self.conn)
        results = analyzer.compute_rfm()
        self.assertGreater(len(results), 0)
        valid_segments = {'Champions', 'Loyal Customers', 'Potential Loyalists', 'New Customers', 'At Risk', "Can't Lose Them", 'Lost Customers'}
        for r in results:
            self.assertIn(r['segment_name'], valid_segments)
            self.assertTrue(1 <= r['r_score'] <= 5)
            self.assertTrue(1 <= r['f_score'] <= 5)
            self.assertTrue(1 <= r['m_score'] <= 5)

    def test_cohorts_matrix(self):
        analyzer = CohortAnalyzer(self.conn)
        results = analyzer.compute_cohorts()
        self.assertGreater(len(results), 0)
        # Period 0 retention must be 100%
        p0_cohorts = [c for c in results if c['period_month'] == 0]
        self.assertGreater(len(p0_cohorts), 0)
        for c in p0_cohorts:
            self.assertEqual(c['retention_rate'], 100.0)

    def test_pareto_distribution(self):
        analyzer = ParetoAnalyzer(self.conn)
        results = analyzer.compute_product_pareto()
        self.assertGreater(len(results), 0)
        classes = {r['pareto_class'] for r in results}
        self.assertTrue('A' in classes)

    def test_forecast_confidence_bounds(self):
        engine = ForecastEngine(self.conn)
        res = engine.generate_forecasts()
        self.assertIn('revenue', res)
        for fc in res['revenue']:
            self.assertLessEqual(fc['lower_bound'], fc['predicted_value'])
            self.assertGreaterEqual(fc['upper_bound'], fc['predicted_value'])

if __name__ == '__main__':
    unittest.main()

