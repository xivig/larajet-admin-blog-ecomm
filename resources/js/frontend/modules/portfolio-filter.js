/**
 * Portfolio showcase filter & case study modal controller (/project).
 */
export function initPortfolioFilter() {
    const dataEl = document.getElementById('portfolioProjectsData');
    const gridItems = document.querySelectorAll('.portfolio-grid-item');

    if (!dataEl && !gridItems.length) return;

    let projectsData = [];
    if (dataEl) {
        try {
            projectsData = JSON.parse(dataEl.textContent || '[]');
        } catch {
            projectsData = [];
        }
    }

    const baseAsset = dataEl?.dataset.assetBase || '/';
    const contactUrl = dataEl?.dataset.contactUrl || '/contact';
    const filterButtons = document.querySelectorAll('#portfolioFilterTabs .portfolio-filter-btn, .portfolio-filter-btn[data-filter]');
    const searchInput = document.getElementById('portfolioSearchInput');
    const emptyState = document.getElementById('portfolioEmptyState');
    const resetBtn = document.getElementById('resetPortfolioFilterBtn');
    let activeFilter = 'all';

    function applyPortfolioFilters() {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visibleCount = 0;

        gridItems.forEach((item) => {
            const cat = item.getAttribute('data-category');
            const haystack = item.getAttribute('data-search') || '';
            const matchesCategory = activeFilter === 'all' || cat === activeFilter;
            const matchesSearch = !query || haystack.indexOf(query) !== -1;

            if (matchesCategory && matchesSearch) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
        }
    }

    filterButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            filterButtons.forEach((b) => b.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.getAttribute('data-filter') || 'all';
            applyPortfolioFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyPortfolioFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            activeFilter = 'all';
            if (searchInput) searchInput.value = '';
            filterButtons.forEach((b) => {
                b.classList.toggle('active', b.getAttribute('data-filter') === 'all');
            });
            applyPortfolioFilters();
        });
    }

    const modalEl = document.getElementById('portfolioCaseStudyModal');
    let bsModal = null;

    function openCaseStudyModal(project) {
        if (!project || !modalEl) return;

        const modalImg = document.getElementById('caseStudyModalImg');
        if (modalImg) {
            modalImg.onerror = function () {
                this.onerror = null;
                this.src = baseAsset + (project.fallback || project.image);
            };
            modalImg.src = baseAsset + project.image;
            modalImg.alt = project.title;
        }

        const statusEl = document.getElementById('caseStudyModalStatus');
        if (statusEl) {
            statusEl.innerHTML = `<span class="badge-dot pulse" style="background: #34d399;"></span> ${project.status}`;
        }

        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };
        setText('caseStudyModalDomain', project.domain);
        setText('caseStudyModalSector', project.sector);
        setText('caseStudyModalTitle', project.title);
        setText('caseStudyModalOverview', project.overview);

        const verEl = document.getElementById('caseStudyModalVersion');
        if (verEl) verEl.innerHTML = `<i class="bi bi-git"></i> ${project.version}`;

        const statsEl = document.getElementById('caseStudyModalStats');
        if (statsEl) {
            statsEl.innerHTML = (project.stats || []).map((stat) =>
                `<div class="col-4"><div class="portfolio-modal-stat"><div class="font-weight-bold text-primary" style="font-size: 1.25rem; font-family: var(--font-heading);">${stat.value}</div><div class="small text-muted" style="font-size: 11.5px;">${stat.label}</div></div></div>`
            ).join('');
        }

        const delivEl = document.getElementById('caseStudyModalDeliverables');
        if (delivEl) {
            delivEl.innerHTML = (project.deliverables || []).map((item) =>
                `<li class="d-flex align-items-start small text-muted"><i class="bi bi-check-circle-fill text-success me-2 mr-2 mt-1 flex-shrink-0"></i><span>${item}</span></li>`
            ).join('');
        }

        const stackEl = document.getElementById('caseStudyModalStack');
        if (stackEl) {
            stackEl.innerHTML = (project.stack || []).map((tech) => `<span class="portfolio-tech-chip">${tech}</span>`).join('');
        }

        const quoteLink = document.getElementById('caseStudyModalQuoteLink');
        if (quoteLink) {
            quoteLink.href = `${contactUrl}?subject=${encodeURIComponent(`Architecture Inquiry: ${project.title}`)}`;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            bsModal = bsModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        } else if (window.jQuery && window.jQuery(modalEl).modal) {
            window.jQuery(modalEl).modal('show');
        }
    }

    document.querySelectorAll('.open-case-study-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-project-index') || '', 10);
            if (!Number.isNaN(idx) && projectsData[idx]) {
                openCaseStudyModal(projectsData[idx]);
            }
        });
    });
}
