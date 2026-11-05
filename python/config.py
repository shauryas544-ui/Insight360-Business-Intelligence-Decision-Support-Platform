import os
import sys
import sqlite3
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent

def load_env():
    env_file = BASE_DIR / '.env'
    env_vars = {}
    if env_file.exists():
        with open(env_file, 'r', encoding='utf-8') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    k, v = line.split('=', 1)
                    k = k.strip()
                    v = v.strip().strip('"').strip("'")
                    env_vars[k] = v
                    os.environ.setdefault(k, v)
    return env_vars

ENV = load_env()

DB_CONNECTION = ENV.get('DB_CONNECTION', 'sqlite')
DB_SQLITE_PATH = BASE_DIR / ENV.get('DB_SQLITE_PATH', 'storage/database/insightflow.sqlite')
DB_HOST = ENV.get('DB_HOST', '127.0.0.1')
DB_PORT = int(ENV.get('DB_PORT', 3306))
DB_DATABASE = ENV.get('DB_DATABASE', 'insightflow')
DB_USERNAME = ENV.get('DB_USERNAME', 'root')
DB_PASSWORD = ENV.get('DB_PASSWORD', '')

def get_db_connection():
    if DB_CONNECTION == 'sqlite':
        DB_SQLITE_PATH.parent.mkdir(parents=True, exist_ok=True)
        conn = sqlite3.connect(str(DB_SQLITE_PATH))
        conn.row_factory = sqlite3.Row
        conn.execute("PRAGMA foreign_keys = ON;")
        return conn
    else:
        try:
            import pymysql
            conn = pymysql.connect(
                host=DB_HOST,
                port=DB_PORT,
                user=DB_USERNAME,
                password=DB_PASSWORD,
                database=DB_DATABASE,
                cursorclass=pymysql.cursors.DictCursor
            )
            return conn
        except ImportError:
            # Fallback to sqlite if pymysql is not installed
            conn = sqlite3.connect(str(DB_SQLITE_PATH))
            conn.row_factory = sqlite3.Row
            return conn

