<?php
// admin/includes/footer.php
?>
    </div> <!-- End Main Content -->

    <!-- GSAP (if needed in admin) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <!-- Chart.js for dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Highlight active nav link
        const currentLocation = location.href;
        const menuItem = document.querySelectorAll('.sidebar nav a');
        const menuLength = menuItem.length;
        for (let i = 0; i < menuLength; i++) {
            if (menuItem[i].href === currentLocation) {
                menuItem[i].className = "active";
            }
        }
    </script>
</body>
</html>
