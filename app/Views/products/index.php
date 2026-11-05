<!-- Product Analytics & Pareto 80/20 View -->

<!-- Pareto Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card border-start border-success border-4">
            <div class="kpi-title">
                <span>Class A Products (Core 80%)</span>
                <span class="badge badge-class-a">Top 80% Revenue</span>
            </div>
            <div class="kpi-value text-success"><?= $pareto_stats['A']['count'] ?> SKUs</div>
            <div class="text-muted small">
                Generates <strong>$<?= number_format($pareto_stats['A']['revenue'], 2) ?></strong>
                (<?= round(($pareto_stats['A']['revenue'] / max(1, $total_revenue)) * 100, 1) ?>% of total revenue)
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="kpi-card border-start border-primary border-4">
            <div class="kpi-title">
                <span>Class B Products (Next 15%)</span>
                <span class="badge badge-class-b">Mid 15% Revenue</span>
            </div>
            <div class="kpi-value text-primary"><?= $pareto_stats['B']['count'] ?> SKUs</div>
            <div class="text-muted small">
                Generates <strong>$<?= number_format($pareto_stats['B']['revenue'], 2) ?></strong>
                (<?= round(($pareto_stats['B']['revenue'] / max(1, $total_revenue)) * 100, 1) ?>% of total revenue)
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="kpi-card border-start border-danger border-4">
            <div class="kpi-title">
                <span>Class C Products (Long Tail 5%)</span>
                <span class="badge badge-class-c">Bottom 5% Revenue</span>
            </div>
            <div class="kpi-value text-danger"><?= $pareto_stats['C']['count'] ?> SKUs</div>
            <div class="text-muted small">
                Generates <strong>$<?= number_format($pareto_stats['C']['revenue'], 2) ?></strong>
                (<?= round(($pareto_stats['C']['revenue'] / max(1, $total_revenue)) * 100, 1) ?>% of total revenue)
            </div>
        </div>
    </div>
</div>

<!-- 4-Quadrant Strategic Product Profitability Grid -->
<div class="card-chart mb-4">
    <div class="card-header">
        <h5><i class="bi bi-grid-fill text-purple me-2"></i>Strategic 4-Quadrant Profitability Matrix</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Stars -->
            <div class="col-md-6">
                <div class="p-3 rounded-3 border border-success bg-success bg-opacity-10 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-success mb-0"><i class="bi bi-star-fill me-1"></i> Star SKUs (High Rev, High Margin)</h6>
                        <span class="badge bg-success"><?= count($quadrants['stars']) ?> Products</span>
                    </div>
                    <p class="text-muted small mb-2">Highest priority strategic assets. Protect market share and invest in capacity.</p>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach (array_slice($quadrants['stars'], 0, 4) as $s): ?>
                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                            <span><?= htmlspecialchars($s['product_name']) ?></span>
                            <span class="fw-bold">$<?= number_format((float)$s['revenue']) ?> (<?= $s['margin_pct'] ?>%)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Volume Risk -->
            <div class="col-md-6">
                <div class="p-3 rounded-3 border border-warning bg-warning bg-opacity-10 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-warning-emphasis mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> Volume Risk (High Rev, Low Margin)</h6>
                        <span class="badge bg-warning text-dark"><?= count($quadrants['volume_risk']) ?> Products</span>
                    </div>
                    <p class="text-muted small mb-2">High unit turnover but thin profitability or high support/return costs.</p>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach (array_slice($quadrants['volume_risk'], 0, 4) as $vr): ?>
                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                            <span><?= htmlspecialchars($vr['product_name']) ?></span>
                            <span class="fw-bold text-danger">$<?= number_format((float)$vr['revenue']) ?> (<?= $vr['margin_pct'] ?>%)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Niche Opportunities -->
            <div class="col-md-6">
                <div class="p-3 rounded-3 border border-info bg-info bg-opacity-10 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-info mb-0"><i class="bi bi-gem me-1"></i> Niche Assets (Low Rev, High Margin)</h6>
                        <span class="badge bg-info text-white"><?= count($quadrants['niche']) ?> Products</span>
                    </div>
                    <p class="text-muted small mb-2">High unit profitability with under-marketed volume. Prime expansion targets.</p>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach (array_slice($quadrants['niche'], 0, 4) as $nc): ?>
                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                            <span><?= htmlspecialchars($nc['product_name']) ?></span>
                            <span class="fw-bold text-success">$<?= number_format((float)$nc['revenue']) ?> (<?= $nc['margin_pct'] ?>%)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Underperforming -->
            <div class="col-md-6">
                <div class="p-3 rounded-3 border border-secondary bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-archive me-1"></i> Underperforming (Low Rev, Low Margin)</h6>
                        <span class="badge bg-secondary"><?= count($quadrants['underperforming']) ?> Products</span>
                    </div>
                    <p class="text-muted small mb-2">Review for discontinuation or bundle renegotiation.</p>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach (array_slice($quadrants['underperforming'], 0, 4) as $up): ?>
                        <li class="d-flex justify-content-between py-1 border-bottom border-light">
                            <span><?= htmlspecialchars($up['product_name']) ?></span>
                            <span class="fw-bold text-muted">$<?= number_format((float)$up['revenue']) ?> (<?= $up['margin_pct'] ?>%)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Products Master Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-table text-primary me-2"></i>Product Catalog Performance Drilldown</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-end">Units Sold</th>
                        <th class="text-end">Revenue ($)</th>
                        <th class="text-end">Gross Margin</th>
                        <th class="text-end">Return Rate</th>
                        <th class="text-end">Stock Level</th>
                        <th class="text-center">Pareto Class</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p):
                        $r = (float)$p['revenue'];
                        $m = (float)$p['margin_pct'];
                        $ret = (float)$p['return_rate'];
                        $pClass = $p['pareto_class'] ?: 'B';
                    ?>
                    <tr>
                        <td class="fw-bold text-primary font-monospace"><?= htmlspecialchars($p['sku']) ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($p['product_name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name']) ?></span></td>
                        <td class="text-end"><?= number_format($p['units_sold']) ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format($r, 2) ?></td>
                        <td class="text-end">
                            <span class="badge bg-success bg-opacity-10 text-success"><?= $m ?>%</span>
                        </td>
                        <td class="text-end <?= $ret > 15 ? 'text-danger fw-bold' : '' ?>">
                            <?= $ret ?>%
                        </td>
                        <td class="text-end">
                            <span class="badge <?= $p['current_stock'] <= $p['safety_stock'] ? 'bg-danger' : 'bg-light text-dark border' ?>">
                                <?= $p['current_stock'] ?> units
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-class-<?= strtolower($pClass) ?> px-2"><?= $pClass ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

