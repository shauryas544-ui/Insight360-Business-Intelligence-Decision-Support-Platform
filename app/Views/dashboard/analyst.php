<!-- Analyst Workspace View -->

<div class="card-chart mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-funnel text-primary me-2"></i>Multi-Dimensional Exploration Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="/dashboard" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Date Range Start</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="2025-05-01">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Date Range End</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="2026-11-05">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Product Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories (8)</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Sales Territory</label>
                <select name="region_id" class="form-select form-select-sm">
                    <option value="">All Global Territories (10)</option>
                    <?php foreach ($regions as $reg): ?>
                    <option value="<?= $reg['id'] ?>"><?= htmlspecialchars($reg['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="bi bi-funnel me-1"></i> Apply Analytical Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Analytical Engine Fast-Access Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="/customers" class="text-decoration-none">
            <div class="kpi-card text-center p-4">
                <i class="bi bi-people-fill fs-2 text-primary mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">RFM Segmentation</h6>
                <div class="text-muted small">Recency, Frequency & Spend scoring with 7 behavioural tiers</div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/customers" class="text-decoration-none">
            <div class="kpi-card text-center p-4">
                <i class="bi bi-grid-3x3-gap-fill fs-2 text-info mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Cohort Retention Matrix</h6>
                <div class="text-muted small">Track multi-month retention rates across acquisition cohorts</div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/products" class="text-decoration-none">
            <div class="kpi-card text-center p-4">
                <i class="bi bi-pie-chart-fill fs-2 text-warning mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Pareto 80/20 Distribution</h6>
                <div class="text-muted small">Classify revenue drivers across Class A, B & C product tiers</div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="/analytics" class="text-decoration-none">
            <div class="kpi-card text-center p-4">
                <i class="bi bi-cpu-fill fs-2 text-danger mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Anomalies & Forecasts</h6>
                <div class="text-muted small">Z-score outlier detection & Holt-Winters predictive horizons</div>
            </div>
        </a>
    </div>
</div>

<!-- Dimension Correlation & Performance Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-table text-primary me-2"></i>Monthly Aggregation & Efficiency Metrics</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Year-Month</th>
                        <th class="text-end">Revenue ($)</th>
                        <th class="text-end">Gross Profit ($)</th>
                        <th class="text-end">Margin %</th>
                        <th class="text-end">Order Count</th>
                        <th class="text-end">Avg Order Value (AOV)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_trends as $mt):
                        $r = (float)$mt['revenue'];
                        $p = (float)$mt['profit'];
                        $m = $r > 0 ? round(($p / $r) * 100, 1) : 0;
                        $o = (int)$mt['orders'];
                        $a = $o > 0 ? round($r / $o, 2) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($mt['ym']) ?></td>
                        <td class="text-end fw-semibold">$<?= number_format($r, 2) ?></td>
                        <td class="text-end text-success fw-semibold">$<?= number_format($p, 2) ?></td>
                        <td class="text-end"><span class="badge bg-success bg-opacity-10 text-success"><?= $m ?>%</span></td>
                        <td class="text-end"><?= number_format($o) ?></td>
                        <td class="text-end">$<?= number_format($a, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

