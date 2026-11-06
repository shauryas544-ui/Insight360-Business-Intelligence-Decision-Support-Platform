# InsightFlow — Sales & Business Intelligence Analytics Platform

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Render-46E3B7?style=for-the-badge&logo=render&logoColor=white)](https://insightflow-analytics-1681.onrender.com)
[![Deployment Status](https://img.shields.io/badge/Deployment-Live-success?style=for-the-badge)](https://insightflow-analytics-1681.onrender.com)

[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777bb4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Python 3.11+](https://img.shields.io/badge/Python-3.11%2B-3776ab?style=flat&logo=python&logoColor=white)](https://www.python.org/)
[![Database](https://img.shields.io/badge/Database-MySQL%208+%20%7C%20SQLite%203-00618a?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)

> **InsightFlow** is an enterprise-grade Business Intelligence (BI), Data Analytics, and Decision Support Platform. It transforms raw transactional data into measurable business KPIs, advanced behavioral segmentations, predictive forecasts, operational anomaly alerts, and evidence-based strategic recommendations.

---

### 🌐 Live Production Demo
* **Live App URL:** [https://insightflow-analytics-1681.onrender.com](https://insightflow-analytics-1681.onrender.com)
* **Demo Access Credentials:**
  * **Executive (CEO/Director):** `ceo@insightflow.com` / `Password123!`
  * **Senior Data Analyst:** `analyst@insightflow.com` / `Password123!`
  * **Operations Manager:** `ops@insightflow.com` / `Password123!`

---

## 1. Core Architecture & Design Principles

InsightFlow is built around strict enterprise software engineering standards:

```
                         BUSINESS USERS
                               │
                               ▼
                    ┌──────────────────────┐
                    │ Web Frontend (SaaS)  │
                    │ Bootstrap 5 + JS     │
                    │ Chart.js Visuals     │
                    └──────────┬───────────┘
                               │ HTTPS / REST JSON
                               ▼
                    ┌──────────────────────┐
                    │     PHP Backend      │
                    │ MVC + REST API v1    │
                    │ Auth & Session Guard │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴──────────┐
                    ▼                     ▼
             ┌─────────────┐       ┌─────────────┐
             │ MySQL / DB  │◄─────►│   Python    │
             │ Persistence │  SQL  │  Analytics  │
             └─────────────┘       │   Engine    │
                    ▲              └─────────────┘
                    │                     │
                    └─────────────────────┘
```

1. **No Basic CRUD**: The platform's primary product is its analytical intelligence and decision-support layer.
2. **PHP as the Primary Web Application**: Manages authentication, RBAC, dashboards, file uploads, ingestion orchestration, and report generation.
3. **Python as the Analytics Engine**: Performs data cleansing, RFM segmentation, cohort retention matrices, Pareto 80/20 analysis, 30/60/90-day forecasting, and rolling Z-score anomaly detection.
4. **Dual Database Compatibility**: Zero-configuration local SQLite for instant out-of-the-box evaluation and standard MySQL 8.0+ for high-concurrency production deployments.
5. **Never Invent Business Results**: All KPI metrics, anomaly flags, and recommendations are mathematically derived from verified stored transaction data.
6. **No "Fake AI"**: Recommendations are rule-governed and accompanied by observable mathematical evidence (customer tiers, dollar amounts, return rates).

---

## 2. Key Modules & Capabilities

- **Executive & Analyst Dashboards**: High-level C-Suite KPIs (Revenue, Profit Margin, AOV, Quota Attainment) alongside multi-dimensional exploratory cuts.
- **Signature "Why Did This Change?" Engine**: Automatically decomposes revenue and order variances between two periods across categories, territories, and customer segments with waterfall driver charts.
- **Customer Analytics & RFM Segmentation**: Dynamically scores accounts across Recency, Frequency, and Monetary spend to categorize customers into 7 tiers: *Champions, Loyal Customers, Potential Loyalists, New Customers, At Risk, Can't Lose Them,* and *Lost Customers*.
- **Cohort Retention Matrix**: Visual heatmap tracking account retention rates and revenue cohorts month-over-month.
- **Product Profitability & Pareto 80/20**: Ranks catalog items into Class A (core 80%), Class B (next 15%), and Class C (tail 5%), plus a strategic 4-Quadrant Matrix (*Stars, Volume Drivers, Niche, Underperforming*).
- **Territory & Regional Matrix**: Monitors global sales territories, margins, and quota achievement rates.
- **Marketing & CAC / ROI Attribution**: Tracks conversion rates, customer acquisition costs, and campaign ROI.
- **Operations, Quality & Inventory**: Analyzes return root causes and tracks warehouse safety stock levels.
- **Predictive Forecasting**: Projects 30, 60, and 90-day revenue and order volumes with 95% confidence intervals.
- **Statistical Anomaly Detection**: Employs rolling Z-scores and IQR to flag unusual revenue drops or return rate surges.
- **Operational Alerts Center**: Severity-tiered notifications (`CRITICAL`, `HIGH`, `MEDIUM`, `LOW`) with resolve/dismiss workflows.
- **Evidence-Based Recommendation Engine**: Actionable business strategies with stated problem, evidence, suggested action, priority, and affected segment.
- **Data Import & Quality Engine**: CSV upload with automatic scoring of Completeness, Uniqueness, Validity, and Consistency. Datasets scoring below 50% are automatically rejected.
- **Executive Reporting & Exporting**: Printable PDF-ready executive briefings and direct raw CSV exports.
- **System Administration & Immutable Audit Logs**: Granular user provisioning and security event tracking.

---

## 3. Demo Credentials

The platform includes seeded demo accounts with pre-configured RBAC roles:

| Role | Email | Password | Privileges |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@insightflow.io` | `AdminPassword123!` | Full administrative control, user provisioning, imports, and audit logs. |
| **Senior Analyst** | `analyst@insightflow.io` | `AnalystPassword123!` | Deep analytical workspace, RFM, cohorts, forecasting, and data explorer. |
| **Business Manager** | `manager@insightflow.io` | `ManagerPassword123!` | Executive dashboard, KPIs, alerts, recommendations, and report downloads. |

*Quick-fill demo buttons are provided directly on the login screen for 1-click access.*

---

## 4. Quickstart Guide

### Step 1: Clone or Open Workspace
Ensure you have **PHP 8.2+** and **Python 3.11+** installed.

### Step 2: Configure Environment
Copy `.env.example` to `.env` (already pre-configured for instant SQLite execution):

```bash
cp .env.example .env
```

### Step 3: Run Database Migrations
Create the database tables:

```bash
php database/migrate.php
```

### Step 4: Seed Initial Business Data
Populate 18 months of realistic business orders, products, categories, customers, and campaigns:

```bash
php database/seed.php
```

### Step 5: Execute Initial Analytics Run
Trigger the Python Analytics Engine to compute RFM segments, cohorts, Pareto classes, forecasts, anomalies, alerts, and recommendations:

```bash
python python/main.py run-all
```

### Step 6: Start the Web Application
Launch the built-in PHP development server:

```bash
php -S 127.0.0.1:8080 -t public
```

Open your browser and navigate to:
[http://127.0.0.1:8080](http://127.0.0.1:8080)

Sign in using `admin@insightflow.io` / `AdminPassword123!`.

---

## 5. Running Automated Tests

InsightFlow includes comprehensive unit and integration test suites:

```bash
# Run PHP Integration Test Suite (Database, Services, KPIs, Drivers)
php tests/php/IntegrationTest.php

# Run Authenticated Route & View Render Tests
php tests/php/test_router.php

# Run Python Analytics Engine Unit Tests (ETL, RFM, Cohorts, Forecasting, Anomalies)
python tests/python/test_analytics.py
```

---

## 6. Project Structure

```
insightflow/
├── app/
│   ├── Controllers/         # Thin HTTP & REST API controllers
│   ├── Helpers/             # Database PDO manager, Request, Response, Router, Env, Session
│   ├── Middleware/          # AuthMiddleware & RoleMiddleware
│   ├── Services/            # Core business & analytics domain services
│   └── Views/               # Blade-style PHP views & layout
├── config/                  # App & Database configuration
├── database/
│   ├── schema.sql           # MySQL 8.0+ DDL
│   ├── schema_sqlite.sql    # SQLite 3 DDL
│   ├── migrate.php          # Migration runner
│   ├── seed.php             # Seeder entry point
│   └── seeders/             # DatabaseSeeder with 18-month business scenario generator
├── docs/
│   ├── ARCHITECTURE.md      # Detailed system architecture
│   ├── API.md               # REST API v1 reference
│   ├── DATABASE.md          # Database schema & entity relationships
│   ├── ANALYTICS.md         # Mathematical definitions & algorithms
│   └── DEPLOYMENT.md        # Production Linux/Nginx/Apache deployment guide
├── public/
│   ├── index.php            # Web entry point
│   ├── .htaccess            # Apache URL rewrite configuration
│   └── assets/              # CSS & JS platform assets
├── python/
│   ├── main.py              # CLI & HTTP analytics runner
│   ├── config.py            # Python DB connector & environment loader
│   ├── etl/                 # Data loader, cleaner, validator, transformer
│   ├── analytics/           # Sales & Why-Did-This-Change engine
│   ├── segmentation/        # RFM customer segmentation engine
│   ├── stats/               # Cohort retention matrix & Pareto 80/20
│   ├── forecasting/         # 30/60/90-day predictive horizons
│   ├── anomaly/             # Rolling Z-score anomaly detector
│   └── recommendations/     # Evidence-based business recommendations generator
├── storage/                 # Database, logs, temporary files, and report exports
├── tests/
│   ├── php/                 # PHP integration & routing tests
│   └── python/              # Python analytics unit tests
├── .env.example             # Template environment variables
├── composer.json            # Composer PSR-4 autoloader specification
└── requirements.txt         # Optional Python dependencies
```

---

## 7. Production Deployment

For production deployments with Nginx/Apache, MySQL 8+, and automated nightly cron jobs, refer to the detailed [Deployment Guide](docs/DEPLOYMENT.md).

