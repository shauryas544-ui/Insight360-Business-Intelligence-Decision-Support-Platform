# InsightFlow Database Architecture & Data Dictionary

InsightFlow supports both **MySQL 8.0+** (enterprise/production) and **SQLite 3** (local / sandbox / test automation).

---

## 1. Entity-Relationship Overview

```
categories (1) ───< products (M) ───< order_items (M)
                                            │
                                            ▼
regions (1) ───< customers (M) ───< orders (1)
       │                              │
       └─────< salespersons (M) ─────<┘
                                      │
                                      ▼
                               returns (M)
```

---

## 2. Table Specifications

### Identity & Security
- `roles`: `id`, `name`, `display_name`, `description`, `created_at`
- `permissions`: `id`, `name`, `slug`, `description`, `created_at`
- `role_permissions`: `role_id`, `permission_id`
- `users`: `id`, `name`, `email`, `password_hash`, `role_id`, `status`, `last_login_at`, `created_at`, `updated_at`
- `audit_logs`: `id`, `user_id`, `action`, `entity_type`, `entity_id`, `status`, `metadata`, `created_at`

### Master Data
- `categories`: `id`, `name`, `code`, `description`, `created_at`
- `regions`: `id`, `name`, `country`, `state`, `city`, `code`, `created_at`
- `salespersons`: `id`, `name`, `email`, `region_id`, `target_quarterly`, `active`, `created_at`
- `products`: `id`, `sku`, `product_name`, `category_id`, `cost_price`, `selling_price`, `active`, `created_at`, `updated_at`
- `customers`: `id`, `customer_code`, `name`, `email`, `phone`, `city`, `state`, `region_id`, `customer_since`, `segment`, `created_at`, `updated_at`

### Transactional & Operations
- `orders`: `id`, `order_number`, `customer_id`, `salesperson_id`, `region_id`, `order_date`, `channel`, `status`, `subtotal`, `discount`, `tax`, `total_amount`, `created_at`, `updated_at`
- `order_items`: `id`, `order_id`, `product_id`, `quantity`, `unit_price`, `discount`, `cost`, `profit`
- `returns`: `id`, `order_id`, `order_item_id`, `return_date`, `reason`, `refund_amount`, `status`, `created_at`
- `inventory`: `id`, `product_id`, `current_stock`, `safety_stock`, `reorder_level`, `last_restocked_at`

### Marketing & Target Goals
- `campaigns`: `id`, `name`, `channel`, `budget`, `start_date`, `end_date`, `target_audience`, `status`, `created_at`
- `campaign_results`: `id`, `campaign_id`, `impressions`, `clicks`, `leads`, `conversions`, `revenue_generated`, `recorded_date`
- `targets`: `id`, `year`, `month`, `dimension_type`, `dimension_id`, `target_revenue`, `target_profit`, `target_orders`, `created_at`

### Ingestion & Data Quality
- `data_imports`: `id`, `filename`, `file_type`, `file_size`, `total_rows`, `imported_rows`, `failed_rows`, `status`, `quality_score`, `error_log`, `imported_by`, `created_at`
- `data_quality_logs`: `id`, `import_id`, `metric_name`, `score`, `issue_count`, `details`, `created_at`

### Analytics Cache & Decision Support
- `customer_segments`: `id`, `customer_id`, `recency_days`, `frequency`, `monetary`, `r_score`, `f_score`, `m_score`, `rfm_score`, `segment_name`, `calculated_at`
- `customer_cohorts`: `id`, `cohort_month`, `period_month`, `active_customers`, `retention_rate`, `cohort_revenue`, `calculated_at`
- `product_metrics`: `id`, `product_id`, `period_month`, `units_sold`, `revenue`, `profit`, `margin_pct`, `return_rate`, `pareto_class`, `calculated_at`
- `regional_metrics`: `id`, `region_id`, `period_month`, `revenue`, `profit`, `order_count`, `target_achievement_pct`, `calculated_at`
- `monthly_metrics`: `id`, `year_month`, `revenue`, `profit`, `order_count`, `customer_count`, `aov`, `retention_rate`, `calculated_at`
- `forecast_results`: `id`, `metric_name`, `forecast_date`, `horizon_days`, `predicted_value`, `lower_bound`, `upper_bound`, `confidence_level`, `method`, `created_at`
- `anomalies`: `id`, `metric_name`, `anomaly_date`, `actual_value`, `expected_value`, `deviation_pct`, `z_score`, `severity`, `dimension`, `reason`, `status`, `created_at`
- `alerts`: `id`, `type`, `title`, `severity`, `metric`, `actual_value`, `expected_value`, `reason`, `recommendation`, `status`, `created_at`
- `recommendations`: `id`, `category`, `title`, `problem`, `evidence`, `suggested_action`, `priority`, `affected_segment`, `status`, `created_at`

---

## 3. Query Indexes

Optimized indices are configured for high query throughput:
- `orders`: `(order_date)`, `(customer_id)`, `(region_id)`, `(channel)`
- `order_items`: `(order_id)`, `(product_id)`
- `products`: `(category_id)`, `(sku)`
- `customers`: `(region_id)`, `(customer_since)`, `(segment)`
- `audit_logs`: `(user_id)`, `(action)`, `(created_at)`

