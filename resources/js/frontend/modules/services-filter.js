/**
 * Services capability filter & scope modal controller (/services).
 */
export function initServicesFilter() {
    const dataEl = document.getElementById('servicesCatalogData');
    const gridItems = document.querySelectorAll('.service-grid-item');

    if (!dataEl && !gridItems.length) return;

    let servicesData = [];
    if (dataEl) {
        try {
            servicesData = JSON.parse(dataEl.textContent || '[]');
        } catch {
            servicesData = [];
        }
    }

    const contactUrl = dataEl?.dataset.contactUrl || '/contact';
    const filterTabs = document.querySelectorAll('#serviceFilterTabs .portfolio-filter-btn');
    const searchInput = document.getElementById('serviceSearchInput');
    const emptyState = document.getElementById('serviceEmptyState');
    const resetBtn = document.getElementById('resetServiceFilterBtn');
    let activePractice = 'all';

    function applyServiceFilters() {
        const q = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visible = 0;

        gridItems.forEach((item) => {
            const p = item.getAttribute('data-practice');
            const hay = item.getAttribute('data-search') || '';
            const show = (activePractice === 'all' || p === activePractice) && (!q || hay.indexOf(q) !== -1);
            item.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visible > 0);
        }
    }

    filterTabs.forEach((btn) => {
        btn.addEventListener('click', function () {
            filterTabs.forEach((b) => b.classList.remove('active'));
            this.classList.add('active');
            activePractice = this.getAttribute('data-practice') || 'all';
            applyServiceFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyServiceFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            activePractice = 'all';
            if (searchInput) searchInput.value = '';
            filterTabs.forEach((b) => {
                b.classList.toggle('active', b.getAttribute('data-practice') === 'all');
            });
            applyServiceFilters();
        });
    }

    const modalEl = document.getElementById('serviceScopeModal');
    let svcModal = null;

    function openScopeModal(svc) {
        if (!svc || !modalEl) return;

        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        setText('svcModalCode', `PRACTICE ${svc.code}`);
        setText('serviceScopeModalTitle', svc.title);
        setText('svcModalOverview', svc.overview);
        setText('svcModalPractice', svc.practice_label);
        setText('svcModalTimeline', svc.timeline);
        setText('svcModalTeam', svc.team);

        const slaEl = document.getElementById('svcModalSla');
        if (slaEl) slaEl.innerHTML = `<i class="bi bi-lightning-charge-fill"></i> ${svc.sla}`;

        const delivEl = document.getElementById('svcModalDeliverables');
        if (delivEl) {
            delivEl.innerHTML = (svc.deliverables || []).map((d) =>
                `<li><i class="bi bi-check-circle-fill"></i><span>${d}</span></li>`
            ).join('');
        }

        const stackEl = document.getElementById('svcModalStack');
        if (stackEl) {
            stackEl.innerHTML = (svc.stack || []).map((t) => `<span class="portfolio-tech-chip">${t}</span>`).join('');
        }

        const quoteLink = document.getElementById('svcModalQuoteLink');
        if (quoteLink) {
            quoteLink.href = `${contactUrl}?subject=${encodeURIComponent(`Service Inquiry: ${svc.title}`)}`;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            svcModal = svcModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
            svcModal.show();
        } else if (window.jQuery && window.jQuery(modalEl).modal) {
            window.jQuery(modalEl).modal('show');
        }
    }

    document.querySelectorAll('.inspect-service-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-service-index') || '', 10);
            if (!Number.isNaN(idx) && servicesData[idx]) {
                openScopeModal(servicesData[idx]);
            }
        });
    });
}
