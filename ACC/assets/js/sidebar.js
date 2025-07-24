document.addEventListener('DOMContentLoaded', function() {
    // Get the sidebar and toggle button elements
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');
    
    // Check for saved sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.style.marginLeft = '4rem';
    }
    
    // Add click event listener to toggle button
    sidebarToggle.addEventListener('click', function() {
        // Toggle collapsed class on sidebar
        sidebar.classList.toggle('collapsed');
        
        // Update main content margin
        if (sidebar.classList.contains('collapsed')) {
            mainContent.style.marginLeft = '4rem';
            localStorage.setItem('sidebarCollapsed', 'true');
        } else {
            mainContent.style.marginLeft = '';
            localStorage.setItem('sidebarCollapsed', 'false');
        }
        
        // Toggle icon rotation
        const icon = sidebarToggle.querySelector('.material-icons');
        if (sidebar.classList.contains('collapsed')) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = '';
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768 && !sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('collapsed');
            mainContent.style.marginLeft = '4rem';
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    });
});
