<!-- Reports & Intelligence Export Hub -->

<div class="row g-4 mb-4">
    <!-- Executive Monthly Briefing Card -->
    <div class="col-lg-6">
        <div class="card-chart h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary">Executive Briefing</span>
                <span class="text-muted small">Generated: <?= $generated_at ?></span>
            </div>
            <h4 class="fw-bold mb-2">Monthly Executive Business Intelligence Report</h4>
            <p class="text-muted small mb-4">
                Consolidated C-Suite performance overview containing verified revenue trends, regional share, active operational alerts, 95% CI predictive forecasts, and evidence-based strategic recommendations.
            </p>

            <div class="d-flex gap-2">
                <a href="/reports/printable" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer me-1"></i> Open Printable View / PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Raw Data Export Cards -->
    <div class="col-lg-6">
        <div class="card-chart h-100 p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Direct Data Warehouse Exports (CSV)</h5>
            <p class="text-muted small mb-4">
                Export raw sanitized datasets directly to Excel or data engineering pipelines:
            </p>

            <div class="d-grid gap-2">
                <a href="/reports/export?type=sales" class="btn btn-outline-secondary d-flex justify-content-between align-items-center text-dark">
                    <span><i class="bi bi-receipt me-2 text-primary"></i>All Orders & Transactions Ledger</span>
                    <span class="badge bg-light text-dark border">CSV Download</span>
                </a>
                <a href="/reports/export?type=rfm" class="btn btn-outline-secondary d-flex justify-content-between align-items-center text-dark">
                    <span><i class="bi bi-people me-2 text-info"></i>Customer Accounts with RFM Scores</span>
                    <span class="badge bg-light text-dark border">CSV Download</span>
                </a>
                <a href="/reports/export?type=products" class="btn btn-outline-secondary d-flex justify-content-between align-items-center text-dark">
                    <span><i class="bi bi-box me-2 text-warning"></i>Product Catalog Profitability & Pareto Classes</span>
                    <span class="badge bg-light text-dark border">CSV Download</span>
                </a>
            </div>
        </div>
    </div>
</div>

