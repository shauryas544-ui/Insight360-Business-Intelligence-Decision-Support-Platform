import csv
from pathlib import Path

class DataLoader:
    @classmethod
    def load_file(cls, file_path):
        path = Path(file_path)
        if not path.exists():
            raise FileNotFoundError(f"File not found: {file_path}")

        encodings = ['utf-8-sig', 'utf-8', 'latin-1', 'cp1252']
        content = None
        for enc in encodings:
            try:
                with open(path, 'r', encoding=enc) as f:
                    content = f.read()
                    break
            except UnicodeDecodeError:
                continue

        if content is None:
            raise ValueError(f"Unable to decode file: {file_path}")

        lines = [line for line in content.splitlines() if line.strip()]
        if not lines:
            return []

        # Detect delimiter (comma vs tab vs semicolon)
        first_line = lines[0]
        delimiter = ','
        if '\t' in first_line and first_line.count('\t') > first_line.count(','):
            delimiter = '\t'
        elif ';' in first_line and first_line.count(';') > first_line.count(','):
            delimiter = ';'

        reader = csv.DictReader(lines, delimiter=delimiter)
        rows = []
        for r in reader:
            # normalize keys to lowercase trimmed
            normalized_row = {k.strip().lower(): v for k, v in r.items() if k is not None}
            rows.append(normalized_row)

        return rows

