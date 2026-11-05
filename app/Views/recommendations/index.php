<!-- Recommendations View -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-lightbulb text-warning me-2"></i>Executive Decision Support & Recommendations</h4>
        <p class="text-muted mb-0">Empirically grounded strategic actions synthesized from live transactional data, customer retention curves, and margin variances.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/recommendations" class="btn btn-sm <?= empty($currentFilter) ? 'btn-primary' : 'btn-light border' ?>">All Strategic Items</a>
        <a href="/recommendations?status=OPEN" class="btn btn-sm <?= $currentFilter === 'OPEN' ? 'btn-primary' : 'btn-light border' ?>">Open & Active</a>
        <a href="/recommendations?status=COMPLETED" class="btn btn-sm <?= $currentFilter === 'COMPLETED' ? 'btn-primary' : 'btn-light border' ?>">Executed & Completed</a>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($recommendations)): ?>
        <div class="col-12 text-center py-5 text-muted bg-white rounded-3 border">
            <i class="bi bi-check2-circle fs-1 text-success mb-2 d-block"></i>
            <h5>All Strategic Directives Completed</h5>
            <p class="small text-muted mb-0">No open recommendations pending leadership action.</p>
        </div>
    <?php else: ?>
        <?php foreach ($recommendations as $rec):
            $pri = $rec['priority'];
            $priBadge = match($pri) {
                'CRITICAL' => 'badge-critical',
                'HIGH' => 'badge-high',
                default => 'badge-medium'
            };
        ?>
        <div class="col-lg-6">
            <div class="card-chart h-100 p-4 shadow-sm border-top border-4 border-<?= $pri === 'CRITICAL' ? 'danger' : ($pri === 'HIGH' ? 'warning' : 'primary') ?>">
                <!-- Header Badge & Status -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-secondary text-uppercase px-2 py-1" style="font-size:0.72rem; letter-spacing: 0.05em;"><?= htmlspecialchars($rec['category']) ?></span>
                    <span class="badge <?= $priBadge ?> fw-semibold px-2 py-1"><?= htmlspecialchars($pri) ?> Priority</span>
                </div>

                <!-- Title -->
                <h5 class="fw-bold text-dark mb-3"><?= htmlspecialchars($rec['title']) ?></h5>

                <!-- Problem Narrative -->
                <div class="mb-3">
                    <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">Executive Context & Diagnosed Problem:</div>
                    <p class="text-dark small leading-relaxed mb-0 bg-light p-3 rounded border">
                        <?= htmlspecialchars($rec['problem']) ?>
                    </p>
                </div>

                <!-- Live Empirical Evidence -->
                <div class="mb-3">
                    <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;"><i class="bi bi-database-check text-primary me-1"></i>Observed Empirical Evidence:</div>
                    <div class="small text-dark p-2 rounded bg-white border">
                        <?= htmlspecialchars($rec['evidence']) ?>
                    </div>
                </div>

                <!-- Prescribed Strategic Plan -->
                <div class="mb-4">
                    <div class="text-uppercase text-muted fw-bold small mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;"><i class="bi bi-compass text-success me-1"></i>Prescribed Strategic Action Plan:</div>
                    <div class="p-3 rounded bg-success bg-opacity-10 text-dark small leading-relaxed border border-success border-opacity-25">
                        <div class="text-success fw-bold mb-1"><i class="bi bi-arrow-right-circle-fill me-1"></i>3-Step Implementation Roadmap:</div>
                        <div style="white-space: pre-line; line-height: 1.6;"><?= htmlspecialchars($rec['suggested_action']) ?></div>
                    </div>
                </div>

                <!-- Footer & Action Buttons -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="small text-muted">Scope: <strong class="text-dark"><?= htmlspecialchars($rec['affected_segment']) ?></strong></span>
                    <div>
                        <?php if ($rec['status'] === 'OPEN'): ?>
                        <form method="POST" action="/recommendations/<?= $rec['id'] ?>/status" class="d-inline">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="status" value="COMPLETED">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check2 me-1"></i>Mark Completed
                            </button>
                        </form>
                        <form method="POST" action="/recommendations/<?= $rec['id'] ?>/status" class="d-inline ms-1">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="status" value="DISMISSED">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Dismiss</button>
                        </form>
                        <?php else: ?>
                        <span class="badge bg-light text-muted border px-2 py-1"><?= htmlspecialchars($rec['status']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
