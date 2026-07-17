@php
    $sidebarUser = $user ?? auth()->user();
    $activeMenu = $activeMenu ?? 'dashboard';
    $menuItems = \App\Support\SidebarMenu::forRole($sidebarUser?->role);
    $routeSafe = $routeSafe ?? function (string $name, string $fallback = '#') {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };
@endphp

<aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full sidebar-desktop md:sticky md:top-0 md:h-screen rounded-r-[36px] md:rounded-r-none shadow-lg md:shadow-none shrink-0">
    <div class="p-8 flex-1 flex flex-col overflow-y-auto custom-scrollbar">
        <a href="{{ $routeSafe('dashboard') }}" class="flex items-center gap-3 px-4 text-decoration-none">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-silapor-500 to-silapor-700 flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-square-poll-vertical text-xl"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-silapor-500 to-silapor-dark bg-clip-text text-transparent">
                SiLapor
            </span>
        </a>

        <nav class="mt-10 space-y-7">
            @foreach($menuItems as [$key, $label, $icon, $url])
                @if($activeMenu === $key)
                    <a href="{{ $url }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl bg-gray-100 text-gray-800 font-bold text-sm group transition-all">
                        <div class="flex items-center gap-3.5">
                            <i class="{{ $icon }} text-lg text-silapor-500"></i>
                            <span>{{ $label }}</span>
                        </div>
                        <div class="w-1.5 h-6 rounded-full bg-silapor-500"></div>
                    </a>
                @else
                    <a href="{{ $url }}" class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-semibold text-sm transition-all">
                        <i class="{{ $icon }} text-lg"></i>
                        <span>{{ $label }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    <div class="mt-auto p-8 border-t border-gray-100 bg-white rounded-br-[36px] md:rounded-br-none">
        <a href="#" data-logout-link class="flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 font-semibold text-sm transition-all">
            <i class="fa-solid fa-right-from-bracket text-lg"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ $routeSafe('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden" onclick="toggleSidebar()"></div>
