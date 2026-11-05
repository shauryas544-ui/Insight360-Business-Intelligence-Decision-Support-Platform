<!-- Executive Dashboard View -->

<!-- Real Dynamically Computed Commercial KPI Cards -->
<div class="row g-3 mb-4">
    <!-- KPI 1: Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Cumulative Net Revenue</span>
                <i class="bi bi-wallet2 text-primary fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($revenue, 2) ?></div>
            <div class="kpi-trend <?= $mom_revenue_pct >= 0 ? 'trend-up' : 'trend-down' ?>">
                <i class="bi bi-arrow-<?= $mom_revenue_pct >= 0 ? 'up' : 'down' ?>-right"></i>
                <span><?= $mom_revenue_pct >= 0 ? '+' : '' ?><?= $mom_revenue_pct ?>% <?= htmlspecialchars($comparison_label) ?></span>
            </div>
        </div>
    </div>

    <!-- KPI 2: Gross Profit -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Gross Profit (Margin)</span>
                <i class="bi bi-cash-stack text-success fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($profit, 2) ?></div>
            <div class="kpi-trend <?= $mom_profit_pct >= 0 ? 'trend-up' : 'trend-down' ?>">
                <i class="bi bi-percent"></i>
                <span><?= $margin_pct ?>% margin (<?= $mom_profit_pct >= 0 ? '+' : '' ?><?= $mom_profit_pct ?>% MoM)</span>
            </div>
        </div>
    </div>

    <!-- KPI 3: Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Completed Orders</span>
                <i class="bi bi-cart-check text-info fs-5"></i>
            </div>
            <div class="kpi-value"><?= number_format($orders) ?></div>
            <div class="kpi-trend <?= $mom_orders_pct >= 0 ? 'trend-up' : 'trend-down' ?>">
                <i class="bi bi-arrow-<?= $mom_orders_pct >= 0 ? 'up' : 'down' ?>-right"></i>
                <span><?= $mom_orders_pct >= 0 ? '+' : '' ?><?= $mom_orders_pct ?>% MoM volume</span>
            </div>
        </div>
    </div>

    <!-- KPI 4: AOV -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Avg Order Value (AOV)</span>
                <i class="bi bi-receipt-cutoff text-warning fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($aov, 2) ?></div>
            <div class="kpi-trend <?= $mom_aov_pct >= 0 ? 'trend-up' : 'trend-down' ?>">
                <i class="bi bi-people-fill text-muted me-1"></i>
                <span><?= number_format($customers) ?> active corporate clients</span>
            </div>
        </div>
    </div>
</div>

<!-- Executive Strategic Intelligence & Briefing (Humanized Synthesis) -->
<div class="card-chart mb-4 border-start border-primary border-4 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-primary text-uppercase px-2 py-1 mb-1" style="font-size:0.68rem;">Strategic Intelligence</span>
            <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-journal-text text-primary me-2"></i><?= htmlspecialchars($executive_briefing['headline']) ?></h5>
        </div>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#executiveBriefingBody">
            <i class="bi bi-arrows-expand me-1"></i>Toggle Briefing
        </button>
    </div>
    <div class="collapse show" id="executiveBriefingBody">
        <div class="card-body p-4 bg-light bg-opacity-25">
            <!-- Commercial Pulse Paragraph -->
            <div class="mb-4">
                <h6 class="text-uppercase text-muted fw-bold small mb-2" style="letter-spacing: 0.05em;">Commercial Operating Momentum</h6>
                <p class="text-dark leading-relaxed mb-0" style="font-size: 0.95rem; line-height: 1.65;">
                    <?= htmlspecialchars($executive_briefing['pulse']) ?>
                </p>
            </div>

            <!-- 3 Core Analytical Dimensions -->
            <div class="row g-3 mb-4">
                <!-- Dimension 1: Growth Engine -->
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle"><i class="bi bi-graph-up-arrow"></i></span>
                            <span class="fw-bold text-dark small text-uppercase">Growth Drivers</span>
                        </div>
                        <p class="small text-muted mb-0 leading-normal">
                            <?= htmlspecialchars($executive_briefing['growth_drivers']) ?>
                        </p>
                    </div>
                </div>

                <!-- Dimension 2: Friction Points -->
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-circle"><i class="bi bi-exclamation-triangle"></i></span>
                            <span class="fw-bold text-dark small text-uppercase">Operational Friction</span>
                        </div>
                        <p class="small text-muted mb-0 leading-normal">
                            <?= htmlspecialchars($executive_briefing['risk_friction']) ?>
                        </p>
                    </div>
                </div>

                <!-- Dimension 3: Account Health -->
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis p-2 rounded-circle"><i class="bi bi-shield-exclamation"></i></span>
                            <span class="fw-bold text-dark small text-uppercase">Retention Health</span>
                        </div>
                        <p class="small text-muted mb-0 leading-normal">
                            <?= htmlspecialchars($executive_briefing['retention_health']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Leadership Directives -->
            <div class="border-top pt-3">
                <h6 class="text-uppercase text-muted fw-bold small mb-3" style="letter-spacing: 0.05em;">Recommended C-Suite Action Directives</h6>
                <div class="row g-2">
                    <?php foreach ($executive_briefing['directives'] as $dir): ?>
                    <div class="col-md-4">
                        <div class="p-2 px-3 bg-white border rounded d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-dark small mb-1"><?= htmlspecialchars($dir['title']) ?></div>
                                <div class="text-muted" style="font-size: 0.76rem;"><?= htmlspecialchars($dir['action']) ?></div>
                                <div class="text-primary mt-1" style="font-size: 0.72rem;"><strong>Owner:</strong> <?= htmlspecialchars($dir['owner']) ?></div>
                            </div>
                            <span class="badge <?= $dir['badge_class'] ?> ms-2"><?= htmlspecialchars($dir['badge']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1: Trends & Regional Distribution -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card-chart">
            <div class="card-header">
                <h5><i class="bi bi-graph-up text-primary me-2"></i>Monthly Revenue & Profit Growth</h5>
                <span class="badge bg-light text-dark border">18-Month Operating Trajectory</span>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-chart">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart text-info me-2"></i>Regional Revenue Contribution</h5>
            </div>
            <div class="card-body">
                <canvas id="regionalShareChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Category Breakdown & Forecast Outlook -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card-chart">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart-steps text-purple me-2"></i>Revenue & Profit by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="130"></canvas>
            </div>
        </div>
    </div>

    <!-- Predictive Forecast Summary Card -->
    <div class="col-lg-5">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-lightning-charge text-warning me-2"></i>Predictive Forecast Outlook (95% CI)</h5>
                <a href="/analytics" class="small text-decoration-none">Full Engine &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Horizon</th>
                                <th>Forecast Date</th>
                                <th class="text-end">Predicted</th>
                                <th class="text-end">Confidence Bounds</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forecasts as $fc): ?>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= $fc['horizon_days'] ?> Days</span></td>
                                <td class="small fw-semibold"><?= htmlspecialchars($fc['forecast_date']) ?></td>
                                <td class="text-end fw-bold text-dark">$<?= number_format((float)$fc['predicted_value'], 2) ?></td>
                                <td class="text-end small text-muted">
                                    $<?= number_format((float)$fc['lower_bound'], 0) ?> &ndash; $<?= number_format((float)$fc['upper_bound'], 0) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-2 mt-3 bg-light rounded text-muted small" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i> Modeled using Linear Trend Regression with Standard Error Bounds. Projections adapt dynamically as new orders are ingested.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decision Support Row: Alerts & Recommendations -->
<div class="row g-3">
    <!-- Active Alerts -->
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-bell text-danger me-2"></i>Active Operational & Statistical Alerts</h5>
                <a href="/alerts" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($alerts)): ?>
                        <div class="p-4 text-center text-muted">No active critical alerts logged.</div>
                    <?php else: ?>
                        <?php foreach ($alerts as $alt): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-<?= strtolower($alt['severity']) ?> fw-semibold"><?= htmlspecialchars($alt['severity']) ?></span>
                                <span class="text-muted small"><?= htmlspecialchars(substr($alt['created_at'], 0, 10)) ?></span>
                            </div>
                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($alt['title']) ?></h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($alt['reason']) ?></p>
                            <div class="small text-primary"><i class="bi bi-arrow-return-right me-1"></i><?= htmlspecialchars($alt['recommendation']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-lightbulb text-warning me-2"></i>Evidence-based Business Recommendations</h5>
                <a href="/recommendations" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($recommendations)): ?>
                        <div class="p-4 text-center text-muted">No pending recommendations.</div>
                    <?php else: ?>
                        <?php foreach ($recommendations as $rec): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-secondary text-uppercase" style="font-size:0.7rem;"><?= htmlspecialchars($rec['category']) ?></span>
                                <span class="badge badge-<?= strtolower($rec['priority']) ?> fw-semibold"><?= htmlspecialchars($rec['priority']) ?></span>
                            </div>
                            <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($rec['title']) ?></h6>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($rec['problem']) ?></p>
                            <div class="p-2 bg-light rounded text-dark small mb-2 border">
                                <strong>Observed Evidence:</strong> <?= htmlspecialchars($rec['evidence']) ?>
                            </div>
                            <div class="small text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i><?= nl2br(htmlspecialchars($rec['suggested_action'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Monthly Trends Chart
    const monthlyLabels = <?= json_encode(array_column($monthly_trends, 'ym')) ?>;
    const monthlyRevenue = <?= json_encode(array_map('floatval', array_column($monthly_trends, 'revenue'))) ?>;
    const monthlyProfit = <?= json_encode(array_map('floatval', array_column($monthly_trends, 'profit'))) ?>;

    const ctxTrend = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: monthlyRevenue,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 3
                },
                {
                    label: 'Gross Profit ($)',
                    data: monthlyProfit,
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    borderDash: [4, 4],
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function (val) {
                            return '$' + (val / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            }
        }
    });

    // 2. Regional Share Chart
    const regLabels = <?= json_encode(array_column($regional_share, 'region_name')) ?>;
    const regData = <?= json_encode(array_map('floatval', array_column($regional_share, 'revenue'))) ?>;

    const ctxReg = document.getElementById('regionalShareChart').getContext('2d');
    new Chart(ctxReg, {
        type: 'doughnut',
        data: {
            labels: regLabels,
            datasets: [{
                data: regData,
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#64748b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.label + ': $' + ctx.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // 3. Category Breakdown Chart
    const catLabels = <?= json_encode(array_column($category_breakdown, 'category_name')) ?>;
    const catRevenue = <?= json_encode(array_map('floatval', array_column($category_breakdown, 'revenue'))) ?>;
    const catProfit = <?= json_encode(array_map('floatval', array_column($category_breakdown, 'profit'))) ?>;

    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'bar',
        data: {
            labels: catLabels,
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: catRevenue,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                },
                {
                    label: 'Profit ($)',
                    data: catProfit,
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (c) {
                            return c.dataset.label + ': $' + c.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: { ticks: { font: { size: 9 }, maxRotation: 25 } },
                y: {
                    ticks: {
                        callback: function (v) { return '$' + (v / 1000).toFixed(0) + 'k'; }
                    }
                }
            }
        }
    });
});
</script>
