<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InsightFlow - Monthly Executive Business Intelligence Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; color: #1e293b; background: #fff; padding: 30px; font-size: 0.9rem; }
        .report-header { border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 25px; }
        .section-title { font-size: 1.1rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin: 25px 0 15px; text-transform: uppercase; letter-spacing: 0.05em; color: #0f172a; }
        .kpi-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; }
        .kpi-box h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0; }
        .kpi-box .lbl { font-size: 0.72rem; text-transform: uppercase; color: #64748b; font-weight: 600; }
        .briefing-card { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; padding: 18px; border-radius: 6px; margin-bottom: 20px; }
        @media print {
            .btn-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-between align-items-center report-header">
        <div>
            <h2 class="fw-bold mb-1">InsightFlow Executive Business Intelligence Report</h2>
            <div class="text-muted small">Generated on: <?= $generated_at ?> &bull; Scope: Global Operations (18-Month Operating Cycle)</div>
        </div>
        <div class="text-end">
            <button onclick="window.print()" class="btn btn-sm btn-primary btn-print me-2">Print / Save as PDF</button>
            <span class="badge bg-dark px-3 py-2">CONFIDENTIAL &bull; C-SUITE ONLY</span>
        </div>
    </div>

    <!-- Executive Strategic Briefing -->
    <div class="briefing-card">
        <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($summary['executive_briefing']['headline']) ?></h5>
        <p class="text-dark mb-3 leading-relaxed" style="line-height: 1.6;">
            <?= htmlspecialchars($summary['executive_briefing']['pulse']) ?>
        </p>
        <div class="row g-2 pt-2 border-top">
            <div class="col-4">
                <small class="text-muted text-uppercase fw-bold">Primary Growth Driver:</small>
                <div class="small text-dark"><?= htmlspecialchars($summary['executive_briefing']['growth_drivers']) ?></div>
            </div>
            <div class="col-4">
                <small class="text-muted text-uppercase fw-bold">Operational Friction Drag:</small>
                <div class="small text-dark"><?= htmlspecialchars($summary['executive_briefing']['risk_friction']) ?></div>
            </div>
            <div class="col-4">
                <small class="text-muted text-uppercase fw-bold">Customer Retention Health:</small>
                <div class="small text-dark"><?= htmlspecialchars($summary['executive_briefing']['retention_health']) ?></div>
            </div>
        </div>
    </div>

    <!-- KPI Summary -->
    <div class="section-title">1. Verified Financial & Commercial Key Indicators</div>
    <div class="row g-3">
        <div class="col-3">
            <div class="kpi-box">
                <div class="lbl">Cumulative Net Revenue</div>
                <h3 class="text-primary">$<?= number_format($summary['revenue'], 2) ?></h3>
                <small class="text-muted"><?= $summary['mom_revenue_pct'] >= 0 ? '+' : '' ?><?= $summary['mom_revenue_pct'] ?>% MoM Full Cycle</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box">
                <div class="lbl">Gross Profit</div>
                <h3 class="text-success">$<?= number_format($summary['profit'], 2) ?></h3>
                <small class="text-muted"><?= $summary['margin_pct'] ?>% Profit Margin</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box">
                <div class="lbl">Average Order Value</div>
                <h3>$<?= number_format($summary['aov'], 2) ?></h3>
                <small class="text-muted"><?= number_format($summary['customers']) ?> Active Accounts</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box">
                <div class="lbl">Completed Orders</div>
                <h3><?= number_format($summary['orders']) ?></h3>
                <small class="text-muted">Target Attainment: <?= $summary['target_achievement_pct'] ?>%</small>
            </div>
        </div>
    </div>

    <!-- Category Contribution -->
    <div class="section-title">2. Category Profitability Breakdown</div>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product Category</th>
                <th class="text-end">Revenue ($)</th>
                <th class="text-end">Gross Profit ($)</th>
                <th class="text-end">Gross Margin %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary['category_breakdown'] as $cat):
                $r = (float)$cat['revenue'];
                $p = (float)$cat['profit'];
                $m = $r > 0 ? round(($p / $r) * 100, 1) : 0;
            ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($cat['category_name']) ?></td>
                <td class="text-end">$<?= number_format($r, 2) ?></td>
                <td class="text-end text-success">$<?= number_format($p, 2) ?></td>
                <td class="text-end"><?= $m ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Predictive Forecasts -->
    <div class="section-title">3. Predictive Forecast Projections (95% CI Horizons)</div>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th>Forecast Horizon</th>
                <th>Projected Date</th>
                <th class="text-end">Predicted Net Revenue ($)</th>
                <th class="text-end">Lower Bound (95%)</th>
                <th class="text-end">Upper Bound (95%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($forecasts as $fc): ?>
            <tr>
                <td><?= $fc['horizon_days'] ?> Days Horizon</td>
                <td><?= htmlspecialchars($fc['forecast_date']) ?></td>
                <td class="text-end fw-bold">$<?= number_format((float)$fc['predicted_value'], 2) ?></td>
                <td class="text-end">$<?= number_format((float)$fc['lower_bound'], 2) ?></td>
                <td class="text-end">$<?= number_format((float)$fc['upper_bound'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Strategic Evidence-based Recommendations -->
    <div class="section-title">4. Evidence-based Strategic Recommendations</div>
    <div class="list-group">
        <?php foreach ($recommendations as $rec): ?>
        <div class="list-group-item p-3 mb-2 border rounded">
            <div class="d-flex justify-content-between mb-1">
                <strong class="text-dark"><?= htmlspecialchars($rec['title']) ?></strong>
                <span class="badge bg-dark"><?= htmlspecialchars($rec['priority']) ?> Priority</span>
            </div>
            <div class="small text-muted mb-1"><strong>Diagnosed Context:</strong> <?= htmlspecialchars($rec['problem']) ?></div>
            <div class="small text-muted mb-1"><strong>Empirical Evidence:</strong> <?= htmlspecialchars($rec['evidence']) ?></div>
            <div class="small text-success"><strong>Prescribed Action Plan:</strong><br><?= nl2br(htmlspecialchars($rec['suggested_action'])) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
