/**
 * SiSehat Global Responsive Sidebar JS
 * Dynamically injects and manages mobile layout components.
 */

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return; // Exit if no sidebar is found on the page

    // 1. Create and inject the Hamburger toggle button if it doesn't exist
    let toggleBtn = document.getElementById('mobileMenuToggle');
    if (!toggleBtn) {
        toggleBtn = document.createElement('button');
        toggleBtn.id = 'mobileMenuToggle';
        toggleBtn.className = 'mobile-menu-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle Navigation Menu');
        
        // Add SVG hamburger icon inside
        toggleBtn.innerHTML = `
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        `;
        document.body.appendChild(toggleBtn);
    }

    // 2. Create and inject the backdrop overlay if it doesn't exist
    let overlay = document.getElementById('sidebarOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sidebarOverlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    // 3. Helper functions to toggle state
    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        toggleBtn.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling background content
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        toggleBtn.classList.remove('active');
        document.body.style.overflow = ''; // Restore background scroll
    }

    // 4. Toggle click actions
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (sidebar.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    // 5. Click overlay to close
    overlay.addEventListener('click', closeSidebar);

    // 6. Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // 7. Auto close and clean up on resize to larger screens
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            closeSidebar();
        }
    });
});
