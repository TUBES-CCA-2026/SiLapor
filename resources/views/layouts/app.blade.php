<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiLapor')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        silapor: { 500: '#29ABE2', 600: '#1B8DC4', 700: '#156C99', dark: '#0E3A4D' },
                    },
                    fontFamily: {
                        display: ['Poppins', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style> body { font-family: 'Inter', sans-serif; } .font-display { font-family: 'Poppins', sans-serif; } </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800">

    @auth
    <nav class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-display font-bold text-silapor-700">
            <img src="{{ asset('images/logo-silapor.png') }}" alt="SiLapor" class="w-8 h-8 rounded-lg object-contain">
            SiLapor
        </a>
        <div class="flex items-center gap-4 text-sm">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="text-silapor-600 font-medium hover:underline">Panel Admin</a>
            @endif
            <span class="text-gray-500">{{ auth()->user()->nama }} <span class="text-xs uppercase text-silapor-600">({{ auth()->user()->role }})</span></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-500 hover:text-red-600">Keluar</button>
            </form>
        </div>
    </nav>
    @endauth

    <main>
        @if (session('success'))
            <div class="max-w-3xl mx-auto mt-4 px-4">
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
