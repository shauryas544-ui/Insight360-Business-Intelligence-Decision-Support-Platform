# InsightFlow Architecture Specification

## 1. System Overview

**InsightFlow** is an enterprise-grade Sales & Business Intelligence (BI) Analytics and Decision Support Platform. The application is built upon a hybrid architecture:

- **PHP 8.2+ Backend & Web Application**: Handles authentication, Role-Based Access Control (RBAC), view rendering, REST API routing, session management, file uploads, data ingestion orchestration, and audit logging.
- **Python 3.11+ Analytics Engine**: Serves as the statistical computation powerhouse. Executes ETL, Data Quality scoring, RFM behavioral customer segmentation, cohort retention matrices, Pareto 80/20 distributions, multi-horizon forecasting, rolling Z-score anomaly detection, and dimensional driver decomposition ("Why Did This Change?").
- **Central Database (MySQL 8+ / SQLite 3 Dual Compatibility)**: Provides persistent storage for master entities, transactional orders, marketing telemetry, analytics aggregates, system alerts, and recommendations.

```
                         BUSINESS USERS
                               │
                               ▼
                    ┌──────────────────────┐
                    │ Web Frontend (SaaS)  │
                    │ Bootstrap 5 + JS     │
                    │ Chart.js Visuals     │
                    └──────────┬───────────┘
                               │ HTTPS / JSON
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

---

## 2. Layered Application Structure

InsightFlow enforces strict separation of concerns following a modern modular layered pattern:

1. **Presentation Layer (`app/Views/` & `public/assets/`)**:
   - Modern, data-dense SaaS user interface.
   - Reusable layout (`layouts/main.php`), interactive modals (e.g. *Why-Did-This-Change* driver drawer), responsive charts (Chart.js), and data tables.
2. **Controller Layer (`app/Controllers/`)**:
   - Thin controllers responsible for request validation, CSRF checks, invoking domain services, and returning view templates or JSON API responses.
3. **Service Layer (`app/Services/`)**:
   - Business logic encapsulation: `DashboardService`, `SalesAnalyticsService`, `CustomerAnalyticsService`, `ProductAnalyticsService`, `RegionalAnalyticsService`, `OperationsAnalyticsService`, `MarketingAnalyticsService`, `AlertService`, `RecommendationService`, `ImportService`, `ReportService`, and `PythonBridgeService`.
4. **Data Access / Repository Layer (`app/Helpers/Database.php`)**:
   - Centralized PDO connection handling, multi-driver dialect translation, parameterized prepared statements, and transaction management.
5. **Python Analytics Subsystem (`python/`)**:
   - Standalone analytical modules in `etl/`, `segmentation/`, `stats/`, `forecasting/`, `anomaly/`, and `recommendations/`.

---

## 3. Dual Engine Bridge

Communication between PHP and Python is designed to be resilient and non-blocking:

- **Synchronous CLI Execution (`PythonBridgeService::runCommand`)**:
  - PHP triggers Python CLI commands (e.g., `python main.py run-all`, `python main.py why-change --period1 2026-09 --period2 2026-10`).
  - Python streams structured JSON telemetry back to standard output.
- **Microservice / Job Mode (`python main.py serve --port 8000`)**:
  - An embedded lightweight HTTP daemon can be launched for high-volume environments to handle `POST /analytics/run` and `POST /analytics/why-change` over local REST sockets.
- **Database Persistence**:
  - Python directly populates analytical aggregate tables (`monthly_metrics`, `customer_segments`, `customer_cohorts`, `product_metrics`, `forecast_results`, `anomalies`, `alerts`, `recommendations`), allowing dashboards to load with instant sub-millisecond SQL queries.

---

## 4. Role-Based Access Control (RBAC)

InsightFlow implements granular role-based authorization:

| Role | Access Scope |
| :--- | :--- |
| **Admin** | Unrestricted access: user management, provisioning, dataset imports, audit logs, analytics job executions, system settings. |
| **Analyst** | Deep analytics workspace, RFM segment exploration, cohort heatmaps, Pareto 80/20, forecast inspection, CSV exports, data uploads. |
| **Manager** | Executive C-Suite dashboard, commercial KPIs, quota attainment leaderboard, active operational alerts, recommendations, printable reports. |

---

## 5. Security & Observability

- **CSRF Defense**: Cryptographically random per-session tokens validated on all mutation requests.
- **SQL Injection Prevention**: All queries execute via PDO parameterized prepared statements.
- **XSS Mitigation**: Strict HTML entity escaping via `htmlspecialchars` across all view outputs.
- **Password Security**: Strong bcrypt password hashing (`password_hash` with cost 10).
- **Audit Logging**: Every sensitive action (logins, imports, user creations, manual analytics runs) is immutably logged into the `audit_logs` table with user IDs, IP addresses, and JSON metadata.

