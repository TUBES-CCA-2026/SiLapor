<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SiLapor Dashboard' }}</title>
    <style>
:root {
    --page-bg: #f6f9fc;
    --panel: #ffffff;
    --soft-panel: #f7f9fc;
    --line: #e5ebf2;
    --text: #10263d;
    --heading: #132f4a;
    --muted: #71829a;
    --accent: #0d8ff2;
    --accent-dark: #057bd5;
    --danger: #ff4b55;
    --danger-soft: #ffe8ea;
    --process: #0d8ff2;
    --process-soft: #e6f4ff;
    --success: #25c65a;
    --success-soft: #e1f8ec;
    --radius-xl: 26px;
    --radius-lg: 20px;
    --radius-md: 14px;
    --shadow-sm: 0 8px 22px rgba(14, 35, 60, .06);
    --shadow-md: 0 20px 45px rgba(14, 35, 60, .08);
    --sidebar-w: 270px;
}

* {
    box-sizing: border-box;
}

html,
body {
    min-height: 100%;
    margin: 0;
}

body {
    font-family: "Inter", "Segoe UI", Arial, sans-serif;
    background: var(--page-bg);
    color: var(--text);
    font-weight: 400;
    -webkit-font-smoothing: antialiased;
    text-rendering: geometricPrecision;
}

button,
a,
input,
select,
textarea {
    font: inherit;
}

.app-shell {
    width: 100%;
    min-height: 100vh;
    display: grid;
    grid-template-columns: var(--sidebar-w) minmax(0, 1fr);
    background: var(--page-bg);
}

.sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    background: var(--panel);
    border-right: 1px solid var(--line);
    display: flex;
    flex-direction: column;
    z-index: 5;
}

.brand {
    min-height: 112px;
    padding: 30px 34px 22px;
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
}

.brand-logo {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    display: block;
    object-fit: contain;
}

.brand-text {
    color: var(--accent-dark);
    font-weight: 800;
    font-size: 27px;
    letter-spacing: -.5px;
}

.sidebar-nav {
    padding: 0 30px;
    display: grid;
    gap: 12px;
}

.nav-item {
    position: relative;
    min-height: 48px;
    padding: 0 18px;
    display: flex;
    align-items: center;
    gap: 15px;
    color: #4d4d4d;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    border: 0;
    border-radius: 13px;
    background: transparent;
    width: 100%;
    cursor: pointer;
    text-align: left;
    transition: background .18s ease, color .18s ease, transform .18s ease;
}

.nav-item:hover {
    background: #f7faff;
    color: var(--accent-dark);
}

.nav-item.active {
    background: #f2f5fa;
    color: #4d4d4d;
    font-weight: 800;
}

.nav-item.active::before {
    content: "";
    position: absolute;
    right: 16px;
    top: 10px;
    width: 5px;
    height: 28px;
    border-radius: 999px;
    background: var(--accent);
}

.nav-icon {
    width: 25px;
    flex: 0 0 25px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: currentColor;
    font-weight: 900;
    font-size: 18px;
    line-height: 1;
}

.nav-icon svg {
    width: 25px;
    height: 25px;
    display: block;
    color: currentColor;
    overflow: visible;
}

.nav-icon svg:not(.icon-fill) {
    fill: none;
    stroke: currentColor;
    stroke-width: 2.35;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.nav-icon .icon-fill {
    fill: currentColor;
    stroke: none;
}


/* Sidebar icon style mengikuti referensi gambar kedua */
.sidebar-nav .nav-item,
.sidebar-footer .nav-item {
    color: #4d4d4d;
}

.sidebar-nav .nav-item.active {
    background: #eeeeee;
    color: #4d4d4d;
}

.sidebar-nav .nav-item.active::before {
    right: -2px;
    width: 8px;
    height: 34px;
    top: 7px;
}

.sidebar-footer {
    margin-top: auto;
    min-height: 104px;
    padding: 28px 30px 30px;
    border-top: 1px solid var(--line);
}

.sidebar-footer form {
    margin: 0;
}

.nav-item.logout {
    color: #4d4d4d;
}

.main-panel {
    min-width: 0;
    background: var(--page-bg);
}

.topbar {
    min-height: 118px;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 38px;
    background: var(--page-bg);
}

.mobile-menu-btn {
    display: none;
    border: 0;
    background: var(--accent);
    color: #fff;
    border-radius: 12px;
    width: 42px;
    height: 42px;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(13, 143, 242, .24);
}

.topbar h1 {
    margin: 0;
    color: var(--heading);
    font-size: 26px;
    line-height: 1;
    font-weight: 700;
    letter-spacing: .3px;
}

.dashboard-card {
    width: calc(100% - 76px);
    margin: 0 38px 48px;
    padding: 0;
    background: transparent;
}

.section-title {
    margin: 0 0 22px;
    color: var(--heading);
    font-size: 22px;
    line-height: 1;
    font-weight: 700;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.summary-box {
    min-height: 126px;
    padding: 26px 30px;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    background: var(--panel);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
}

.summary-icon {
    width: 60px;
    height: 60px;
    flex: 0 0 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 900;
    font-size: 24px;
    line-height: 1;
}

.summary-icon.danger {
    color: var(--danger);
    border: 1px solid #ffc6cc;
    background: var(--danger-soft);
}

.summary-icon.process {
    color: var(--process);
    border: 1px solid #c7e7ff;
    background: var(--process-soft);
    font-size: 24px;
}

.summary-icon.success {
    color: #fff;
    background: var(--success);
    border: 14px solid var(--success-soft);
    font-size: 16px;
}

.summary-info {
    min-width: 0;
    display: grid;
    line-height: 1.08;
}

.summary-info span {
    color: var(--muted);
    font-size: 15px;
    font-weight: 700;
}

.summary-info strong {
    margin-top: 6px;
    color: var(--heading);
    font-size: 42px;
    line-height: .95;
    font-weight: 700;
    letter-spacing: 1.5px;
}

.table-wrap {
    width: 100%;
    overflow: auto;
    border: 1px solid var(--line);
    border-radius: var(--radius-xl);
    background: var(--panel);
    box-shadow: var(--shadow-md);
}

.report-table {
    width: 100%;
    min-width: 760px;
    border-collapse: collapse;
    color: var(--text);
}

.report-table thead th {
    padding: 26px 38px 22px;
    background: #f7f9fc;
    color: #4d6078;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
    text-transform: none;
    white-space: nowrap;
    border-bottom: 1px solid var(--line);
}

.report-table tbody td {
    padding: 20px 38px;
    font-size: 14px;
    line-height: 1.25;
    background: #fff;
    border-bottom: 1px solid #edf1f6;
    white-space: nowrap;
}

.report-table tbody tr:nth-child(even) td {
    background: #fbfcfe;
}

.report-table tbody tr:last-child td {
    border-bottom: 0;
}

.report-table tbody tr:hover td {
    background: #f4f9ff;
}

.text-center {
    text-align: center !important;
}

.detail-btn {
    min-width: 76px;
    height: 32px;
    padding: 0 16px;
    border: 1px solid var(--accent);
    border-radius: 7px;
    background: #eef7ff;
    color: var(--accent-dark);
    font-size: 14px;
    font-weight: 500;
    line-height: 30px;
    cursor: pointer;
    transition: background .18s ease, color .18s ease, box-shadow .18s ease;
}

.detail-btn:hover {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 8px 18px rgba(13, 143, 242, .22);
}

.empty-state {
    padding: 42px !important;
    text-align: center;
    color: var(--muted);
    font-size: 14px !important;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 20;
    padding: 20px;
    background: rgba(16, 38, 61, .30);
    display: grid;
    place-items: center;
}

.modal-backdrop[hidden] {
    display: none !important;
}

.modal-card {
    width: min(520px, 96vw);
    max-height: 92vh;
    overflow: hidden;
    border: 1.5px solid #2f2f2f;
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 18px 35px rgba(0, 0, 0, .18);
}

.modal-header {
    height: 58px;
    padding: 0 20px;
    border-bottom: 1.5px solid #2f2f2f;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h2,
.modal-card h2 {
    margin: 0;
    color: #404040;
    font-size: 16px;
    font-weight: 700;
}

.modal-close {
    border: 0;
    background: transparent;
    color: #4a4a4a;
    font-size: 42px;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    padding: 0;
}

.modal-body {
    padding: 34px 32px 36px;
    overflow-y: auto;
    max-height: calc(92vh - 58px);
}

.detail-photo-wrap {
    width: 240px;
    height: 160px;
    margin: 0 auto 20px;
    border: 1.5px solid #2f2f2f;
    border-radius: 18px;
    overflow: hidden;
    background: #f1f1f1;
}

.modal-photo {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.modal-photo-placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    color: #777;
    font-size: 13px;
    font-weight: 600;
}

.detail-panel {
    width: min(100%, 420px);
    margin: 0 auto;
    border: 1.5px solid #2f2f2f;
    border-radius: 20px;
    overflow: hidden;
    background: #f7f7f7;
}

.modal-row {
    min-height: 38px;
    display: grid;
    grid-template-columns: 96px 12px 1fr;
    align-items: center;
    padding: 0 16px;
    background: #f0f0f0;
    color: #555;
    font-size: 14px;
}

.modal-row:nth-child(even) {
    background: #e8e8e8;
}

.modal-label,
.modal-separator,
.modal-value {
    min-width: 0;
}

.modal-label {
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 11px;
    font-style: normal;
    line-height: 1.25;
    color: #6b5700;
    background: #ffe03d;
}

.status-badge.new {
    color: #0b5b9c;
    background: #d8ecff;
}

.status-badge.done {
    color: #0f7433;
    background: #d9f7e3;
}

.status-badge.progress {
    color: #6b5700;
    background: #ffe03d;
}

.modal-row-description {
    min-height: 116px;
    align-items: start;
    padding-top: 14px;
    padding-bottom: 14px;
}

.description-box {
    min-height: 76px;
    padding: 14px;
    border: 1.5px solid #555;
    border-radius: 18px;
    background: #fff;
    color: #555;
    line-height: 1.45;
    white-space: pre-wrap;
}

.loading-line {
    height: 13px;
    margin: 13px 0;
    border-radius: 30px;
    background: linear-gradient(90deg, #edf2f7, #f8fbff, #edf2f7);
}

.loading-line.short {
    width: 60%;
}

@media (max-width: 1024px) {
    :root {
        --sidebar-w: 240px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 820px) {
    .app-shell {
        grid-template-columns: 1fr;
    }

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: min(270px, 88vw);
        transform: translateX(-105%);
        transition: transform .2s ease;
        box-shadow: 12px 0 30px rgba(16, 38, 61, .18);
    }

    body.sidebar-open .sidebar {
        transform: translateX(0);
    }

    .topbar {
        min-height: 86px;
        padding: 0 20px;
    }

    .mobile-menu-btn {
        display: inline-grid;
        place-items: center;
    }

    .dashboard-card {
        width: calc(100% - 32px);
        margin: 0 16px 32px;
    }

    .section-title {
        font-size: 20px;
    }

    .summary-box {
        min-height: 112px;
        padding: 22px;
    }
}


/* Soft font + symmetric spacing override */
.topbar h1,
.section-title,
.report-table thead th,
.summary-info span,
.nav-item,
.brand-text {
    letter-spacing: 0;
}

.nav-item.active {
    font-weight: 700;
}

.summary-info strong {
    font-variant-numeric: tabular-nums;
}

.dashboard-card {
    max-width: none;
}

.summary-grid {
    align-items: stretch;
}

.report-table th,
.report-table td {
    vertical-align: middle;
}

/* Final soft-font corrections */
.topbar h1 {
    font-size: 24px;
    font-weight: 700;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
}

.nav-item {
    font-weight: 500;
}

.nav-item.active {
    font-weight: 600;
}

.summary-info span {
    font-size: 14px;
    font-weight: 600;
    color: #7b8ca5;
}

.summary-info strong {
    font-size: 40px;
    font-weight: 700;
    letter-spacing: 1px;
}

.report-table thead th {
    font-size: 12px;
    font-weight: 600;
}

.report-table tbody td,
.empty-state {
    font-weight: 400;
}

.table-wrap,
.summary-box {
    box-shadow: 0 14px 32px rgba(14, 35, 60, .06);
}


/* =========================
   Halaman Menu Laporan
   ========================= */
.laporan-page {
    padding-top: 0;
}

.laporan-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 20px;
}

.laporan-title {
    margin-bottom: 0;
}

.laporan-search {
    width: min(260px, 100%);
    height: 42px;
    padding: 0 14px 0 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #d8e1ec;
    border-radius: 999px;
    background: #fff;
    box-shadow: 0 8px 20px rgba(14, 35, 60, .04);
}

.laporan-search input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--text);
    font-size: 14px;
}

.laporan-search input::placeholder {
    color: #9aa9ba;
}

.laporan-search svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: #52657a;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
    flex: 0 0 auto;
}

.laporan-table-wrap {
    border-radius: 28px;
}

.laporan-table {
    min-width: 1180px;
}

.laporan-table thead th {
    padding: 24px 18px 20px;
    color: #4b5870;
    font-size: 14px;
    font-weight: 700;
    background: #f7f9fc;
}

.laporan-table tbody td {
    padding: 18px 18px;
    color: #354860;
    font-size: 14px;
}

.laporan-description {
    max-width: 230px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.laporan-status {
    min-width: 116px;
    height: 28px;
    padding: 0 9px 0 12px;
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
}

.laporan-status.progress {
    color: #756000;
    background: #ffd400;
}

.laporan-status.done {
    color: #187c28;
    background: #59ff45;
}

.laporan-status.new {
    color: #095e9c;
    background: #d8ecff;
}

.status-arrow {
    font-size: 12px;
    opacity: .75;
}

@media (max-width: 820px) {
    .laporan-toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .laporan-search {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a href="{{ url('/dashboard') }}" class="brand" aria-label="SiLapor Dashboard">
                <img src="{{ asset('images/logo-silapor.png') }}" alt="Logo SiLapor" class="brand-logo">
                <span class="brand-text">SiLapor</span>
            </a>

            @php($activeMenu = $activeMenu ?? 'dashboard')
            <nav class="sidebar-nav" aria-label="Menu utama">
                <a href="{{ url('/dashboard') }}" class="nav-item {{ $activeMenu === 'dashboard' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img" class="icon-fill">
                            <rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/>
                            <rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/>
                            <rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/>
                            <rect x="13.5" y="13.5" width="7" height="7" rx="1.2"/>
                        </svg>
                    </span><span>Dashboard</span>
                </a>

                <a href="{{ url('/laporan') }}" class="nav-item {{ $activeMenu === 'laporan' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M8.3 3.5h7.4L20.5 8.3v7.4l-4.8 4.8H8.3l-4.8-4.8V8.3L8.3 3.5Z"/>
                            <path d="M12 7.7v6.1"/>
                            <path d="M12 17.1h.01"/>
                        </svg>
                    </span><span>Laporan</span>
                </a>

                <a href="{{ url('/penugasan') }}" class="nav-item {{ $activeMenu === 'penugasan' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M8.2 4.4H5.7a2 2 0 0 0-2 2v13.1a2 2 0 0 0 2 2h12.6a2 2 0 0 0 2-2V6.4a2 2 0 0 0-2-2h-2.5"/>
                            <path d="M8 6.8V4.9a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1.9H8Z"/>
                            <path d="M12 12.2a2.1 2.1 0 1 0 0-4.2 2.1 2.1 0 0 0 0 4.2Z"/>
                            <path d="M8.3 17.3a3.7 3.7 0 0 1 7.4 0"/>
                        </svg>
                    </span><span>Penugasan</span>
                </a>

                <a href="{{ url('/detail-laporan') }}" class="nav-item {{ $activeMenu === 'detail' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <rect x="3.5" y="4.5" width="17" height="15" rx="1.5"/>
                            <path d="M7.3 9h9.4"/>
                            <path d="M7.3 12.2h7.2"/>
                            <path d="M7.3 15.4h5.5"/>
                        </svg>
                    </span><span>Detail Laporan</span>
                </a>

                <a href="{{ url('/laboratorium') }}" class="nav-item {{ $activeMenu === 'laboratorium' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M3.2 5.4h12.5a1.7 1.7 0 0 1 1.7 1.7v7.4a1.7 1.7 0 0 1-1.7 1.7H3.2V5.4Z"/>
                            <path d="M7.2 19.1h7.8"/>
                            <path d="M10.2 16.2v2.9"/>
                            <path d="M19 6.4h1.8v11.2H19z"/>
                            <path d="M19.9 9.2h.01"/>
                            <path d="M19.9 14.8h.01"/>
                        </svg>
                    </span><span>Laboratorium</span>
                </a>

                <a href="{{ url('/profil') }}" class="nav-item {{ $activeMenu === 'profil' ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img">
                            <path d="M12 12.2a3.6 3.6 0 1 0 0-7.2 3.6 3.6 0 0 0 0 7.2Z"/>
                            <path d="M4.5 20.2c.9-4 3.4-6 7.5-6s6.6 2 7.5 6H4.5Z"/>
                        </svg>
                    </span><span>Profil</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                @if(Route::has('logout'))
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-item logout" type="submit">
                            <span class="nav-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" role="img">
                                    <path d="M10 7V5.5a2 2 0 0 1 2-2h6.5a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H12a2 2 0 0 1-2-2V17"/>
                                    <path d="M15 12H3.8"/>
                                    <path d="m7.5 8.2-3.8 3.8 3.8 3.8"/>
                                </svg>
                            </span><span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ url('/logout') }}" class="nav-item logout">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img">
                                <path d="M10 7V5.5a2 2 0 0 1 2-2h6.5a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H12a2 2 0 0 1-2-2V17"/>
                                <path d="M15 12H3.8"/>
                                <path d="m7.5 8.2-3.8 3.8 3.8 3.8"/>
                            </svg>
                        </span><span>Logout</span>
                    </a>
                @endif
            </div>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <button class="mobile-menu-btn" type="button" aria-label="Buka menu">☰</button>
                <h1>{{ $pageTitle ?? 'DASBOARD' }}</h1>
            </header>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        document.querySelector('.mobile-menu-btn')?.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
    </script>
    <script>
(function () {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');

    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        modalContent.innerHTML = '';
    }

    function esc(value) {
        return String(value ?? '-')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderDetail(data) {
        const foto = data.foto
            ? `<img src="${esc(data.foto)}" alt="Foto kerusakan" class="modal-photo">`
            : `<div class="modal-photo-placeholder">Tidak ada foto</div>`;

        const statusClass = esc(data.statusClass || 'new');
        const statusLabel = esc(data.statusLabel || data.status);

        return `
            <div class="detail-photo-wrap">
                ${foto}
            </div>

            <div class="detail-panel">
                <div class="modal-row">
                    <span class="modal-label">ID</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value">${esc(data.id)}</span>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Status</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value"><mark class="status-badge ${statusClass}">${statusLabel}</mark></span>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Pelapor</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value">${esc(data.pelapor)}</span>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Lokasi</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value">${esc(data.lokasi)}</span>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Fasilitas</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value">${esc(data.fasilitas)}</span>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Tgl Lapor</span>
                    <span class="modal-separator">:</span>
                    <span class="modal-value">${esc(data.tanggal)}</span>
                </div>
                <div class="modal-row modal-row-description">
                    <span class="modal-label">Deskripsi</span>
                    <span class="modal-separator">:</span>
                    <div class="description-box">${esc(data.deskripsi)}</div>
                </div>
            </div>
        `;
    }

    document.addEventListener('click', async function (event) {
        const detailButton = event.target.closest('.detail-btn');
        const closeButton = event.target.closest('[data-close-modal]');

        if (closeButton || event.target === modal) {
            closeModal();
            return;
        }

        if (!detailButton || !modal) return;

        const url = detailButton.dataset.detailUrl;
        modal.hidden = false;
        modalContent.innerHTML = '<div class="loading-line"></div><div class="loading-line short"></div><div class="loading-line"></div>';

        if (!url || url === '#') {
            modalContent.innerHTML = '<p>URL detail belum tersedia.</p>';
            return;
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Gagal mengambil detail laporan.');
            const data = await response.json();
            modalContent.innerHTML = renderDetail(data);
        } catch (error) {
            modalContent.innerHTML = '<p>Detail laporan belum bisa ditampilkan. Pastikan route detail pengaduan sudah benar.</p>';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });
})();


(function () {
    const searchInput = document.querySelector('[data-laporan-search]');
    const table = document.querySelector('[data-laporan-table]');

    if (!searchInput || !table) return;

    const rows = Array.from(table.querySelectorAll('[data-laporan-row]'));
    const emptyRow = table.querySelector('[data-empty-row]');

    searchInput.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const text = row.textContent.toLowerCase();
            const isMatch = text.includes(keyword);
            row.hidden = !isMatch;
            if (isMatch) visibleCount += 1;
        });

        if (emptyRow && rows.length > 0) {
            emptyRow.hidden = visibleCount !== 0;
        }
    });
})();

    </script>
</body>
</html>
