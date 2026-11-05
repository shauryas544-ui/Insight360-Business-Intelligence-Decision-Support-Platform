<!-- Marketing Analytics & ROI View -->

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Total Campaign Spend</span>
                <i class="bi bi-wallet text-primary fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($total_budget, 2) ?></div>
            <div class="kpi-trend text-muted small">Across active & past campaigns</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Revenue Generated</span>
                <i class="bi bi-cash-coin text-success fs-5"></i>
            </div>
            <div class="kpi-value text-success">$<?= number_format($total_revenue, 2) ?></div>
            <div class="kpi-trend trend-up">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Direct attributed revenue</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Average Campaign ROI</span>
                <i class="bi bi-percent text-info fs-5"></i>
            </div>
            <div class="kpi-value"><?= $overall_roi ?>%</div>
            <div class="kpi-trend trend-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>(Revenue - Cost) / Cost</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Avg Customer Acquisition Cost</span>
                <i class="bi bi-person-check text-warning fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($overall_cac, 2) ?></div>
            <div class="kpi-trend text-muted small"><?= number_format($total_conversions) ?> total enterprise wins</div>
        </div>
    </div>
</div>

<!-- Marketing Campaigns Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-bullseye text-primary me-2"></i>Marketing Campaigns & Attribution Ledger</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Campaign Name</th>
                        <th>Channel</th>
                        <th class="text-end">Budget ($)</th>
                        <th class="text-end">Impressions</th>
                        <th class="text-end">Clicks</th>
                        <th class="text-end">Leads</th>
                        <th class="text-end">Conversions</th>
                        <th class="text-end">CAC ($)</th>
                        <th class="text-end">Revenue ($)</th>
                        <th class="text-end">ROI %</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $camp):
                        $roi = (float)$camp['roi'];
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($camp['name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($camp['channel']) ?></span></td>
                        <td class="text-end">$<?= number_format((float)$camp['budget'], 2) ?></td>
                        <td class="text-end small"><?= number_format($camp['impressions']) ?></td>
                        <td class="text-end small"><?= number_format($camp['clicks']) ?></td>
                        <td class="text-end small"><?= number_format($camp['leads']) ?></td>
                        <td class="text-end fw-semibold"><?= number_format($camp['conversions']) ?></td>
                        <td class="text-end small">$<?= number_format((float)$camp['cac'], 2) ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format((float)$camp['revenue'], 2) ?></td>
                        <td class="text-end">
                            <span class="badge <?= $roi > 0 ? 'bg-success' : 'bg-danger' ?>"><?= $roi ?>%</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= $camp['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($camp['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

