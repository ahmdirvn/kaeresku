<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaeresku - @yield('title', 'Rencanakan Studi dengan Mudah')</title>
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

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .content-wrapper {
            padding: 80px 20px;
            min-height: 80vh;
        }
    </style>
</head>
<body>

    <!-- Content -->
<div class="container content-wrapper">
    @yield('content')
</div>

</body>

<!-- Footer -->
<footer class="text-center py-4 bg-white border-top">
    &copy; 2025 Kaeresku. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>