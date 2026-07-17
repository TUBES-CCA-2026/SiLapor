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
            background: #29ABE2;
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }
        .global-notification-button:hover { background: #1B8DC4; }

        .logout-confirm-actions {
            display: flex;
            justify-content: center;
            gap: .75rem;
            margin-top: 1.25rem;
        }
        .logout-confirm-actions button {
            min-width: 96px;
            border: 0;
            border-radius: .95rem;
            padding: .75rem 1rem;
            font-weight: 800;
            cursor: pointer;
        }
        .logout-confirm-cancel { background: #E5E7EB; color: #374151; }
        .logout-confirm-yes { background: #DC2626; color: #fff; }
        .logout-confirm-yes:hover { background: #B91C1C; }

        /* Custom File Input Styling */
        input[type="file"].form-control {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
        }
        input[type="file"].form-control::file-selector-button {
            border: none;
            background: #E8F7FC;
            color: #29ABE2;
            padding: 0.4rem 0.85rem;
            border-radius: 0.6rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            margin-right: 0.75rem;
            font-size: 0.8125rem;
        }
        input[type="file"].form-control::file-selector-button:hover {
            background: #29ABE2;
            color: #fff;
        }
    </style>
    @stack('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    @auth
   
    @endauth

    <div id="app-content">
        @yield('content')
    </div>
    

    @php
        $globalNotificationsSuppressed = trim($__env->yieldContent('suppress_global_notification')) === 'true';
        $globalNotificationType = session('success') ? 'success' : ((session('error') || $errors->any()) ? 'error' : null);
        $globalNotificationMessage = session('success') ?: (session('error') ?: ($errors->any() ? $errors->first() : null));
        $globalNotificationTitle = $globalNotificationType === 'success' ? 'Berhasil' : 'Gagal';
    @endphp

    @if(! $globalNotificationsSuppressed && $globalNotificationType && $globalNotificationMessage)
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
                </div>
            </div>
        </div>
    @endif


<script>
(function () {
    function findLogoutForm() {
        return document.getElementById('logout-form') || document.querySelector('form[action$="/logout"]');
    }

    function confirmLogout(form) {
        if (!form) return;

        if (window.Swal) {
            Swal.fire({
                title: 'Keluar dari akun?',
                text: 'Pilih Ya untuk logout atau Tidak untuk tetap di halaman ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
            return;
        }

        if (confirm('Keluar dari akun?')) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-logout-link], a[onclick*="logout-form"]');
        if (!trigger) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        confirmLogout(findLogoutForm());
    }, true);

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.matches('#logout-form, form[action$="/logout"]')) return;
        if (form.dataset.confirmed === '1') return;

        event.preventDefault();
        confirmLogout(form);
    }, true);
})();
</script>

<script>
(function () {
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.hasAttribute('data-confirm-delete')) return;
        if (form.dataset.confirmed === '1') return;

        event.preventDefault();

        const title = form.dataset.confirmTitle || 'Hapus data ini?';
        const text = form.dataset.confirmText || 'Data yang dihapus tidak dapat dikembalikan.';
        const confirmYes = form.dataset.confirmYes || 'Ya, Hapus';
        const confirmNo = form.dataset.confirmNo || 'Batal';

        if (window.Swal) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: confirmYes,
                cancelButtonText: confirmNo,
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        } else {
            if (confirm(title + '\n' + text)) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        }
    }, true);
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Searchable Select Component Logic
    document.querySelectorAll('.custom-searchable-select').forEach(function (wrapper) {
        const trigger = wrapper.querySelector('.searchable-select-trigger');
        const dropdown = wrapper.querySelector('.searchable-select-dropdown');
        const searchInput = wrapper.querySelector('.searchable-select-search');
        const optionsList = wrapper.querySelector('.searchable-select-options');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const options = optionsList ? optionsList.querySelectorAll('li') : [];
 
        if (!trigger || !dropdown) return;
 
        // Setup setter wrapper to automatically sync visible trigger input with hidden input changes
        if (hiddenInput && options.length > 0) {
            const descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
            Object.defineProperty(hiddenInput, 'value', {
                get: function() {
                    return descriptor.get.call(this);
                },
                set: function(val) {
                    descriptor.set.call(this, val);
                    const option = Array.from(options).find(opt => opt.getAttribute('data-value') === String(val));
                    trigger.value = option ? option.textContent.trim() : '';
                }
            });
 
            // Set initial trigger text
            const initialValue = hiddenInput.value;
            const initialOption = Array.from(options).find(opt => opt.getAttribute('data-value') === String(initialValue));
            if (initialOption) {
                trigger.value = initialOption.textContent.trim();
            }
        }
 
        // Click trigger to open dropdown
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.searchable-select-dropdown, .searchable-multiselect-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            
            const isHidden = dropdown.classList.contains('hidden');
            if (isHidden) {
                dropdown.classList.remove('hidden');
                
                // Teleport to body to prevent clipping
                if (dropdown.parentElement !== document.body) {
                    document.body.appendChild(dropdown);
                }
                
                // Reposition absolutely
                const rect = trigger.getBoundingClientRect();
                dropdown.style.position = 'absolute';
                dropdown.style.top = (rect.bottom + window.scrollY) + 'px';
                dropdown.style.left = (rect.left + window.scrollX) + 'px';
                dropdown.style.width = Math.max(rect.width, 180) + 'px';
                dropdown.style.zIndex = '9999';
                
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                options.forEach(opt => opt.style.display = '');
            } else {
                dropdown.classList.add('hidden');
            }
        });
 
        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
 
        // Filtering search inputs
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                options.forEach(function (option) {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
 
        // Select an option
        options.forEach(function (option) {
            option.addEventListener('click', function () {
                const val = option.getAttribute('data-value');
                const text = option.textContent.trim();
                const oldVal = hiddenInput.value;
                if (oldVal !== val) {
                    hiddenInput.value = val;
                    trigger.value = val ? text : '';
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                dropdown.classList.add('hidden');
            });
        });
    });
 
    // Searchable Multiselect Component Logic
    document.querySelectorAll('.custom-searchable-multiselect').forEach(function (wrapper) {
        const trigger = wrapper.querySelector('.searchable-multiselect-trigger');
        const dropdown = wrapper.querySelector('.searchable-multiselect-dropdown');
        const searchInput = wrapper.querySelector('.searchable-multiselect-search');
        const optionsList = wrapper.querySelector('.searchable-multiselect-options');
        const checkboxes = optionsList ? optionsList.querySelectorAll('.searchable-multiselect-checkbox') : [];
        const items = optionsList ? optionsList.querySelectorAll('li') : [];
 
        if (!trigger || !dropdown) return;
 
        function updateTriggerText() {
            const selectedNames = [];
            checkboxes.forEach(function (cb) {
                if (cb.checked) {
                    selectedNames.push(cb.getAttribute('data-name'));
                }
            });
            trigger.value = selectedNames.length > 0 ? selectedNames.join(', ') : '';
        }
 
        updateTriggerText();
 
        // Toggle dropdown
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.searchable-select-dropdown, .searchable-multiselect-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            
            const isHidden = dropdown.classList.contains('hidden');
            if (isHidden) {
                dropdown.classList.remove('hidden');
                
                // Teleport to body to prevent clipping
                if (dropdown.parentElement !== document.body) {
                    document.body.appendChild(dropdown);
                }
                
                // Reposition absolutely
                const rect = trigger.getBoundingClientRect();
                dropdown.style.position = 'absolute';
                dropdown.style.top = (rect.bottom + window.scrollY) + 'px';
                dropdown.style.left = (rect.left + window.scrollX) + 'px';
                dropdown.style.width = Math.max(rect.width, 180) + 'px';
                dropdown.style.zIndex = '9999';
                
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                items.forEach(li => li.style.display = '');
            } else {
                dropdown.classList.add('hidden');
            }
        });
 
        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
 
        // Filtering search
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                items.forEach(function (li) {
                    const text = li.textContent.toLowerCase();
                    li.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
 
        // Clicking list item toggles checkbox
        items.forEach(function (li) {
            li.addEventListener('click', function (e) {
                if (e.target.tagName !== 'INPUT') {
                    const cb = li.querySelector('.searchable-multiselect-checkbox');
                    if (cb) {
                        cb.checked = !cb.checked;
                        updateTriggerText();
                    }
                }
            });
        });
 
        // Checking checkbox directly updates trigger text
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTriggerText);
        });
    });
 
    // Click outside closes dropdowns
    document.addEventListener('click', function () {
        document.querySelectorAll('.searchable-select-dropdown, .searchable-multiselect-dropdown').forEach(d => d.classList.add('hidden'));
    });
});
</script>

    @stack('scripts')
</body>
</html>
