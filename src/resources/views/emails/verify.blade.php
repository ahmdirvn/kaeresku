<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md text-center">
        @if ($status === 'success')
            <h2 class="text-2xl font-bold text-green-600">Berhasil</h2>
            <p class="mt-2">{{ $message }}</p>
            <a href="{{ route('login') }}" class="mt-4 inline-block px-4 py-2 bg-green-500 text-white rounded-lg">Login</a>
        @elseif ($status === 'warning')
            <h2 class="text-2xl font-bold text-yellow-600">Perhatian</h2>
            <p class="mt-2">{{ $message }}</p>
        @else
            <h2 class="text-2xl font-bold text-red-600">Gagal</h2>
            <p class="mt-2">{{ $message }}</p>
        @endif
    </div>
</body>
</html>
