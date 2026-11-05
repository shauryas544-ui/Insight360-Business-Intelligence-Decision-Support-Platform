<!-- Alerts Center View -->

<!-- Counts Header -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card border-start border-danger border-4">
            <div class="kpi-title">Critical Alerts</div>
            <div class="kpi-value text-danger"><?= $counts['CRITICAL'] ?></div>
            <div class="kpi-trend text-muted small">Immediate action required</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card border-start border-warning border-4">
            <div class="kpi-title">High Severity Alerts</div>
            <div class="kpi-value text-warning-emphasis"><?= $counts['HIGH'] ?></div>
            <div class="kpi-trend text-muted small">Elevated priority</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card border-start border-primary border-4">
            <div class="kpi-title">Total Active Alerts</div>
            <div class="kpi-value text-primary"><?= $counts['TOTAL'] ?></div>
            <div class="kpi-trend text-muted small">Operational & analytical anomalies</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card border-start border-success border-4">
            <div class="kpi-title">Engine Monitoring</div>
            <div class="kpi-value text-success fs-2">Active</div>
            <div class="kpi-trend text-muted small">Continuous telemetry</div>
        </div>
    </div>
</div>

<!-- Alerts Table -->
<div class="card-chart">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="bi bi-bell text-danger me-2"></i>Alerts Ledger & Resolution Center</h5>
        <div class="d-flex gap-2">
            <a href="/alerts" class="btn btn-sm <?= empty($currentFilter) ? 'btn-primary' : 'btn-light border' ?>">All</a>
            <a href="/alerts?status=ACTIVE" class="btn btn-sm <?= $currentFilter === 'ACTIVE' ? 'btn-primary' : 'btn-light border' ?>">Active</a>
            <a href="/alerts?status=RESOLVED" class="btn btn-sm <?= $currentFilter === 'RESOLVED' ? 'btn-primary' : 'btn-light border' ?>">Resolved</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Severity</th>
                        <th>Alert Summary</th>
                        <th>Metric Observed</th>
                        <th>Actual vs Expected</th>
                        <th>Evidence / Reason</th>
                        <th>Action Protocol</th>
                        <th class="text-center">Resolution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alerts)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No alerts found under this filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($alerts as $a):
                            $sev = $a['severity'];
                            $badgeClass = match($sev) {
                                'CRITICAL' => 'badge-critical',
                                'HIGH' => 'badge-high',
                                default => 'badge-medium'
                            };
                        ?>
                        <tr>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($sev) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($a['title']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($a['created_at']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($a['metric']) ?></span></td>
                            <td class="small">
                                <span class="fw-bold text-dark"><?= htmlspecialchars($a['actual_value']) ?></span>
                                <span class="text-muted">/ <?= htmlspecialchars($a['expected_value']) ?></span>
                            </td>
                            <td class="small text-muted" style="max-width: 250px;"><?= htmlspecialchars($a['reason']) ?></td>
                            <td class="small text-primary" style="max-width: 220px;"><?= htmlspecialchars($a['recommendation']) ?></td>
                            <td class="text-center">
                                <?php if ($a['status'] === 'ACTIVE'): ?>
                                <form method="POST" action="/alerts/<?= $a['id'] ?>/status" class="d-inline">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="status" value="RESOLVED">
                                    <button type="submit" class="btn btn-xs btn-outline-success btn-sm">Resolve</button>
                                </form>
                                <form method="POST" action="/alerts/<?= $a['id'] ?>/status" class="d-inline ms-1">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="status" value="DISMISSED">
                                    <button type="submit" class="btn btn-xs btn-outline-secondary btn-sm">Dismiss</button>
                                </form>
                                <?php else: ?>
                                <span class="badge bg-light text-muted border"><?= htmlspecialchars($a['status']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

