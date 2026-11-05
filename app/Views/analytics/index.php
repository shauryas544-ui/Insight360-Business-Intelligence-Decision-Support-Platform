<!-- Advanced Analytics & Predictive Engine View -->

<!-- Forecasting Section -->
<div class="card-chart mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="bi bi-graph-up-arrow text-primary me-2"></i>Multi-Horizon Predictive Forecasting (30, 60, 90 Days)</h5>
        <span class="badge bg-primary">95% Confidence Interval</span>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">
            Projections calculated by the Python Analytics Engine using Linear Trend Regression and Standard Error Bounds over 18 months of historical transaction trends.
        </p>

        <div class="row g-3">
            <?php foreach ($forecasts as $fc): ?>
            <div class="col-md-4">
                <div class="p-3 rounded-3 border bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary text-uppercase"><?= $fc['horizon_days'] ?> Days Horizon</span>
                        <span class="text-muted small fw-semibold"><?= htmlspecialchars($fc['forecast_date']) ?></span>
                    </div>
                    <div class="text-muted small mb-1"><?= htmlspecialchars($fc['metric_name']) ?> Forecast</div>
                    <h3 class="fw-bold text-dark mb-2">$<?= number_format((float)$fc['predicted_value'], 2) ?></h3>

                    <div class="p-2 bg-white rounded border small">
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>Lower Bound (95%):</span>
                            <strong>$<?= number_format((float)$fc['lower_bound'], 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Upper Bound (95%):</span>
                            <strong>$<?= number_format((float)$fc['upper_bound'], 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Anomaly Detection Section -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-exclamation-diamond text-danger me-2"></i>Statistical Anomaly Detection (Z-Score & IQR Engine)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Severity</th>
                        <th>Metric & Dimension</th>
                        <th>Anomaly Date</th>
                        <th class="text-end">Actual Value</th>
                        <th class="text-end">Expected Value</th>
                        <th class="text-end">Deviation %</th>
                        <th class="text-end">Z-Score</th>
                        <th>Transparent Mathematical Root Cause</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($anomalies)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No statistical anomalies detected.</td></tr>
                    <?php else: ?>
                        <?php foreach ($anomalies as $anom):
                            $sev = $anom['severity'];
                            $badgeClass = match($sev) {
                                'CRITICAL' => 'badge-critical',
                                'HIGH' => 'badge-high',
                                default => 'badge-medium'
                            };
                        ?>
                        <tr>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($sev) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($anom['metric_name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($anom['dimension']) ?></div>
                            </td>
                            <td class="small fw-semibold"><?= htmlspecialchars($anom['anomaly_date']) ?></td>
                            <td class="text-end fw-bold"><?= number_format((float)$anom['actual_value'], 2) ?></td>
                            <td class="text-end text-muted"><?= number_format((float)$anom['expected_value'], 2) ?></td>
                            <td class="text-end fw-semibold <?= $anom['deviation_pct'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= $anom['deviation_pct'] > 0 ? '+' : '' ?><?= $anom['deviation_pct'] ?>%
                            </td>
                            <td class="text-end font-monospace"><?= $anom['z_score'] ?></td>
                            <td class="small text-muted" style="max-width: 320px;"><?= htmlspecialchars($anom['reason']) ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($anom['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

