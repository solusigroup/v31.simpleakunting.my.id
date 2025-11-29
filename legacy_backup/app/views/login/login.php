"<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['judul']; ?> - SIMPLE AKUNTING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
        .main-container {
            display: flex;
            flex-direction: column; /* Mengubah arah flex */
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 1rem;
        }
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            background-color: #fff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 2rem; /* Memberi jarak dari footer */
        }
        .login-form-side {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .illustration-side {
            background: linear-gradient(135deg, #0d6efd, #0d63e3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
            text-align: center;
        }
        .illustration-side svg {
            width: 80%;
            max-width: 300px;
            height: auto;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
        }
        .brand-icon {
            font-size: 2rem;
            color: #0d6efd;
        }
        .login-footer {
            width: 100%;
            text-align: center;
            padding: 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .login-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .illustration-side {
                display: none;
            }
            .login-wrapper {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="login-wrapper">
            <!-- Sisi Ilustrasi -->
            <div class="col-lg-6 illustration-side d-none d-lg-flex">
                <div>
                    <!-- Ilustrasi SVG -->
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                      <path fill="#FFFFFF" d="M37.5,-45.3C52.3,-34.8,70.6,-22.7,76.8,-6.4C83,9.9,77.1,30.3,64.2,43.2C51.3,56.1,31.4,61.6,13.1,64.1C-5.2,66.6,-21.8,66.2,-36.5,59.3C-51.2,52.4,-64,39,-69.1,23.3C-74.2,7.6,-71.7,-10.5,-62.7,-23.5C-53.7,-36.5,-38.3,-44.4,-23.6,-51.2C-8.9,-58,-0.7,-57.7,9.3,-56.3C19.3,-54.9,37.5,-45.3,37.5,-45.3Z" transform="translate(100 100)" opacity="0.1" />
                      <path fill="#FFFFFF" d="M47.8,-53.4C62,-44.8,73.4,-30.9,79.5,-14.2C85.6,2.6,86.4,22.3,77.8,37.8C69.2,53.4,51.3,64.9,33.1,71.2C14.9,77.5,-3.6,78.6,-21.7,73.9C-39.8,69.2,-57.5,58.8,-67.2,43.9C-77,29,-78.8,9.6,-74.6,-6.5C-70.3,-22.6,-59.9,-35.4,-47.5,-44.7C-35,-54,-20.4,-60,-5.2,-58.9C10.1,-57.8,20.2,-50,47.8,-53.4Z" transform="translate(130 90)" opacity="0.15" />
                    </svg>
                    <h2 class="fw-bold mt-4">Modal awal dari Dana Desa habis tanpa jejak?</h2>
                    <p><h2 class="fw-bold mt-4">Kelola Keuangan Anda</h2></p>
                    <p class="mt-2">SIMPLE AKUNTING membantu Anda mencatat setiap transaksi dengan mudah dan menghasilkan laporan yang akurat sesuai kaidah, menghindarkan modal awal dari Dana Desa habis tanpa jejak</p>
                </div>
            </div>
            <!-- Sisi Form Login -->
            <div class="col-12 col-lg-6 login-form-side">
                <div class="text-center mb-4">
                   
                    <img src="<?php echo BASEURL; ?>/img/logo_klinik.png" alt="Logo PPNI" class="mx-auto" style="max-height: 80px;">
                </div>
                <h3 class="text-center fw-bold mb-1">Selamat Datang</h3>
                <p class="text-center text-muted mb-4">Silakan masuk untuk melanjutkan.</p>
                
                <?php Flash::flash(); ?>

                <form action="<?php echo BASEURL; ?>/login/process" method="post">
                    <div class="mb-3">
                        <label for="nama_user" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="nama_user" name="nama_user" placeholder="Contoh: Administrator" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="******" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- **PERUBAHAN: Footer ditambahkan di sini** -->
        <footer class="login-footer">
            &copy; <?php echo date('Y'); ?> - SIMPLE AKUNTING created by <a href="https://solusiconsulting.simpleakunting.biz.id" target="_blank">Kurniawan @Simple Akunting</a>
        </footer>
        <footer class="login-footer">
            &copy; <?php echo date('Y'); ?> - Analisa Laporan Keuangan <a href="https://finratio.simpleakunting.biz.id" target="_blank">oleh Kurniawan</a>
        </footer>
    </div>
</body>
</html>

