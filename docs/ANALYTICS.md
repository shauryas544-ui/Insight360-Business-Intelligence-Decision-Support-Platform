# InsightFlow Analytics Methodology & Governance

This document describes the mathematical and statistical algorithms powering InsightFlow.

---

## 1. Metric Governance & Definitions

To maintain single-source-of-truth analytical consistency across all modules:

| Metric | Business Definition | Mathematical Formula | Source Data |
| :--- | :--- | :--- | :--- |
| **Total Revenue** | Net billed order transactions | `SUM(orders.total_amount)` | `orders.total_amount` |
| **Gross Profit** | Commercial profit after product cost & discounts | `SUM(order_items.profit)` | `order_items.profit` |
| **Gross Margin %** | Proportion of revenue retained after product costs | `(Gross Profit / Revenue) * 100` | Calculated |
| **AOV** | Average dollar size per completed order | `Total Revenue / Total Completed Orders` | Calculated |
| **Return Rate %** | Proportion of ordered units refunded / returned | `(Units Returned / Units Sold) * 100` | `returns`, `order_items` |
| **Campaign ROI %**| Return on marketing capital invested | `((Attributed Revenue - Budget) / Budget) * 100` | `campaigns`, `campaign_results` |
| **CAC** | Cost to acquire an enterprise corporate customer | `Campaign Budget / Conversions` | `campaigns`, `campaign_results` |

---

## 2. RFM Customer Segmentation Engine

Customer transactions are aggregated to calculate:
- **Recency ($R$)**: Days elapsed from the customer's most recent order to the reference date.
- **Frequency ($F$)**: Total count of distinct orders placed.
- **Monetary ($M$)**: Cumulative historical gross revenue from the account.

### Quintile Scoring ($1 - 5$)
Values are partitioned into 20% percentiles (quintiles):
- Recency scores are inverted: shortest recency = 5, longest recency = 1.
- Frequency and Monetary scores: highest volume/spend = 5, lowest = 1.

### Segment Classification Matrix
Scores yield composite RFM codes (e.g. 555, 455, 121) classified into 7 operational segments:
1. **Champions**: $R \ge 4$ and $(F+M)/2 \ge 4$.
2. **Loyal Customers**: $R \ge 3$ and $(F+M)/2 \ge 3$.
3. **Potential Loyalists**: $R \ge 3$ and $(F+M)/2 \le 3$.
4. **New Customers**: $R \ge 4$ and $(F+M)/2 \le 2$.
5. **Can't Lose Them**: $R \le 2$ and $(F+M)/2 \ge 4$.
6. **At Risk**: $R \le 2$ and $(F+M)/2 \ge 2$.
7. **Lost Customers**: Remaining lower combinations.

---

## 3. Customer Cohort Retention Matrix

1. **Cohort Assignment**: Customers are tagged by the calendar month of their first purchase ($C_0$).
2. **Period Tracking ($P_n$)**: Subsequent orders are bucketed by month distance $P_n = (Year_t - Year_0) \times 12 + (Month_t - Month_0)$.
3. **Retention Rate**:
   $$\text{Retention Rate}(C_0, P_n) = \frac{\text{Active Accounts in } P_n}{\text{Total Accounts in } C_0} \times 100$$
4. Displayed as a shaded heatmap matrix in the analyst and customer workspaces.

---

## 4. Pareto 80/20 & 4-Quadrant Profitability

### Pareto Analysis
- Products sorted descending by revenue contribution.
- Cumulative revenue share percentage calculated:
  - **Class A**: Accounts for first $80\%$ of cumulative revenue (Core business drivers).
  - **Class B**: Accounts for next $15\%$ (Mid-tier volume contributors).
  - **Class C**: Accounts for the remaining $5\%$ (Long-tail catalog assets).

### 4-Quadrant Strategic Categorization
- Products mapped against catalog average revenue and margin benchmarks (45%):
  - **Stars**: High Revenue + High Margin.
  - **Volume Drivers / Risk**: High Revenue + Low Margin.
  - **Niche Opportunities**: Low Revenue + High Margin.
  - **Underperforming**: Low Revenue + Low Margin.

---

## 5. Multi-Horizon Predictive Forecasting

Forecasts project revenue and orders across 30, 60, and 90-day horizons:
- Model: Linear Trend Regression with Standard Error Bounds.
- Linear relationship: $\hat{y}_t = \alpha + \beta \cdot t$.
- Residual Standard Error ($SE$):
  $$SE = \sqrt{\frac{\sum (y_t - \hat{y}_t)^2}{n - 2}}$$
- 95% Confidence Interval ($CI_{95}$):
  $$\hat{y}_{future} \pm 1.96 \cdot SE \cdot \sqrt{1 + \frac{1}{n} + \frac{(t_{future} - \bar{t})^2}{\sum (t_i - \bar{t})^2}}$$

---

## 6. Statistical Anomaly Detection

- Metrics monitored across rolling historical windows (Monthly Revenue, Product Return Rates).
- Z-score computation:
  $$Z = \frac{x - \mu}{\sigma}$$
- Outlier Severity Classification:
  - $|Z| \ge 2.5$: **CRITICAL** (automatically creates high-priority operational alert).
  - $2.0 \le |Z| < 2.5$: **HIGH**.
  - $1.75 \le |Z| < 2.0$: **MEDIUM**.

---

## 7. Signature "Why Did This Change?" Engine

Decomposes the variance between Base Month ($M_1$) and Comparison Month ($M_2$):
1. **Delta Calculation**: $\Delta Revenue = Revenue(M_2) - Revenue(M_1)$.
2. **Dimensional Breakdown**: Revenue delta computed by:
   - Product Category ($\Delta Category_k$)
   - Sales Territory / Region ($\Delta Region_j$)
   - Customer Segment ($\Delta Segment_i$)
3. **Contribution Percentage**:
   $$Contribution\% = \frac{\Delta DimensionalValue}{|\Delta Revenue|} \times 100$$
4. Ranks top positive and negative drivers and outputs an automated narrative explanation with waterfall visualizations.

---

## 8. Data Quality Engine Scoring

Uploaded datasets are graded on a 0-100% scale across 4 core dimensions:
- **Completeness (30% weight)**: % of required columns populated with non-null values.
- **Uniqueness (20% weight)**: % of records free from duplicate transaction keys.
- **Validity (30% weight)**: % of dates conforming to ISO patterns and numeric values $> 0$.
- **Consistency (20% weight)**: Referential integrity against master records.
- Overall Score:
  $$\text{Score} = (0.3 \cdot C) + (0.2 \cdot U) + (0.3 \cdot V) + (0.2 \cdot Cons)$$
Datasets scoring below 50.0% are rejected to protect analytical integrity.

