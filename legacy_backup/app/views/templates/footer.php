    </main> <!-- Penutup .p-4 -->
    
    <footer class="footer mt-auto py-3 text-muted text-center">
        <div class="container">
            <span>
                &copy; <?php echo date('Y'); ?> - SIMPLE AKUNTING created by <a href="https://solusiconsulting.simpleakunting.biz.id" target="_blank" class="fw-bold text-decoration-none">Kurniawan</a>
            </span>
        </div>
    </footer>

</div> <!-- Penutup .main-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        if (sidebarToggle) {
            // Logika untuk tombol toggle utama
            sidebarToggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
            });

            // Cek status dari localStorage saat halaman dimuat (untuk desktop)
            if (window.innerWidth >= 992) {
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    document.body.classList.add('sidebar-collapsed');
                }
            }
        }
        
        // **PERBAIKAN: Logika baru untuk auto-hide di layar kecil**
        const sidebarLinks = document.querySelectorAll('.sidebar a.nav-link');
        sidebarLinks.forEach(link => {
            // Kita tidak ingin collapse trigger menutup sidebar, hanya link navigasi
            if (!link.getAttribute('data-bs-toggle')) {
                link.addEventListener('click', function() {
                    // Hanya jalankan di layar kecil (lebar < 992px)
                    if (window.innerWidth < 992) {
                        document.body.classList.remove('sidebar-collapsed');
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                });
            }
        });
    });
</script>
</body>
</html>

