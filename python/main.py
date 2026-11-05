import sys
import json
import argparse
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import urlparse, parse_qs

from config import get_db_connection, ENV
from etl.loader import DataLoader
from etl.transformer import DataTransformer
from segmentation.rfm import RFMAnalyzer
from stats.cohorts import CohortAnalyzer
from stats.pareto import ParetoAnalyzer
from forecasting.forecast import ForecastEngine
from anomaly.detector import AnomalyDetector
from analytics.sales import SalesAnalyticsEngine
from recommendations.engine import RecommendationEngine

def run_all_jobs():
    print(">>> Starting Full InsightFlow Analytics Run...")
    conn = get_db_connection()

    # 1. Sales & Regional Aggregations
    print("1. Computing Monthly & Regional Sales Metrics...")
    sales_engine = SalesAnalyticsEngine(conn)
    sales_engine.compute_monthly_metrics()
    sales_engine.compute_regional_metrics()

    # 2. RFM Segmentation
    print("2. Computing Customer RFM Segmentation...")
    rfm_analyzer = RFMAnalyzer(conn)
    rfm_results = rfm_analyzer.compute_rfm()
    print(f"   Processed {len(rfm_results)} customer RFM profiles.")

    # 3. Cohorts
    print("3. Computing Cohort Retention Matrix...")
    cohort_analyzer = CohortAnalyzer(conn)
    cohort_results = cohort_analyzer.compute_cohorts()
    print(f"   Generated {len(cohort_results)} cohort periods.")

    # 4. Pareto Analysis
    print("4. Computing Product Pareto 80/20 Distribution...")
    pareto_analyzer = ParetoAnalyzer(conn)
    pareto_results = pareto_analyzer.compute_product_pareto()
    print(f"   Analyzed {len(pareto_results)} products.")

    # 5. Forecasting
    print("5. Generating 30, 60, 90-Day Trend & Seasonality Forecasts...")
    forecast_engine = ForecastEngine(conn)
    fc_results = forecast_engine.generate_forecasts()
    print("   Forecast calculations completed.")

    # 6. Anomaly Detection
    print("6. Executing Anomaly Detection Engine...")
    anomaly_detector = AnomalyDetector(conn)
    anom_results = anomaly_detector.detect_all()
    print(f"   Identified {len(anom_results)} statistical anomalies & triggered alerts.")

    # 7. Recommendation Engine
    print("7. Generating Evidence-based Recommendations...")
    rec_engine = RecommendationEngine(conn)
    rec_results = rec_engine.generate_recommendations()
    print(f"   Generated {len(rec_results)} business recommendations.")

    conn.close()
    print("[OK] Full Analytics Run Completed Successfully!")
    return {
        'status': 'SUCCESS',
        'customers_rfm': len(rfm_results),
        'cohorts': len(cohort_results),
        'pareto_products': len(pareto_results),
        'anomalies': len(anom_results),
        'recommendations': len(rec_results)
    }

def run_import(import_id, file_path):
    conn = get_db_connection()
    try:
        rows = DataLoader.load_file(file_path)
        transformer = DataTransformer(conn)
        result = transformer.process_import(int(import_id), rows)
        if result['success']:
            # Refresh analytics after import
            SalesAnalyticsEngine(conn).compute_monthly_metrics()
            RFMAnalyzer(conn).compute_rfm()
            ParetoAnalyzer(conn).compute_product_pareto()
        conn.close()
        return result
    except Exception as e:
        conn.close()
        return {'success': False, 'message': str(e)}

class SimpleAnalyticsServer(BaseHTTPRequestHandler):
    def do_POST(self):
        parsed = urlparse(self.path)
        path = parsed.path
        length = int(self.headers.get('Content-Length', 0))
        body = self.rfile.read(length).decode('utf-8') if length > 0 else '{}'
        try:
            payload = json.loads(body) if body else {}
        except:
            payload = {}

        response_data = {'status': 'ERROR', 'message': 'Unknown route'}
        status_code = 404

        conn = None
        try:
            conn = get_db_connection()

            if path == '/analytics/run':
                response_data = run_all_jobs()
                status_code = 200
            elif path == '/analytics/rfm':
                rfm = RFMAnalyzer(conn).compute_rfm()
                response_data = {'status': 'SUCCESS', 'count': len(rfm), 'data': rfm}
                status_code = 200
            elif path == '/analytics/forecast':
                fc = ForecastEngine(conn).generate_forecasts()
                response_data = {'status': 'SUCCESS', 'data': fc}
                status_code = 200
            elif path == '/analytics/anomalies':
                anoms = AnomalyDetector(conn).detect_all()
                response_data = {'status': 'SUCCESS', 'count': len(anoms), 'data': anoms}
                status_code = 200
            elif path == '/analytics/why-change':
                p1 = payload.get('period1')
                p2 = payload.get('period2')
                result = SalesAnalyticsEngine(conn).why_did_this_change(p1, p2)
                response_data = {'status': 'SUCCESS', 'data': result}
                status_code = 200
            elif path == '/analytics/recommendations':
                recs = RecommendationEngine(conn).generate_recommendations()
                response_data = {'status': 'SUCCESS', 'data': recs}
                status_code = 200
            elif path == '/analytics/import':
                imp_id = payload.get('import_id')
                f_path = payload.get('file_path')
                response_data = run_import(imp_id, f_path)
                status_code = 200 if response_data.get('success') else 400
        except Exception as e:
            response_data = {'status': 'ERROR', 'message': str(e)}
            status_code = 500
        finally:
            if conn:
                try:
                    conn.close()
                except:
                    pass

        self.send_response(status_code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(response_data).encode('utf-8'))

    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps({'status': 'HEALTHY', 'service': 'InsightFlow Python Analytics Engine'}).encode('utf-8'))

def main():
    parser = argparse.ArgumentParser(description="InsightFlow Python Analytics Engine")
    parser.add_argument('command', choices=['run-all', 'rfm', 'cohorts', 'pareto', 'forecast', 'anomalies', 'recommendations', 'why-change', 'import', 'serve'])
    parser.add_argument('--import-id', type=int, help="Import ID for ETL")
    parser.add_argument('--file', type=str, help="File path for import")
    parser.add_argument('--period1', type=str, help="Period 1 for Why-Change")
    parser.add_argument('--period2', type=str, help="Period 2 for Why-Change")
    parser.add_argument('--port', type=int, default=8000, help="Port for HTTP server")

    args = parser.parse_args()

    if args.command == 'run-all':
        res = run_all_jobs()
        print(json.dumps(res, indent=2))
    elif args.command == 'serve':
        server_address = ('127.0.0.1', args.port)
        httpd = HTTPServer(server_address, SimpleAnalyticsServer)
        print(f"InsightFlow Python Analytics Server listening on http://127.0.0.1:{args.port}...")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\nShutting down server.")
    elif args.command == 'import':
        if not args.import_id or not args.file:
            print(json.dumps({'error': '--import-id and --file are required for import'}))
            sys.exit(1)
        res = run_import(args.import_id, args.file)
        print(json.dumps(res, indent=2))
    elif args.command == 'why-change':
        conn = get_db_connection()
        res = SalesAnalyticsEngine(conn).why_did_this_change(args.period1, args.period2)
        conn.close()
        print(json.dumps(res, indent=2))
    else:
        conn = get_db_connection()
        if args.command == 'rfm':
            res = RFMAnalyzer(conn).compute_rfm()
        elif args.command == 'cohorts':
            res = CohortAnalyzer(conn).compute_cohorts()
        elif args.command == 'pareto':
            res = ParetoAnalyzer(conn).compute_product_pareto()
        elif args.command == 'forecast':
            res = ForecastEngine(conn).generate_forecasts()
        elif args.command == 'anomalies':
            res = AnomalyDetector(conn).detect_all()
        elif args.command == 'recommendations':
            res = RecommendationEngine(conn).generate_recommendations()
        conn.close()
        print(json.dumps({'count': len(res) if isinstance(res, list) else 1, 'data': res}, indent=2))

if __name__ == '__main__':
    main()
