<!-- Detailed Data Quality Report View -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="/import" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left me-1"></i>Back to Imports</a>
        <h4 class="fw-bold mb-0">Quality Audit Report for: <?= htmlspecialchars($import['filename']) ?></h4>
    </div>
    <div>
        <span class="badge bg-<?= $import['status'] === 'COMPLETED' ? 'success' : 'danger' ?> fs-6 px-3 py-2">
            Status: <?= htmlspecialchars($import['status']) ?>
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Overall Score Gauge -->
    <div class="col-lg-4">
        <div class="card-chart h-100 text-center p-4">
            <h6 class="text-uppercase text-muted fw-bold mb-3">Overall Data Quality Score</h6>
            <?php
            $score = (float)$import['quality_score'];
            $gaugeBg = $score >= 90 ? '#10b981' : ($score >= 70 ? '#3b82f6' : '#ef4444');
            ?>
            <div class="quality-gauge" style="background-color: <?= $gaugeBg ?>;">
                <?= $score ?>%
            </div>
            <div class="text-muted small">
                Total Rows: <strong><?= number_format($import['total_rows']) ?></strong> &bull;
                Imported: <strong><?= number_format($import['imported_rows']) ?></strong> &bull;
                Failed: <strong><?= number_format($import['failed_rows']) ?></strong>
            </div>
        </div>
    </div>

    <!-- 4 Dimension Scorecards -->
    <div class="col-lg-8">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-shield-check text-primary me-2"></i>Quality Dimension Metrics</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dimension</th>
                                <th class="text-end">Dimension Score</th>
                                <th class="text-end">Issues Flagged</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($import['quality_logs'] as $ql):
                                $dScore = (float)$ql['score'];
                                $badge = $dScore >= 90 ? 'bg-success' : ($dScore >= 70 ? 'bg-primary' : 'bg-danger');
                            ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($ql['metric_name']) ?></td>
                                <td class="text-end fw-bold"><?= $dScore ?>%</td>
                                <td class="text-end"><?= number_format($ql['issue_count']) ?></td>
                                <td class="text-center"><span class="badge <?= $badge ?>"><?= $dScore >= 80 ? 'PASSED' : 'FLAGGED' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Issue Logs -->
<?php if (!empty($import['error_log_parsed'])): ?>
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-exclamation-triangle text-warning me-2"></i>Sample Cleansed & Flagged Row Logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Row #</th>
                        <th>Issue Code</th>
                        <th>Detailed Telemetry Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($import['error_log_parsed'] as $iss): ?>
                    <tr>
                        <td class="fw-bold"><?= $iss['row'] ?? '-' ?></td>
                        <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($iss['type'] ?? 'FLAG') ?></span></td>
                        <td class="small text-muted"><?= htmlspecialchars($iss['message'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

