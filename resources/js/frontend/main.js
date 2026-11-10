import { initThemeController } from './modules/theme-controller';
import { initNavigationPlugins } from './modules/navigation-plugins';
import { initSidebarAutocomplete } from './modules/sidebar-autocomplete';
import { initShareButtons } from './modules/share-buttons';
import { initGalleryLightbox } from './modules/gallery-lightbox';
import { initPortfolioFilter } from './modules/portfolio-filter';
import { initServicesFilter } from './modules/services-filter';
import { initVisualEssay } from './modules/visual-essay';
import { initStorefront } from './modules/storefront';

document.addEventListener('DOMContentLoaded', () => {
    initThemeController();
    initNavigationPlugins();
    initSidebarAutocomplete();
    initShareButtons();
    initGalleryLightbox();
    initPortfolioFilter();
    initServicesFilter();
    initVisualEssay();
    initStorefront();
});
