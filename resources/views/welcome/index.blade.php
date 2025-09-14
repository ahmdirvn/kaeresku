<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaeresku - Rencanakan Studi dengan Mudah</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .logo {
            font-weight: 700;
            font-size: 24px;
            color: #0d6efd;
            text-decoration: none;
        }

        .hero {
            text-align: center;
            padding: 120px 20px;
            background: linear-gradient(135deg, #e7f1ff, #ffffff);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #0d6efd;
        }

        .hero p {
            font-size: 20px;
            color: #6c757d;
            margin-bottom: 40px;
        }

        .btn-primary-custom {
            background-color: #0d6efd;
            color: white;
            padding: 14px 36px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }

        .btn-primary-custom:hover {
            background-color: #0b5ed7;
        }

        section {
            padding: 80px 50px;
            text-align: center;
        }

        section h2 {
            font-size: 36px;
            margin-bottom: 40px;
            color: #0d6efd;
        }

        section p {
            font-size: 18px;
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto 40px;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .feature-card {
            background-color: #ffffff;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 300px;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #0d6efd;
        }

        .feature-card p {
            font-size: 16px;
            color: #6c757d;
        }

        .cta {
            background-color: #e7f1ff;
            padding: 80px 50px;
        }

        footer {
            background-color: #ffffff;
            padding: 40px 50px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="logo" href="#">Kaeresku</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#cta">Start Now</a></li>

                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="display:inline; padding:0; border:none;">Logout</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>


<!-- Hero Section -->
<div class="hero">
    <h1>Kelola Kartu Rencana Studi dengan Mudah</h1>
    <p>Kaeresku membantu kamu merencanakan matkul, membuat jadwal, dan mencetak KRS dengan cepat dan praktis.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-primary-custom">Mulai Sekarang</a>
</div>

<!-- Features Section -->
<section id="features">
    <h2>Fitur Utama</h2>
    <div class="features">
        <div class="feature-card">
            <h3>Perencanaan Matkul</h3>
            <p>Tambahkan matkul sesuai semester dan rencana studi kamu dengan mudah tanpa ribet.</p>
        </div>
        <div class="feature-card">
            <h3>Buat Jadwal</h3>
            <p>Atur jadwal kuliah, lab, dan kelas tambahan agar tidak bentrok dan mudah diatur.</p>
        </div>
        <div class="feature-card">
            <h3>Cetak KRS</h3>
            <p>Cetak Kartu Rencana Studi langsung dari web, siap dibawa ke administrasi kampus.</p>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" style="background-color: #f1f3f5;">
    <h2>Tentang Kaeresku</h2>
    <p>Kaeresku adalah platform manajemen Kartu Rencana Studi modern yang memudahkan mahasiswa merencanakan studi mereka dengan efisien, meminimalkan bentrok jadwal, dan memastikan semua dokumen siap untuk dicetak kapan saja.</p>
</section>

<!-- CTA Section -->
<section id="cta" class="cta">
    <h2>Mulai Gunakan Kaeresku Sekarang</h2>
    <p>Buat rencana studimu, atur jadwal, dan cetak KRS dengan cepat. Semua dalam satu platform yang mudah digunakan.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-primary-custom">Daftar & Mulai</a>
</section>

<!-- Footer -->
<footer>
    &copy; 2025 Kaeresku. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
