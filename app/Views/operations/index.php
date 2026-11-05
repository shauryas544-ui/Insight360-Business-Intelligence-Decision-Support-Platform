<!-- Operations Analytics View -->

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Overall Return Rate</span>
                <i class="bi bi-arrow-return-left text-danger fs-5"></i>
            </div>
            <div class="kpi-value text-danger"><?= $return_rate ?>%</div>
            <div class="kpi-trend text-muted small"><?= number_format($total_returns) ?> of <?= number_format($total_orders) ?> orders</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Total Refunded Spend</span>
                <i class="bi bi-cash text-warning fs-5"></i>
            </div>
            <div class="kpi-value">$<?= number_format($total_refunded, 2) ?></div>
            <div class="kpi-trend text-muted small">Processed credits & returns</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-title">
                <span>Inventory Safety Health</span>
                <i class="bi bi-box text-success fs-5"></i>
            </div>
            <div class="kpi-value text-success">Monitored</div>
            <div class="kpi-trend text-muted small">Automatic low-stock reorder thresholds</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Return Root Cause Breakdown -->
    <div class="col-lg-5">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart text-danger me-2"></i>Returns Root Cause Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($return_reasons as $rr): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($rr['reason']) ?></h6>
                            <div class="text-muted small">Total Refunded: $<?= number_format((float)$rr['total_refunded'], 2) ?></div>
                        </div>
                        <span class="badge bg-danger rounded-pill fs-6"><?= $rr['count'] ?> returns</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Levels & Safety Warnings -->
    <div class="col-lg-7">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-boxes text-primary me-2"></i>Warehouse Inventory & Safety Stock Warnings</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>SKU</th>
                                <th>Product</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Safety Stock</th>
                                <th class="text-end">Reorder Point</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $inv):
                                $status = $inv['stock_status'];
                                $badgeClass = match($status) {
                                    'CRITICAL' => 'bg-danger',
                                    'WARNING' => 'bg-warning text-dark',
                                    default => 'bg-success'
                                };
                            ?>
                            <tr>
                                <td class="fw-bold text-primary font-monospace"><?= htmlspecialchars($inv['sku']) ?></td>
                                <td class="small fw-semibold"><?= htmlspecialchars($inv['product_name']) ?></td>
                                <td class="text-end fw-bold"><?= $inv['current_stock'] ?></td>
                                <td class="text-end text-muted small"><?= $inv['safety_stock'] ?></td>
                                <td class="text-end text-muted small"><?= $inv['reorder_level'] ?></td>
                                <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $status ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

