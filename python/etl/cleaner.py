import csv
from datetime import datetime

class DataCleaner:
    DATE_FORMATS = ['%Y-%m-%d', '%m/%d/%Y', '%d/%m/%Y', '%Y/%m/%d', '%m-%d-%Y']

    @classmethod
    def clean_row(cls, row):
        cleaned = {}
        for k, v in row.items():
            cleaned[k.strip().lower()] = str(v).strip() if v is not None else ''

        # Normalize date
        raw_date = cleaned.get('order_date', '')
        normalized_date = cls.normalize_date(raw_date)
        if normalized_date:
            cleaned['order_date'] = normalized_date

        # Clean numeric
        cleaned['quantity'] = cls.clean_int(cleaned.get('quantity', '1'), default=1)
        cleaned['unit_price'] = cls.clean_float(cleaned.get('unit_price', '0.0'))
        cleaned['discount'] = cls.clean_float(cleaned.get('discount', '0.0'))

        return cleaned

    @classmethod
    def normalize_date(cls, date_str):
        if not date_str:
            return None
        for fmt in cls.DATE_FORMATS:
            try:
                dt = datetime.strptime(date_str, fmt)
                return dt.strftime('%Y-%m-%d')
            except ValueError:
                pass
        return date_str

    @classmethod
    def clean_float(cls, val):
        if isinstance(val, (int, float)):
            return float(val)
        cleaned = str(val).replace('$', '').replace(',', '').strip()
        try:
            return float(cleaned)
        except ValueError:
            return 0.0

    @classmethod
    def clean_int(cls, val, default=0):
        try:
            return int(float(str(val).replace(',', '').strip()))
        except (ValueError, TypeError):
            return default

