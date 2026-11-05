<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - InsightFlow BI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="brand-icon mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.3rem;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h3 class="fw-bold mb-1">InsightFlow</h3>
            <p class="text-muted small">Sales & Business Intelligence Analytics Platform</p>
        </div>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger py-2 small mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i><?= htmlspecialchars($flashError) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success py-2 small mb-3">
            <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($flashSuccess) ?>
        </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-3">
                <label class="form-label small fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="loginEmail" name="email" class="form-control" placeholder="name@company.com" required value="admin@insightflow.io">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••" required value="AdminPassword123!">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
                Sign In to Platform <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>

        <div class="border-top pt-3 mt-2">
            <div class="text-center text-muted small fw-semibold mb-2">Instant Demo Role Sign In</div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="fillCreds('admin@insightflow.io', 'AdminPassword123!')">
                    Admin
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="fillCreds('analyst@insightflow.io', 'AnalystPassword123!')">
                    Analyst
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="fillCreds('manager@insightflow.io', 'ManagerPassword123!')">
                    Manager
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(email, pass) {
            document.getElementById('loginEmail').value = email;
            document.getElementById('loginPassword').value = pass;
        }
    </script>
</body>
</html>

