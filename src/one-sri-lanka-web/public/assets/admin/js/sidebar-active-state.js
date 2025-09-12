/**
 * Sidebar Active State Handler
 * Automatically opens and highlights active menu items based on current URL
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuration
    const config = {
        openClass: 'nav-item-open',
        showClass: 'show',
        navItemClass: 'nav-item',
        navLinkClass: 'nav-link',
        navSubmenuClass: 'nav-item-submenu',
        collapseClass: 'collapse'
    };
    
    /**
     * Set active states for sidebar navigation
     */
    function setSidebarActiveStates() {
        const currentUrl = window.location.href;
        const currentPath = window.location.pathname;
        
        // Find all navigation links
        const navLinks = document.querySelectorAll('.sidebar .nav-link[href]');
        
        navLinks.forEach(function(link) {
            const linkHref = link.getAttribute('href');
            
            // Skip empty or hash links
            if (!linkHref || linkHref === '#') {
                return;
            }
            
            // Check if current URL matches this link
            const isActive = currentUrl.includes(linkHref) || currentPath === linkHref;
            
            if (isActive) {
                // Open all parent submenu containers (no styling, just expand)
                let parentSubmenu = link.closest(`.${config.navSubmenuClass}`);
                while (parentSubmenu) {
                    // Add open class to parent submenu container
                    parentSubmenu.classList.add(config.openClass);
                    
                    // Find and show the collapsible submenu
                    const collapseElement = parentSubmenu.querySelector(`:scope > .${config.collapseClass}`);
                    if (collapseElement) {
                        collapseElement.classList.add(config.showClass);
                    }
                    
                    // Move up to find next parent submenu
                    parentSubmenu = parentSubmenu.parentElement.closest(`.${config.navSubmenuClass}`);
                }
            }
        });
    }
    
    /**
     * Handle route matching patterns for more accurate matching
     */
    function handleRoutePatterns() {
        const currentPath = window.location.pathname;
        const routePatterns = [
            {
                pattern: /\/admin\/common-complaints/,
                menuSelectors: [
                    'a[href*="common-complaints"]'
                ],
                parentMenus: [
                    '.nav-item-submenu:has(a[href*="common-complaints"])'
                ]
            },
            {
                pattern: /\/admin\/complaint-category/,
                menuSelectors: [
                    'a[href*="complaint-category"]'
                ],
                parentMenus: [
                    '.nav-item-submenu:has(a[href*="complaint-category"])'
                ]
            },
            {
                pattern: /\/admin\/alerts/,
                menuSelectors: [
                    'a[href*="alerts"]'
                ]
            }
            // Add more patterns as needed
        ];
        
        routePatterns.forEach(function(route) {
            if (route.pattern.test(currentPath)) {
                route.menuSelectors.forEach(function(selector) {
                    const links = document.querySelectorAll(selector);
                    links.forEach(function(link) {
                        // Open parent menus (no styling, just expand)
                        let parentSubmenu = link.closest(`.${config.navSubmenuClass}`);
                        while (parentSubmenu) {
                            parentSubmenu.classList.add(config.openClass);
                            
                            const collapseElement = parentSubmenu.querySelector(`:scope > .${config.collapseClass}`);
                            if (collapseElement) {
                                collapseElement.classList.add(config.showClass);
                            }
                            
                            parentSubmenu = parentSubmenu.parentElement.closest(`.${config.navSubmenuClass}`);
                        }
                    });
                });
                
                // Handle main "Complaints" menu if we're in any complaint-related section
                if (route.pattern.test(currentPath) && currentPath.includes('complaint')) {
                    const complaintsMenu = document.querySelector('a[href="#"]:has(+ ul a[href*="complaint"])');
                    if (complaintsMenu) {
                        const complaintsSubmenu = complaintsMenu.closest(`.${config.navSubmenuClass}`);
                        if (complaintsSubmenu) {
                            complaintsSubmenu.classList.add(config.openClass);
                            const collapseElement = complaintsSubmenu.querySelector(`:scope > .${config.collapseClass}`);
                            if (collapseElement) {
                                collapseElement.classList.add(config.showClass);
                            }
                        }
                    }
                }
            }
        });
    }
    
    /**
     * Handle specific complaint menu structures
     */
    function handleComplaintsMenu() {
        const currentPath = window.location.pathname;
        
        // If we're in any complaints-related page
        if (currentPath.includes('/admin/common-complaints') || currentPath.includes('/admin/complaint-category')) {
            
            // Find the main "Complaints" menu item
            const complaintsMenus = document.querySelectorAll('.nav-item-submenu');
            
            complaintsMenus.forEach(function(menu) {
                const menuText = menu.querySelector('.nav-link span');
                if (menuText && menuText.textContent.trim() === 'Complaints') {
                    // Open the main complaints menu
                    menu.classList.add(config.openClass);
                    const collapseElement = menu.querySelector(`:scope > .${config.collapseClass}`);
                    if (collapseElement) {
                        collapseElement.classList.add(config.showClass);
                    }
                    
                    // Find and activate the specific submenu
                    if (currentPath.includes('/admin/common-complaints')) {
                        const commonComplaintsSubmenu = menu.querySelector('.nav-item-submenu:has(a[href*="common-complaints"])');
                        if (commonComplaintsSubmenu) {
                            commonComplaintsSubmenu.classList.add(config.openClass);
                            const subCollapseElement = commonComplaintsSubmenu.querySelector(`:scope > .${config.collapseClass}`);
                            if (subCollapseElement) {
                                subCollapseElement.classList.add(config.showClass);
                            }
                        }
                    }
                    
                    if (currentPath.includes('/admin/complaint-category')) {
                        const categorySubmenu = menu.querySelector('.nav-item-submenu:has(a[href*="complaint-category"])');
                        if (categorySubmenu) {
                            categorySubmenu.classList.add(config.openClass);
                            const subCollapseElement = categorySubmenu.querySelector(`:scope > .${config.collapseClass}`);
                            if (subCollapseElement) {
                                subCollapseElement.classList.add(config.showClass);
                            }
                        }
                    }
                }
            });
        }
    }
    
    /**
     * Initialize sidebar active states
     */
    function initSidebarActiveStates() {
        // Clear existing open states
        document.querySelectorAll(`.sidebar .${config.openClass}`).forEach(function(element) {
            element.classList.remove(config.openClass);
        });
        
        // Clear existing show states  
        document.querySelectorAll(`.sidebar .${config.showClass}`).forEach(function(element) {
            element.classList.remove(config.showClass);
        });
        
        // Set active states based on current URL (only expand menus)
        setSidebarActiveStates();
        
        // Handle specific route patterns
        handleRoutePatterns();
        
        // Handle complaints menu specifically
        handleComplaintsMenu();
    }
    
    // Initialize when DOM is ready
    initSidebarActiveStates();
    
    // Re-initialize on popstate (browser back/forward)
    window.addEventListener('popstate', function() {
        setTimeout(initSidebarActiveStates, 100);
    });
    
    // Optional: Re-initialize on hash change
    window.addEventListener('hashchange', function() {
        setTimeout(initSidebarActiveStates, 100);
    });
});
