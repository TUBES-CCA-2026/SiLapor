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
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Poppins', sans-serif; }
        .global-notification-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: grid;
            place-items: center;
            padding: 1.25rem;
            background: rgba(15, 23, 42, .35);
        }
        .global-notification-backdrop[hidden] { display: none !important; }
        .global-notification-card {
            width: min(440px, 96vw);
            border-radius: 1.5rem;
            background: #fff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .24);
            overflow: hidden;
            text-align: center;
        }
        .global-notification-header {
            min-height: 58px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #E5E7EB;
        }
        .global-notification-title {
            margin: 0;
            color: #1F2937;
            font-size: 1rem;
            font-weight: 800;
        }
        .global-notification-close {
            border: 0;
            background: transparent;
            color: #64748B;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
        }
        .global-notification-body { padding: 1.75rem 1.5rem 1.5rem; }
        .global-notification-icon {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: 1.65rem;
        }
        .global-notification-icon.success { background: #DCFCE7; color: #16A34A; }
        .global-notification-icon.error { background: #FEE2E2; color: #DC2626; }
        .global-notification-message {
            margin: 0;
            color: #374151;
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.6;
        }
        .global-notification-button {
            width: 100%;
            margin-top: 1.25rem;
            border: 0;
            border-radius: .95rem;
            padding: .8rem 1rem;
            background: #0090F5;
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }
        .global-notification-button:hover { background: #007CD5; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800">

    @auth
   
    @endauth

    <div id="app-content">
<<<<<<< HEAD
<<<<<<< HEAD
        @if (session('success'))
            <div class="max-w-3xl mx-auto mt-4 px-4">
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3 text-sm">
                    {{ session('success') }}
=======
=======
>>>>>>> 1446e82 (Istigfar)
        @yield('content')
    </div>
    

    @php
        $globalNotificationType = session('success') ? 'success' : ((session('error') || $errors->any()) ? 'error' : null);
        $globalNotificationMessage = session('success') ?: (session('error') ?: ($errors->any() ? $errors->first() : null));
        $globalNotificationTitle = $globalNotificationType === 'success' ? 'Berhasil' : 'Gagal';
    @endphp

    @if($globalNotificationType && $globalNotificationMessage)
        <div id="global-notification-popup" class="global-notification-backdrop">
            <div class="global-notification-card" role="dialog" aria-modal="true">
                <div class="global-notification-header">
                    <h2 class="global-notification-title">{{ $globalNotificationTitle }}</h2>
                    <button type="button" class="global-notification-close" onclick="document.getElementById('global-notification-popup')?.remove()">&times;</button>
                </div>
                <div class="global-notification-body">
                    <div class="global-notification-icon {{ $globalNotificationType }}">
                        <i class="fa-solid {{ $globalNotificationType === 'success' ? 'fa-check' : 'fa-triangle-exclamation' }}"></i>
                    </div>
                    <p class="global-notification-message">{{ $globalNotificationMessage }}</p>
                    <button type="button" class="global-notification-button" onclick="document.getElementById('global-notification-popup')?.remove()">Tutup</button>
<<<<<<< HEAD
>>>>>>> 2a3988f (bismillah)
=======
>>>>>>> 1446e82 (Istigfar)
                </div>
            </div>
        </div>
    @endif

<<<<<<< HEAD
<<<<<<< HEAD
        @yield('content')
    </div>
    
=======
>>>>>>> 2a3988f (bismillah)
=======
>>>>>>> 1446e82 (Istigfar)
    @stack('scripts')
</body>
</html>
