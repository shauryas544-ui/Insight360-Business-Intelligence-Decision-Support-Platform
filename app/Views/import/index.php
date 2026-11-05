<!-- Data Import & Data Quality Hub View -->

<div class="row g-4 mb-4">
    <!-- Upload Area -->
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Upload Dataset for ETL & Quality Scoring</h5>
                <a href="/import/template" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i>Sample CSV
                </a>
            </div>
            <div class="card-body">
                <form action="/import/upload" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select CSV Dataset File</label>
                        <input type="file" name="dataset_file" class="form-control" accept=".csv,.txt" required>
                        <div class="form-text">
                            Required columns: <code>order_number</code>, <code>order_date</code>, <code>customer_code</code>, <code>sku</code>, <code>quantity</code>, <code>unit_price</code>.
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-3 small text-muted">
                        <div class="fw-semibold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i>Automated Data Quality Protocol:</div>
                        Every upload triggers our Python ETL pipeline to measure <strong>Completeness</strong>, <strong>Uniqueness</strong>, <strong>Validity</strong>, and <strong>Consistency</strong> before database insertion. Datasets scoring under 50% are automatically rejected.
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-gear-wide-connected me-1"></i> Process & Compute Quality Score
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Quality Engine Specs Card -->
    <div class="col-lg-6">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-check2-all text-success me-2"></i>Data Quality Engine Dimensions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-2 border">
                    <i class="bi bi-clipboard2-check fs-3 text-primary"></i>
                    <div>
                        <div class="fw-bold">Completeness Dimension (30% weight)</div>
                        <div class="small text-muted">Verifies no null or empty values across mandatory business transaction attributes.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-2 border">
                    <i class="bi bi-fingerprint fs-3 text-info"></i>
                    <div>
                        <div class="fw-bold">Uniqueness Dimension (20% weight)</div>
                        <div class="small text-muted">Detects duplicate order transactions and line-item collision keys.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-2 border">
                    <i class="bi bi-calendar-check fs-3 text-warning"></i>
                    <div>
                        <div class="fw-bold">Validity Dimension (30% weight)</div>
                        <div class="small text-muted">Ensures strict date parsing (ISO 8601) and strictly positive price & unit ranges.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                    <i class="bi bi-diagram-3 fs-3 text-success"></i>
                    <div>
                        <div class="fw-bold">Consistency Dimension (20% weight)</div>
                        <div class="small text-muted">Cross-verifies relational alignment across foreign key masters and category codes.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import History Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-clock-history text-primary me-2"></i>Dataset Ingestion History & Quality Scorecard</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job ID</th>
                        <th>Filename</th>
                        <th>File Size</th>
                        <th class="text-end">Total Rows</th>
                        <th class="text-end">Imported Rows</th>
                        <th class="text-center">Quality Score</th>
                        <th class="text-center">Status</th>
                        <th>Uploaded By</th>
                        <th>Timestamp</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($imports)): ?>
                        <tr><td colspan="10" class="text-center py-4 text-muted">No dataset uploads recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($imports as $imp):
                            $score = (float)$imp['quality_score'];
                            $scoreColor = $score >= 90 ? 'bg-success' : ($score >= 70 ? 'bg-primary' : 'bg-danger');
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">#<?= $imp['id'] ?></td>
                            <td class="fw-semibold text-primary"><?= htmlspecialchars($imp['filename']) ?></td>
                            <td class="small text-muted"><?= round($imp['file_size'] / 1024, 1) ?> KB</td>
                            <td class="text-end"><?= number_format($imp['total_rows']) ?></td>
                            <td class="text-end fw-bold"><?= number_format($imp['imported_rows']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $scoreColor ?> px-2 py-1"><?= $score ?>%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $imp['status'] === 'COMPLETED' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($imp['status']) ?></span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($imp['uploader_name'] ?? 'System') ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($imp['created_at']) ?></td>
                            <td class="text-center">
                                <a href="/import/<?= $imp['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.75rem;">
                                    Report &rarr;
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

