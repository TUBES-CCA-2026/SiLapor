@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-2 px-1">
        <!-- Mobile View (Previous / Next buttons) -->
        <div class="flex justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed select-none border border-slate-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('Sebelumnya') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-[#29ABE2] bg-[#E8F7FC] border border-[#29ABE2]/30 rounded-xl hover:bg-[#29ABE2] hover:text-white transition-all duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('Sebelumnya') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-[#29ABE2] bg-[#E8F7FC] border border-[#29ABE2]/30 rounded-xl hover:bg-[#29ABE2] hover:text-white transition-all duration-150 shadow-sm">
                    {{ __('Berikutnya') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed select-none border border-slate-200">
                    {{ __('Berikutnya') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        <!-- Desktop View -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-500">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-extrabold text-[#2C3E50]">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-extrabold text-[#2C3E50]">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-extrabold text-[#2C3E50]">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1.5 p-1 bg-white border border-slate-200/80 rounded-2xl shadow-sm">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-300 bg-slate-50 rounded-xl cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-600 bg-white hover:bg-[#E8F7FC] hover:text-[#29ABE2] border border-transparent hover:border-[#29ABE2]/30 rounded-xl transition-all duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-400 select-none">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-extrabold text-white bg-[#29ABE2] rounded-xl shadow-md shadow-[#29ABE2]/30 select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-600 bg-white hover:bg-[#E8F7FC] hover:text-[#29ABE2] border border-transparent hover:border-[#29ABE2]/30 rounded-xl transition-all duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-600 bg-white hover:bg-[#E8F7FC] hover:text-[#29ABE2] border border-transparent hover:border-[#29ABE2]/30 rounded-xl transition-all duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-300 bg-slate-50 rounded-xl cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
