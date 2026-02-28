<!-- Scripts -->
<script src="<?php echo URLROOT; ?>/public/assets/libs/jquery/jquery.min.js"></script>
<script src="<?php echo URLROOT; ?>/public/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 (CDN fallback as local file logic is complex without listing subdirs) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('toggled');
            });
        }
    });
</script>
</body>
</html>
