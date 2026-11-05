<!-- System Administration & Security Audits View -->

<div class="row g-4 mb-4">
    <!-- User Management Card -->
    <div class="col-lg-7">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-people-fill text-primary me-2"></i>Platform Users & Role Assignments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($u['role_name']) ?></span>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($u['last_login_at'] ?? 'Never') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= htmlspecialchars($u['status']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Form Card -->
    <div class="col-lg-5">
        <div class="card-chart h-100">
            <div class="card-header">
                <h5><i class="bi bi-person-plus text-success me-2"></i>Provision New Platform User</h5>
            </div>
            <div class="card-body">
                <form action="/settings/users" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Rachel Green" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Work Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="rachel@company.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Temporary Password</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="••••••••" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Platform Role (RBAC)</label>
                        <select name="role_id" class="form-select form-select-sm" required>
                            <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['display_name']) ?> (<?= htmlspecialchars($r['name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-sm py-2 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Provision Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Security Audit Logs Table -->
<div class="card-chart">
    <div class="card-header">
        <h5><i class="bi bi-shield-lock text-dark me-2"></i>Security Audit Log (Immutable Events)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 450px;">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Event ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target Entity</th>
                        <th>Status</th>
                        <th>Telemetry & Metadata</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditLogs)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No audit events logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td class="small fw-semibold">#<?= $log['id'] ?></td>
                            <td class="small fw-bold text-dark"><?= htmlspecialchars($log['user_name'] ?? 'System/Guest') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($log['entity_type']) ?> (ID: <?= $log['entity_id'] ?? '-' ?>)</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($log['status']) ?></span></td>
                            <td class="small text-muted font-monospace" style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($log['metadata'] ?? '{}') ?>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($log['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

