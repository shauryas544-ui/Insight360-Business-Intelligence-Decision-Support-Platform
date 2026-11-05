<!-- Sales Analytics View -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Detailed breakdown of sales velocity, acquisition channels, and sales quota achievements.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#whyDidThisChangeModal">
        <i class="bi bi-question-diamond-fill me-2"></i>Analyze Drivers: Why Did Revenue Change?
    </button>
</div>

<!-- Sales Channels & Category Share -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-diagram-3 text-primary me-2"></i>Sales Performance by Channel</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Channel</th>
                                <th class="text-end">Orders</th>
                                <th class="text-end">Revenue ($)</th>
                                <th class="text-end">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalChannelRev = array_sum(array_column($channels, 'revenue'));
                            foreach ($channels as $ch):
                                $rev = (float)$ch['revenue'];
                                $share = $totalChannelRev > 0 ? round(($rev / $totalChannelRev) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td class="fw-semibold"><i class="bi bi-circle-fill text-primary me-2" style="font-size:0.5rem;"></i><?= htmlspecialchars($ch['channel']) ?></td>
                                <td class="text-end"><?= number_format($ch['order_count']) ?></td>
                                <td class="text-end fw-bold">$<?= number_format($rev, 2) ?></td>
                                <td class="text-end">
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= $share ?>%</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Contribution -->
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart text-success me-2"></i>Category Profitability Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Units</th>
                                <th class="text-end">Revenue ($)</th>
                                <th class="text-end">Profit ($)</th>
                                <th class="text-end">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat):
                                $r = (float)$cat['revenue'];
                                $p = (float)$cat['profit'];
                                $m = $r > 0 ? round(($p / $r) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($cat['category_name']) ?></td>
                                <td class="text-end"><?= number_format($cat['units_sold']) ?></td>
                                <td class="text-end fw-semibold">$<?= number_format($r, 2) ?></td>
                                <td class="text-end text-success fw-semibold">$<?= number_format($p, 2) ?></td>
                                <td class="text-end"><span class="badge bg-success bg-opacity-10 text-success"><?= $m ?>%</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Representative Quota Attainment Leaderboard -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-trophy text-warning me-2"></i>Sales Representative Quota Attainment & Leaderboard</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sales Executive</th>
                        <th>Assigned Territory</th>
                        <th class="text-end">Deals Closed</th>
                        <th class="text-end">Quarterly Target</th>
                        <th class="text-end">Actual Revenue</th>
                        <th class="text-end" style="width: 200px;">Quota Attainment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_reps as $rep):
                        $target = (float)$rep['target_quarterly'];
                        $sales = (float)$rep['total_sales'];
                        $attainment = $target > 0 ? round(($sales / $target) * 100, 1) : 0;
                        $progressColor = $attainment >= 100 ? 'bg-success' : ($attainment >= 75 ? 'bg-primary' : 'bg-warning');
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="bi bi-person-badge me-2 text-muted"></i><?= htmlspecialchars($rep['rep_name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($rep['region_name']) ?></span></td>
                        <td class="text-end fw-semibold"><?= number_format($rep['deals_closed']) ?></td>
                        <td class="text-end text-muted">$<?= number_format($target, 2) ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format($sales, 2) ?></td>
                        <td class="text-end">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar <?= $progressColor ?>" style="width: <?= min(100, $attainment) ?>%;"></div>
                                </div>
                                <span class="fw-semibold small <?= $attainment >= 100 ? 'text-success' : '' ?>"><?= $attainment ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

