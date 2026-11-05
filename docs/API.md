# InsightFlow REST API Documentation (v1)

Base URL: `/api/v1`

All responses follow a standard JSON envelope:

```json
{
  "success": true,
  "data": {},
  "message": null
}
```

---

## 1. Executive Dashboard

### `GET /api/v1/dashboard/summary`
Returns high-level commercial KPIs, monthly trends, regional share, and category profit breakdown.

**Optional Query Parameters:**
- `start_date` (YYYY-MM-DD)
- `end_date` (YYYY-MM-DD)

**Example Response:**
```json
{
  "success": true,
  "data": {
    "revenue": 88057383.94,
    "profit": 16486826.00,
    "orders": 1586,
    "customers": 220,
    "aov": 55521.68,
    "margin_pct": 18.72,
    "target_achievement_pct": 142.3,
    "monthly_trends": [
      { "ym": "2025-05", "revenue": 3412500.0, "profit": 650000.0, "orders": 68 }
    ],
    "regional_share": [
      { "region_name": "North America - Northeast", "revenue": 14250000.0 }
    ]
  },
  "message": null
}
```

---

### `GET /api/v1/dashboard/trends`
Returns the historical monthly metrics series.

---

## 2. Sales & Channels

### `GET /api/v1/sales`
Returns monthly sales, channel volume, product category profits, and sales rep quota leaderboard.

**Optional Query Parameters:**
- `start_date`
- `end_date`

---

## 3. Customers & RFM

### `GET /api/v1/customers`
Returns RFM segment aggregations, repeat purchase statistics, and the top 25 accounts.

### `GET /api/v1/analytics/rfm`
Returns all customer profiles with Recency, Frequency, Monetary spend, individual R-F-M scores (1-5), and segment classifications:
- Champions
- Loyal Customers
- Potential Loyalists
- New Customers
- At Risk
- Can't Lose Them
- Lost Customers

---

## 4. Products & Pareto

### `GET /api/v1/products`
Returns the product catalog enriched with units sold, gross margins, return rates, stock levels, and 4-quadrant strategic flags.

### `GET /api/v1/analytics/pareto`
Returns products ranked by cumulative revenue contribution along with their assigned Pareto Class (`A`, `B`, or `C`).

---

## 5. Predictive Analytics & Decisions

### `GET /api/v1/analytics/forecast`
Returns predictive forecast horizons (30, 60, 90 days) for revenue and order volume, including:
- `predicted_value`
- `lower_bound` (95% CI)
- `upper_bound` (95% CI)
- `method` ("Linear Trend Regression with Standard Error Bounds")

### `GET /api/v1/analytics/anomalies`
Returns statistical outliers identified by the rolling Z-Score / IQR engine:
- `metric_name`
- `anomaly_date`
- `actual_value`
- `expected_value`
- `deviation_pct`
- `z_score`
- `severity` (`LOW`, `MEDIUM`, `HIGH`, `CRITICAL`)
- `reason`

### `GET /api/v1/alerts`
Returns active operational and analytical alerts.

### `GET /api/v1/recommendations`
Returns evidence-based business recommendations with diagnosed problems, evidence, suggested actions, and priorities.

---

## 6. Observability

### `GET /api/v1/system/health`
Returns health status for database connectivity, PHP runtime version, and Python analytics subsystem connectivity.

