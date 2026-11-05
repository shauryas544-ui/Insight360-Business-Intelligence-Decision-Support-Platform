<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> - InsightFlow</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Platform CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div>InsightFlow <span class="badge bg-primary text-uppercase fs-6" style="font-size:0.6rem !important;">BI 2.0</span></div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Core Intelligence</div>
                <a href="/dashboard" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Executive Dashboard
                </a>
                <a href="/analyst" class="nav-link <?= ($activeMenu ?? '') === 'analyst' ? 'active' : '' ?>">
                    <i class="bi bi-kanban"></i> Analyst Workspace
                </a>

                <div class="nav-section-title">Business Analytics</div>
                <a href="/sales" class="nav-link <?= ($activeMenu ?? '') === 'sales' ? 'active' : '' ?>">
                    <i class="bi bi-currency-dollar"></i> Sales & Revenue
                </a>
                <a href="/customers" class="nav-link <?= ($activeMenu ?? '') === 'customers' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i> Customers & RFM
                </a>
                <a href="/products" class="nav-link <?= ($activeMenu ?? '') === 'products' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i> Products & Pareto
                </a>
                <a href="/regions" class="nav-link <?= ($activeMenu ?? '') === 'regions' ? 'active' : '' ?>">
                    <i class="bi bi-globe-americas"></i> Regional Matrix
                </a>
                <a href="/marketing" class="nav-link <?= ($activeMenu ?? '') === 'marketing' ? 'active' : '' ?>">
                    <i class="bi bi-megaphone"></i> Marketing & CAC
                </a>
                <a href="/operations" class="nav-link <?= ($activeMenu ?? '') === 'operations' ? 'active' : '' ?>">
                    <i class="bi bi-gear-wide-connected"></i> Operations & Quality
                </a>

                <div class="nav-section-title">Predictive & Decisions</div>
                <a href="/analytics" class="nav-link <?= ($activeMenu ?? '') === 'advanced_analytics' ? 'active' : '' ?>">
                    <i class="bi bi-cpu"></i> Predictive & Anomalies
                </a>
                <a href="/alerts" class="nav-link <?= ($activeMenu ?? '') === 'alerts' ? 'active' : '' ?>">
                    <i class="bi bi-bell"></i> Alerts Center
                </a>
                <a href="/recommendations" class="nav-link <?= ($activeMenu ?? '') === 'recommendations' ? 'active' : '' ?>">
                    <i class="bi bi-lightbulb"></i> Recommendations
                </a>

                <div class="nav-section-title">Data & Administration</div>
                <a href="/import" class="nav-link <?= ($activeMenu ?? '') === 'import' ? 'active' : '' ?>">
                    <i class="bi bi-cloud-arrow-up"></i> Data Import & Quality
                </a>
                <a href="/reports" class="nav-link <?= ($activeMenu ?? '') === 'reports' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reports & Export
                </a>
                <?php if (($currentUser['role_name'] ?? '') === 'admin'): ?>
                <a href="/settings" class="nav-link <?= ($activeMenu ?? '') === 'settings' ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i> System & Audits
                </a>
                <?php endif; ?>
            </nav>

            <div class="p-3 border-top border-secondary border-opacity-10 text-muted small">
                <div>Python Engine: <span class="badge bg-success">Online</span></div>
                <div class="mt-1" style="font-size: 0.72rem;">Database: SQLite / MySQL 8+</div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Header -->
            <header class="app-header">
                <div class="d-flex align-items-center gap-3">
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'InsightFlow') ?></h1>
                    <!-- Quick Why-Did-This-Change Trigger Button -->
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#whyDidThisChangeModal">
                        <i class="bi bi-question-diamond-fill me-1"></i> Why Did This Change?
                    </button>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Manual Run Analytics Button -->
                    <button id="btnRunAnalytics" class="btn btn-sm btn-light border text-primary fw-semibold" title="Trigger Python Analytics Jobs">
                        <i class="bi bi-arrow-repeat me-1"></i> Run Analytics
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-6"></i>
                            <span><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                            <span class="badge bg-dark text-capitalize ms-1"><?= htmlspecialchars($currentUser['role_name'] ?? 'user') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><h6 class="dropdown-header"><?= htmlspecialchars($currentUser['email'] ?? '') ?></h6></li>
                            <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <?php if (($currentUser['role_name'] ?? '') === 'admin'): ?>
                            <li><a class="dropdown-item" href="/settings"><i class="bi bi-gear me-2"></i>Administration</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts Container -->
            <div class="px-4 pt-3">
                <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flashSuccess) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i><?= htmlspecialchars($flashError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Page Content -->
            <main class="app-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Signature "Why Did This Change?" Modal -->
    <div class="modal fade" id="whyDidThisChangeModal" tabindex="-1" aria-labelledby="whyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="whyModalLabel">
                        <i class="bi bi-pie-chart me-2 text-primary"></i>Why Did This Change? — KPI Driver Decomposition
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Select two calendar months to decompose the exact drivers behind revenue and order variances across categories, regions, and customer cohorts.
                    </p>

                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Base Month (Prior)</label>
                            <input type="text" id="whyPeriod1" class="form-control form-control-sm" value="2026-09" placeholder="YYYY-MM (e.g. 2026-09)">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Comparison Month (Recent)</label>
                            <input type="text" id="whyPeriod2" class="form-control form-control-sm" value="2026-10" placeholder="YYYY-MM (e.g. 2026-10)">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="btnRecalculateWhy" class="btn btn-sm btn-primary w-100" style="margin-top: 29px;">
                                Analyze
                            </button>
                        </div>
                    </div>

                    <div id="whyResultsContainer">
                        <!-- Loaded dynamically via AJAX -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Platform JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>

