<!-- Customer Analytics & RFM Segmentation View -->

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Total Corporate Accounts</span>
                <i class="bi bi-buildings text-primary fs-5"></i>
            </div>
            <div class="kpi-value"><?= number_format($total_customers) ?></div>
            <div class="kpi-trend text-muted small">Registered in platform</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Repeat Purchase Rate</span>
                <i class="bi bi-arrow-repeat text-success fs-5"></i>
            </div>
            <div class="kpi-value"><?= $repeat_rate ?>%</div>
            <div class="kpi-trend trend-up">
                <i class="bi bi-check-circle"></i>
                <span><?= $repeat_customers ?> accounts placed >1 order</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>RFM Model Status</span>
                <i class="bi bi-shield-check text-info fs-5"></i>
            </div>
            <div class="kpi-value text-success fs-2">Calibrated</div>
            <div class="kpi-trend text-muted small">7 Behavioral Segments Assigned</div>
        </div>
    </div>
</div>

<!-- RFM Behavioral Segment Distribution -->
<div class="card-chart mb-4">
    <div class="card-header">
        <h5><i class="bi bi-pie-chart text-primary me-2"></i>RFM Behavioral Segment Breakdown</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer Segment</th>
                        <th class="text-end">Account Count</th>
                        <th class="text-end">Total Spend ($)</th>
                        <th class="text-end">Avg Account Spend</th>
                        <th class="text-end">Avg Recency (Days)</th>
                        <th class="text-end">Avg Frequency</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($segments as $seg):
                        $name = $seg['segment_name'];
                        $badgeClass = match($name) {
                            'Champions' => 'bg-success',
                            'Loyal Customers' => 'bg-primary',
                            'Potential Loyalists' => 'bg-info',
                            'New Customers' => 'bg-secondary',
                            'At Risk' => 'bg-warning text-dark',
                            "Can't Lose Them" => 'bg-danger',
                            default => 'bg-dark'
                        };
                    ?>
                    <tr>
                        <td>
                            <span class="badge <?= $badgeClass ?> px-2 py-1 fs-6"><?= htmlspecialchars($name) ?></span>
                        </td>
                        <td class="text-end fw-semibold"><?= number_format($seg['customer_count']) ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format((float)$seg['total_revenue'], 2) ?></td>
                        <td class="text-end">$<?= number_format((float)$seg['avg_revenue'], 2) ?></td>
                        <td class="text-end"><?= round((float)$seg['avg_recency']) ?> days ago</td>
                        <td class="text-end"><?= round((float)$seg['avg_frequency'], 1) ?> orders</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Cohort Retention Matrix Heatmap -->
<div class="card-chart mb-4">
    <div class="card-header">
        <h5><i class="bi bi-grid-3x3-gap text-primary me-2"></i>Customer Cohort Monthly Retention Matrix (%)</h5>
        <span class="badge bg-light text-dark border">Acquisition Retention Matrix</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle text-center mb-0" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 110px;">Cohort Month</th>
                        <?php for ($i = 0; $i <= $max_period; $i++): ?>
                        <th>Month <?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cohort_months as $cMonth): ?>
                    <tr>
                        <td class="fw-bold bg-light text-start ps-3"><?= htmlspecialchars($cMonth) ?></td>
                        <?php for ($p = 0; $p <= $max_period; $p++):
                            $cell = $cohort_matrix[$cMonth][$p] ?? null;
                            if ($cell !== null):
                                $rate = $cell['rate'];
                                // Heatmap shading
                                $alpha = min(0.9, max(0.15, $rate / 100));
                                $bgColor = $p === 0 ? "rgba(37, 99, 235, 0.85)" : "rgba(16, 185, 129, {$alpha})";
                                $textColor = ($alpha > 0.45 || $p === 0) ? '#ffffff' : '#000000';
                            ?>
                            <td style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>; font-weight: 600;"
                                title="<?= $cell['active'] ?> accounts ($<?= number_format($cell['revenue']) ?>)">
                                <?= $rate ?>%
                            </td>
                            <?php else: ?>
                            <td class="bg-light text-muted">&ndash;</td>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Accounts Exploration Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-star text-warning me-2"></i>High-Value Strategic Corporate Accounts</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer Code</th>
                        <th>Company Name</th>
                        <th>Location</th>
                        <th>Segment</th>
                        <th class="text-end">Recency</th>
                        <th class="text-end">Orders</th>
                        <th class="text-end">Total Spend</th>
                        <th class="text-center">RFM Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_customers as $c): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($c['customer_code']) ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($c['city']) ?>, <?= htmlspecialchars($c['state']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['segment_name']) ?></span></td>
                        <td class="text-end small"><?= $c['recency_days'] ?> days</td>
                        <td class="text-end fw-semibold"><?= $c['frequency'] ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format((float)$c['monetary'], 2) ?></td>
                        <td class="text-center"><span class="badge bg-dark font-monospace"><?= htmlspecialchars($c['rfm_score']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

