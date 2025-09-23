<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Admin Panel Footer (Updated Design)
 */
?>
    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            Version 2.0
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">High Data Center</a>.</strong> All rights reserved. ❤️ Developed by <strong><a href="https://skyrhrgts.com/">SKYRHRG Technologies Systems</a></a></strong>
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- THEME SWITCHER LOGIC -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeSwitch = document.getElementById('theme-switch-checkbox');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');
    const body = document.body;
    const mainHeader = document.querySelector('.main-header');
    const mainSidebar = document.querySelector('.main-sidebar');

    // Function to apply the selected theme
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            if (mainHeader) mainHeader.classList.replace('navbar-light', 'navbar-dark');
            if (mainSidebar) mainSidebar.classList.replace('sidebar-light-primary', 'sidebar-dark-primary');
            if (themeSwitch) themeSwitch.checked = true;
            if (themeIcon) themeIcon.className = 'fas fa-sun';
            if (themeText) themeText.textContent = 'Light Mode';
        } else {
            body.classList.remove('dark-mode');
            if (mainHeader) mainHeader.classList.replace('navbar-dark', 'navbar-light');
            if (mainSidebar) mainSidebar.classList.replace('sidebar-dark-primary', 'sidebar-light-primary');
            if (themeSwitch) themeSwitch.checked = false;
            if (themeIcon) themeIcon.className = 'fas fa-moon';
            if (themeText) themeText.textContent = 'Dark Mode';
        }
    };

    // Function to save the theme preference
    const saveTheme = (theme) => {
        localStorage.setItem('theme', theme);
    };

    // Event listener for the theme switch
    if (themeSwitch) {
        themeSwitch.addEventListener('change', () => {
            const newTheme = themeSwitch.checked ? 'dark' : 'light';
            applyTheme(newTheme);
            saveTheme(newTheme);
        });
    }

    // Load and apply the saved theme on page load
    const savedTheme = localStorage.getItem('theme') || 'light'; // Default to light mode
    applyTheme(savedTheme);
});
</script>

<!-- Custom JS (for any other custom scripts) -->
<script src="../../assets/js/script.js"></script>

</body>
</html>