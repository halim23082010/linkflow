document.addEventListener('DOMContentLoaded', () => {
    // DOM öğelerini seçme
    const menuBtn = document.querySelector('.menu-btn'); // Assuming you have a menu button outside the sidebar
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay'); // Assuming you have an overlay element
    const closeBtn = document.querySelector('.close-btn'); // This specific closeBtn is now technically hidden by CSS
    const hasSubmenuItems = document.querySelectorAll('.has-submenu');

    // Function to toggle sidebar visibility and related states
    function toggleSidebar(isOpen) {
        if (typeof isOpen === 'undefined') {
            isOpen = !sidebar.classList.contains('open'); // Toggle if no explicit state given
        }

        if (isOpen) {
            menuBtn.classList.add('active'); // Hamburger -> X dönüşümü
            sidebar.classList.add('open'); // Yan menüyü aç
            if (overlay) overlay.classList.add('open'); // Overlay'i göster
            document.body.style.overflow = 'hidden'; // Sayfa kaydırmasını engelle
            sidebar.setAttribute('aria-hidden', 'false'); // Accessibility update
        } else {
            menuBtn.classList.remove('active'); // X -> Hamburger dönüşümü
            sidebar.classList.remove('open'); // Yan menüyü kapat
            if (overlay) overlay.classList.remove('open'); // Overlay'i gizle
            document.body.style.overflow = ''; // Sayfa kaydırmasını aç
            sidebar.setAttribute('aria-hidden', 'true'); // Accessibility update
        }
    }

    // Menü butonuna tıklandığında (if it exists)
    if (menuBtn) {
        menuBtn.addEventListener('click', () => toggleSidebar());
    }

    // Kapatma butonuna tıklandığında (if you still want this to work, otherwise remove)
    // Note: In this setup, the menuBtn itself acts as the close trigger.
    // The close-btn in the sidebar is now visually hidden by CSS.
    if (closeBtn) { // Keeping this check in case you re-enable it or want to remove the JS entirely
        closeBtn.addEventListener('click', () => toggleSidebar(false));
    }


    // Overlay'e tıklandığında (if it exists)
    if (overlay) {
        overlay.addEventListener('click', () => toggleSidebar(false));
    }

    // Alt menüleri aç/kapat
    hasSubmenuItems.forEach(item => {
        const link = item.querySelector('.menu-link');
        const submenu = item.querySelector('.submenu');
        // The arrow SVG rotation is handled by CSS, no JS needed for its transformation
        // const arrowSvg = item.querySelector('.arrow svg'); // No longer directly used for transformation

        link.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default link behavior

            const isActive = item.classList.toggle('active'); // Toggle 'active' class on the parent menu item

            // Toggle aria-expanded for accessibility
            link.setAttribute('aria-expanded', isActive);

            // Control submenu display (using max-height for smooth transition)
            if (isActive) {
                // To get actual height for smooth transition
                submenu.style.display = 'flex'; // Temporarily display to get scrollHeight
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            } else {
                submenu.style.maxHeight = '0';
                // Listen for transition end to set display: 'none'
                submenu.addEventListener('transitionend', function handler() {
                    submenu.style.display = 'none';
                    submenu.removeEventListener('transitionend', handler); // Clean up listener
                }, { once: true }); // 'once: true' ensures the listener runs only once
            }
        });
    });
});


// Select all navigation items and hover sections
const navItems = document.querySelectorAll('.desktop-nav li');
const hoverSections = document.querySelectorAll('.hover-section');

// Add event listeners for hover
navItems.forEach((item, index) => {
    const section = hoverSections[index];

    // Show the corresponding section on hover
    item.addEventListener('mouseenter', () => {
        hoverSections.forEach(sec => sec.style.display = 'none'); // Hide all sections
        section.style.display = 'block'; // Show the current section
    });

    // Hide the section when the mouse leaves
    item.addEventListener('mouseleave', () => {
        section.style.display = 'none';
    });
});
