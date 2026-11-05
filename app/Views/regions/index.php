<!-- Regional Analytics View -->

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card border-start border-primary border-4">
            <div class="kpi-title">
                <span>Top Performing Territory</span>
                <i class="bi bi-geo-alt-fill text-primary fs-5"></i>
            </div>
            <div class="kpi-value"><?= htmlspecialchars($top_region['region_name'] ?? 'N/A') ?></div>
            <div class="kpi-trend text-muted small">
                $<?= number_format((float)($top_region['revenue'] ?? 0), 2) ?> (<?= $top_region['share_pct'] ?? 0 ?>% share)
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card border-start border-warning border-4">
            <div class="kpi-title">
                <span>Territory Requiring Attention</span>
                <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
            </div>
            <div class="kpi-value"><?= htmlspecialchars($lagging_region['region_name'] ?? 'N/A') ?></div>
            <div class="kpi-trend text-muted small">
                $<?= number_format((float)($lagging_region['revenue'] ?? 0), 2) ?> (<?= $lagging_region['share_pct'] ?? 0 ?>% share)
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card border-start border-success border-4">
            <div class="kpi-title">
                <span>Global Coverage</span>
                <i class="bi bi-globe-americas text-success fs-5"></i>
            </div>
            <div class="kpi-value"><?= count($regions) ?> Territories</div>
            <div class="kpi-trend text-muted small">North America, EMEA & APAC</div>
        </div>
    </div>
</div>

<!-- Regional Performance Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-map text-primary me-2"></i>Territory Performance & Efficiency Matrix</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Territory Name</th>
                        <th>Country / Hub</th>
                        <th class="text-end">Orders</th>
                        <th class="text-end">Accounts</th>
                        <th class="text-end">Revenue ($)</th>
                        <th class="text-end">Global Share</th>
                        <th class="text-end">Profit Margin</th>
                        <th class="text-end">Return Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regions as $reg): ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="bi bi-geo-alt me-2 text-muted"></i><?= htmlspecialchars($reg['region_name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($reg['country']) ?> &bull; <?= htmlspecialchars($reg['city']) ?></span></td>
                        <td class="text-end"><?= number_format($reg['orders']) ?></td>
                        <td class="text-end"><?= number_format($reg['customers']) ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format((float)$reg['revenue'], 2) ?></td>
                        <td class="text-end">
                            <span class="badge bg-primary bg-opacity-10 text-primary"><?= $reg['share_pct'] ?>%</span>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-success bg-opacity-10 text-success"><?= $reg['margin_pct'] ?>%</span>
                        </td>
                        <td class="text-end small <?= $reg['return_rate'] > 10 ? 'text-danger fw-bold' : 'text-muted' ?>">
                            <?= $reg['return_rate'] ?>%
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

