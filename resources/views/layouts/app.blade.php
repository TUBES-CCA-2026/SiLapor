<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiLapor')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
   
    @endauth

    <div id="app-content">
        @if (session('success'))
            <div class="max-w-3xl mx-auto mt-4 px-4">
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>
