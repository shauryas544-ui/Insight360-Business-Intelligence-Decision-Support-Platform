// InsightFlow Analytics & UI Controller

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle Manual Analytics Job Run
    const runBtn = document.getElementById('btnRunAnalytics');
    if (runBtn) {
        runBtn.addEventListener('click', function () {
            const originalHtml = runBtn.innerHTML;
            runBtn.disabled = true;
            runBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Computing Insights...';

            fetch('/dashboard/run-analytics', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                runBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Completed!';
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(err => {
                alert('Analytics run failed: ' + err.message);
                runBtn.disabled = false;
                runBtn.innerHTML = originalHtml;
            });
        });
    }

    // Handle Why-Did-This-Change modal dynamic load
    const whyModal = document.getElementById('whyDidThisChangeModal');
    if (whyModal) {
        const period1Select = document.getElementById('whyPeriod1');
        const period2Select = document.getElementById('whyPeriod2');
        const calculateBtn = document.getElementById('btnRecalculateWhy');

        function loadWhyChange() {
            const p1 = period1Select ? period1Select.value : '';
            const p2 = period2Select ? period2Select.value : '';

            const container = document.getElementById('whyResultsContainer');
            container.innerHTML = '<div class="text-center py-4"><span class="spinner-border text-primary"></span><p class="mt-2 text-muted">Decomposing dimensional drivers...</p></div>';

            fetch(`/sales/why-change?period1=${encodeURIComponent(p1)}&period2=${encodeURIComponent(p2)}`)
                .then(r => r.json())
                .then(res => {
                    const d = res.data?.data || res.data;
                    if (!d || d.error) {
                        container.innerHTML = `<div class="alert alert-warning">${d?.error || 'Unable to compute driver changes'}</div>`;
                        return;
                    }

                    const isDrop = d.revenue_delta < 0;
                    const changeColor = isDrop ? 'text-danger' : 'text-success';
                    const changeIcon = isDrop ? 'bi-arrow-down-right' : 'bi-arrow-up-right';

                    let html = `
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Revenue Variance (${d.period1} vs ${d.period2})</h6>
                                    <h3 class="mb-0 ${changeColor}">
                                        <i class="bi ${changeIcon}"></i>
                                        $${Math.abs(d.revenue_delta).toLocaleString()}
                                        <span class="fs-6 fw-normal">(${d.revenue_pct_change > 0 ? '+' : ''}${d.revenue_pct_change}%)</span>
                                    </h3>
                                </div>
                                <div class="col-md-5 text-md-end">
                                    <div class="text-muted small">Orders Delta: <strong>${d.orders_delta > 0 ? '+' : ''}${d.orders_delta} orders</strong></div>
                                    <div class="text-muted small">${d.summary_text}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold mb-3"><i class="bi bi-tag me-1 text-primary"></i> Top Category Contributors</h6>
                    `;

                    (d.category_drivers || []).forEach(cat => {
                        const isNeg = cat.delta < 0;
                        const barClass = isNeg ? 'driver-bar-fill-neg' : 'driver-bar-fill-pos';
                        const barWidth = Math.min(100, Math.abs(cat.pct_change));
                        html += `
                            <div class="driver-bar-item">
                                <div class="driver-bar-header">
                                    <span>${cat.name}</span>
                                    <span class="${isNeg ? 'text-danger' : 'text-success'} fw-bold">${isNeg ? '' : '+'}${cat.pct_change}% ($${cat.delta.toLocaleString()})</span>
                                </div>
                                <div class="driver-bar-bg">
                                    <div class="${barClass}" style="width: ${barWidth}%;"></div>
                                </div>
                            </div>
                        `;
                    });

                    html += `
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-1 text-info"></i> Top Regional Contributors</h6>
                    `;

                    (d.regional_drivers || []).forEach(reg => {
                        const isNeg = reg.delta < 0;
                        const barClass = isNeg ? 'driver-bar-fill-neg' : 'driver-bar-fill-pos';
                        const barWidth = Math.min(100, Math.abs(reg.pct_change));
                        html += `
                            <div class="driver-bar-item">
                                <div class="driver-bar-header">
                                    <span>${reg.name}</span>
                                    <span class="${isNeg ? 'text-danger' : 'text-success'} fw-bold">${isNeg ? '' : '+'}${reg.pct_change}% ($${reg.delta.toLocaleString()})</span>
                                </div>
                                <div class="driver-bar-bg">
                                    <div class="${barClass}" style="width: ${barWidth}%;"></div>
                                </div>
                            </div>
                        `;
                    });

                    html += `
                            </div>
                        </div>
                    `;

                    container.innerHTML = html;
                })
                .catch(err => {
                    container.innerHTML = `<div class="alert alert-danger">Error loading breakdown: ${err.message}</div>`;
                });
        }

        whyModal.addEventListener('shown.bs.modal', function () {
            loadWhyChange();
        });

        if (calculateBtn) {
            calculateBtn.addEventListener('click', loadWhyChange);
        }
    }
});

