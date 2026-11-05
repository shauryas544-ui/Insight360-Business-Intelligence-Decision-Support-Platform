-- InsightFlow - Enterprise Sales & Business Intelligence Analytics Platform
-- SQLite 3 Schema Definition

PRAGMA foreign_keys = ON;

-- 1. Identity & RBAC
CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    display_name TEXT NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role_id INTEGER NOT NULL,
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    last_login_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL,
    action TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    entity_id INTEGER NULL,
    status TEXT DEFAULT 'SUCCESS',
    metadata TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_logs (user_id);
CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_logs (action);

-- 2. Master Data
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    code TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS regions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    country TEXT DEFAULT 'United States',
    state TEXT NOT NULL,
    city TEXT NOT NULL,
    code TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS salespersons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    region_id INTEGER NOT NULL,
    target_quarterly NUMERIC DEFAULT 0.00,
    active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sku TEXT NOT NULL UNIQUE,
    product_name TEXT NOT NULL,
    category_id INTEGER NOT NULL,
    cost_price NUMERIC NOT NULL,
    selling_price NUMERIC NOT NULL,
    active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    region_id INTEGER NOT NULL,
    customer_since DATE NOT NULL,
    segment TEXT DEFAULT 'New Customers',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

-- 3. Transactions
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_number TEXT NOT NULL UNIQUE,
    customer_id INTEGER NOT NULL,
    salesperson_id INTEGER NULL,
    region_id INTEGER NOT NULL,
    order_date DATE NOT NULL,
    channel TEXT DEFAULT 'Direct',
    status TEXT DEFAULT 'Completed',
    subtotal NUMERIC NOT NULL,
    discount NUMERIC DEFAULT 0.00,
    tax NUMERIC DEFAULT 0.00,
    total_amount NUMERIC NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (salesperson_id) REFERENCES salespersons(id),
    FOREIGN KEY (region_id) REFERENCES regions(id)
);
CREATE INDEX IF NOT EXISTS idx_order_date ON orders (order_date);
CREATE INDEX IF NOT EXISTS idx_order_cust ON orders (customer_id);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price NUMERIC NOT NULL,
    discount NUMERIC DEFAULT 0.00,
    cost NUMERIC NOT NULL,
    profit NUMERIC NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS returns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    order_item_id INTEGER NULL,
    return_date DATE NOT NULL,
    reason TEXT NOT NULL,
    refund_amount NUMERIC NOT NULL,
    status TEXT DEFAULT 'Processed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id)
);

CREATE TABLE IF NOT EXISTS inventory (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL UNIQUE,
    current_stock INTEGER NOT NULL DEFAULT 0,
    safety_stock INTEGER NOT NULL DEFAULT 50,
    reorder_level INTEGER NOT NULL DEFAULT 100,
    last_restocked_at DATETIME NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 4. Marketing & Targets
CREATE TABLE IF NOT EXISTS campaigns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    channel TEXT NOT NULL,
    budget NUMERIC NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    target_audience TEXT,
    status TEXT DEFAULT 'Active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS campaign_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    impressions INTEGER DEFAULT 0,
    clicks INTEGER DEFAULT 0,
    leads INTEGER DEFAULT 0,
    conversions INTEGER DEFAULT 0,
    revenue_generated NUMERIC DEFAULT 0.00,
    recorded_date DATE NOT NULL,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS targets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    month INTEGER NOT NULL,
    dimension_type TEXT DEFAULT 'overall',
    dimension_id INTEGER NULL,
    target_revenue NUMERIC NOT NULL,
    target_profit NUMERIC NOT NULL,
    target_orders INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Data Imports & Quality Logs
CREATE TABLE IF NOT EXISTS data_imports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    file_type TEXT NOT NULL,
    file_size INTEGER NOT NULL,
    total_rows INTEGER DEFAULT 0,
    imported_rows INTEGER DEFAULT 0,
    failed_rows INTEGER DEFAULT 0,
    status TEXT DEFAULT 'PENDING',
    quality_score NUMERIC DEFAULT 100.00,
    error_log TEXT NULL,
    imported_by INTEGER NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (imported_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS data_quality_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    import_id INTEGER NOT NULL,
    metric_name TEXT NOT NULL,
    score NUMERIC NOT NULL,
    issue_count INTEGER DEFAULT 0,
    details TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_id) REFERENCES data_imports(id) ON DELETE CASCADE
);

-- 6. Analytics Results & Decision Support
CREATE TABLE IF NOT EXISTS customer_segments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL,
    recency_days INTEGER NOT NULL,
    frequency INTEGER NOT NULL,
    monetary NUMERIC NOT NULL,
    r_score INTEGER NOT NULL,
    f_score INTEGER NOT NULL,
    m_score INTEGER NOT NULL,
    rfm_score TEXT NOT NULL,
    segment_name TEXT NOT NULL,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS customer_cohorts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cohort_month TEXT NOT NULL,
    period_month INTEGER NOT NULL,
    active_customers INTEGER NOT NULL,
    retention_rate NUMERIC NOT NULL,
    cohort_revenue NUMERIC DEFAULT 0.00,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cohort_month, period_month)
);

CREATE TABLE IF NOT EXISTS product_metrics (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    period_month TEXT NOT NULL,
    units_sold INTEGER DEFAULT 0,
    revenue NUMERIC DEFAULT 0.00,
    profit NUMERIC DEFAULT 0.00,
    margin_pct NUMERIC DEFAULT 0.00,
    return_rate NUMERIC DEFAULT 0.00,
    pareto_class TEXT DEFAULT 'B',
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS regional_metrics (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    region_id INTEGER NOT NULL,
    period_month TEXT NOT NULL,
    revenue NUMERIC DEFAULT 0.00,
    profit NUMERIC DEFAULT 0.00,
    order_count INTEGER DEFAULT 0,
    target_achievement_pct NUMERIC DEFAULT 0.00,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS monthly_metrics (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year_month TEXT NOT NULL UNIQUE,
    revenue NUMERIC NOT NULL,
    profit NUMERIC NOT NULL,
    order_count INTEGER NOT NULL,
    customer_count INTEGER NOT NULL,
    aov NUMERIC NOT NULL,
    retention_rate NUMERIC DEFAULT 0.00,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS forecast_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    metric_name TEXT NOT NULL,
    forecast_date DATE NOT NULL,
    horizon_days INTEGER NOT NULL,
    predicted_value NUMERIC NOT NULL,
    lower_bound NUMERIC NOT NULL,
    upper_bound NUMERIC NOT NULL,
    confidence_level NUMERIC DEFAULT 95.00,
    method TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS anomalies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    metric_name TEXT NOT NULL,
    anomaly_date DATE NOT NULL,
    actual_value NUMERIC NOT NULL,
    expected_value NUMERIC NOT NULL,
    deviation_pct NUMERIC NOT NULL,
    z_score NUMERIC NOT NULL,
    severity TEXT DEFAULT 'MEDIUM',
    dimension TEXT DEFAULT 'Overall',
    reason TEXT NOT NULL,
    status TEXT DEFAULT 'OPEN',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS alerts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    title TEXT NOT NULL,
    severity TEXT DEFAULT 'MEDIUM',
    metric TEXT NOT NULL,
    actual_value TEXT NOT NULL,
    expected_value TEXT NOT NULL,
    reason TEXT NOT NULL,
    recommendation TEXT NOT NULL,
    status TEXT DEFAULT 'ACTIVE',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recommendations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL,
    title TEXT NOT NULL,
    problem TEXT NOT NULL,
    evidence TEXT NOT NULL,
    suggested_action TEXT NOT NULL,
    priority TEXT DEFAULT 'MEDIUM',
    affected_segment TEXT NOT NULL,
    status TEXT DEFAULT 'OPEN',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

