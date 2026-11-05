import re
from datetime import datetime

class DataValidator:
    REQUIRED_COLUMNS = ['order_number', 'order_date', 'customer_code', 'sku', 'quantity', 'unit_price']

    @classmethod
    def validate_dataset(cls, rows):
        if not rows:
            return {
                'quality_score': 0.0,
                'total_rows': 0,
                'valid_rows': 0,
                'issues': [{'type': 'EMPTY_DATASET', 'message': 'The uploaded file contains no data rows.'}],
                'metrics': {'completeness': 0.0, 'uniqueness': 0.0, 'validity': 0.0, 'consistency': 0.0}
            }

        headers = list(rows[0].keys())
        missing_headers = [col for col in cls.REQUIRED_COLUMNS if col not in headers]
        if missing_headers:
            return {
                'quality_score': 0.0,
                'total_rows': len(rows),
                'valid_rows': 0,
                'issues': [{'type': 'MISSING_COLUMNS', 'message': f"Missing required columns: {', '.join(missing_headers)}"}],
                'metrics': {'completeness': 0.0, 'uniqueness': 100.0, 'validity': 0.0, 'consistency': 0.0}
            }

        total_rows = len(rows)
        missing_count = 0
        duplicate_count = 0
        invalid_date_count = 0
        invalid_numeric_count = 0
        seen_keys = set()
        issues = []

        date_patterns = ['%Y-%m-%d', '%m/%d/%Y', '%d/%m/%Y', '%Y/%m/%d']

        for idx, row in enumerate(rows, start=1):
            # Check completeness
            has_empty = any(v is None or str(v).strip() == '' for k, v in row.items() if k in cls.REQUIRED_COLUMNS)
            if has_empty:
                missing_count += 1

            # Check uniqueness
            unique_key = (str(row.get('order_number')).strip(), str(row.get('sku')).strip())
            if unique_key in seen_keys:
                duplicate_count += 1
                if len(issues) < 20:
                    issues.append({'row': idx, 'type': 'DUPLICATE', 'message': f"Duplicate order/SKU pair: {unique_key}"})
            else:
                seen_keys.add(unique_key)

            # Check date validity
            raw_date = str(row.get('order_date', '')).strip()
            date_valid = False
            for fmt in date_patterns:
                try:
                    datetime.strptime(raw_date, fmt)
                    date_valid = True
                    break
                except ValueError:
                    pass
            if not date_valid:
                invalid_date_count += 1
                if len(issues) < 20:
                    issues.append({'row': idx, 'type': 'INVALID_DATE', 'message': f"Invalid date format: {raw_date}"})

            # Check numeric validity
            try:
                qty = float(row.get('quantity', 0))
                price = float(str(row.get('unit_price', 0)).replace('$', '').replace(',', ''))
                if qty <= 0 or price < 0:
                    invalid_numeric_count += 1
                    if len(issues) < 20:
                        issues.append({'row': idx, 'type': 'INVALID_NUMERIC', 'message': f"Negative or zero quantity/price: qty={qty}, price={price}"})
            except (ValueError, TypeError):
                invalid_numeric_count += 1

        # Scores calculation (0 - 100)
        completeness = max(0.0, round((1.0 - (missing_count / total_rows)) * 100.0, 2))
        uniqueness = max(0.0, round((1.0 - (duplicate_count / total_rows)) * 100.0, 2))
        valid_dates_pct = max(0.0, round((1.0 - (invalid_date_count / total_rows)) * 100.0, 2))
        valid_nums_pct = max(0.0, round((1.0 - (invalid_numeric_count / total_rows)) * 100.0, 2))
        validity = round((valid_dates_pct + valid_nums_pct) / 2.0, 2)
        consistency = round(min(completeness, validity), 2)

        overall_score = round((completeness * 0.3) + (uniqueness * 0.2) + (validity * 0.3) + (consistency * 0.2), 2)

        return {
            'quality_score': overall_score,
            'total_rows': total_rows,
            'missing_count': missing_count,
            'duplicate_count': duplicate_count,
            'invalid_date_count': invalid_date_count,
            'invalid_numeric_count': invalid_numeric_count,
            'issues': issues,
            'metrics': {
                'completeness': completeness,
                'uniqueness': uniqueness,
                'validity': validity,
                'consistency': consistency
            }
        }

