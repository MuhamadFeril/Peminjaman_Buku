<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Perpusku'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Improve default text contrast across the app */
        body {
            color: #222 !important;
            background-color: #f8f9fa;
        }
        main.container {
            color: #222 !important;
        }
        .navbar-brand, .nav-link, .dropdown-item {
            color: #222 !important;
        }
        /* Ensure small muted text is still readable */
        .text-muted, .text-gray-500 { color: #6c757d !important; }
        /* --- Animations --- */
        @keyframes subtleFadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up {
            animation: subtleFadeUp 420ms ease both;
        }

        @keyframes floatUp {
            0% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
            100% { transform: translateY(0); }
        }
        .btn-raise {
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .btn-raise:hover, .btn-raise:focus {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(33,37,41,0.08);
        }

        /* Table row entrance */
        .table tbody tr { transform-origin: left top; }
        .table tbody tr.fade-up-row { animation: subtleFadeUp .45s ease both; }

        /* Subtle focus for inputs */
        input.form-control:focus, select.form-control:focus {
            box-shadow: 0 4px 12px rgba(50,115,220,0.08);
            border-color: #6ea8fe;
        }

        /* Mobile tweaks */
        @media (max-width: 576px) {
            main.container { padding: 1.2rem; font-size:1.05rem; }
            .navbar .navbar-brand { font-size: 1.15rem; }
            .mobile-form { padding: 0.9rem; background: #fff; border-radius: 14px; box-shadow: 0 8px 22px rgba(16,24,40,0.06); }
            .mobile-form .form-control { padding: 14px 16px; font-size: 1.05rem; border-radius:8px; }
            .mobile-form .form-label { font-size: 1.03rem; font-weight:600; }
            .mobile-form .btn { width: 100%; padding: 14px 16px; font-size: 1.05rem; border-radius:12px; }
            .mobile-form .small { font-size: 0.9rem; }
            .table-responsive { font-size: 1rem; }
            .navbar-nav .nav-link { padding-left: .6rem; padding-right: .6rem; font-size:1rem }
            /* Increase tap targets for nav toggler */
            .navbar-toggler { padding: .5rem .75rem; border-radius:10px }
        }

        /* Enhanced mobile layout for small devices: convert tables into stacked cards for readability */
        @media (max-width: 768px) {
            /* Make container padding tighter on small screens */
            main.container { padding-left: 0.8rem; padding-right: 0.8rem; font-size:1.02rem }

            /* Dashboard / metric cards — full-width and larger spacing */
            .dashboard-card, .metric-card {
                width: 100%;
                padding: 14px 16px;
                margin-bottom: 12px;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(16,24,40,0.04);
                background: #fff;
            }

            /* Make action buttons touch-friendly and larger */
            .btn { padding: 12px 16px; font-size: 1.05rem; border-radius: 12px; }

            /* Improve table readability by converting rows to card blocks */
            table.table { border-collapse: separate; }
            table.table thead { display: none; }
            table.table tbody { display: block; }
            table.table tbody tr { display: block; margin-bottom: 10px; background: #fff; border-radius: 10px; box-shadow: 0 6px 18px rgba(15,23,42,.04); padding: 10px; }
            table.table td { display: block; padding: 8px 10px; border: none; font-size:1rem }
            table.table td:before { content: attr(data-label); font-weight:600; display:inline-block; width:140px; color:#6b7280; }

            /* Make small images responsive inside cards */
            .book-thumbnail, .img-fluid { width: 100%; height: auto; object-fit:cover; border-radius:8px; }

            /* Make pagination centered and easier to tap */
            .pagination { justify-content: center; }

            /* Slightly larger badges and small visual elements */
            .badge { font-size: 0.95rem; padding: .45em .6em; }

            /* Hide non-essential large side paddings */
            .table-responsive { padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }

        /* Alert auto-dismiss helper */
        .alert-fade-out { transition: opacity .4s ease, transform .4s ease; }
        .alert-hidden { opacity: 0; transform: translateY(-8px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="gap:10px">
                <span class="logo-icon d-inline-block" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;background:#2b6cb0;border-radius:8px;box-shadow:0 6px 14px rgba(43,108,176,0.12)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6v12a1 1 0 0 0 1 1h12"/>
                        <path d="M21 6v12a1 1 0 0 1-1 1H9"/>
                        <path d="M7 6V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
                    </svg>
                </span>
                <span class="ms-2" style="font-weight:600">{{ config('app.name', 'Perpusku') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('buku.index') }}">Buku</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('anggota.index') }}">Anggota</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(!empty(auth()->user()->profile_photo))
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="avatar" class="rounded-circle me-2" style="width:34px;height:34px;object-fit:cover">
                                @else
                                    <div class="rounded-circle bg-secondary me-2 d-inline-block" style="width:34px;height:34px;line-height:34px;text-align:center;color:#fff;font-weight:600">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                                @endif
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                @if (Route::has('profile.show'))
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profil Saya</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!-- standalone logout buttons removed; logout remains inside dropdown -->
                    @endguest
                </ul>
        </div>
    </nav>

    <main class="container">
        @include('partials.alerts')
        @include('partials.toast')
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add fade-up class to main container on load
        document.addEventListener('DOMContentLoaded', function () {
            var main = document.querySelector('main.container');
            if (main) main.classList.add('fade-up');

            // Add row animation to table rows (staggered)
            var rows = document.querySelectorAll('table.table tbody tr');
            rows.forEach(function(r, i) {
                r.style.animationDelay = (i * 35) + 'ms';
                r.classList.add('fade-up-row');
            });

            // Auto-dismiss alerts after 3s
            setTimeout(function(){
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(a){
                    a.classList.add('alert-fade-out');
                    a.classList.add('alert-hidden');
                    setTimeout(function(){ if(a.parentNode) a.parentNode.removeChild(a); }, 450);
                });
            }, 3000);
        });
    </script>
        <!-- Global loader -->
        <div id="global-loader" class="d-none" aria-hidden="true">
            <div class="loader-backdrop"></div>
            <div class="loader-content" role="status" aria-live="polite">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        </div>
        <style>
            #global-loader { position: fixed; inset: 0; z-index: 2200; display:flex; align-items:center; justify-content:center; }
            #global-loader .loader-backdrop { position:absolute; inset:0; background: rgba(0,0,0,0.36); }
            #global-loader .loader-content { position:relative; z-index:2201; }
            #global-loader.d-none { display:none; }
            @media (prefers-reduced-motion: reduce) {
                #global-loader .spinner-border { animation: none !important; }
            }
        </style>
        <script>
            (function(){
                const loader = document.getElementById('global-loader');
                function showLoader(){ if(!loader) return; loader.classList.remove('d-none'); loader.setAttribute('aria-hidden','false'); }
                function hideLoader(){ if(!loader) return; loader.classList.add('d-none'); loader.setAttribute('aria-hidden','true'); }

                // Hide loader on initial load
                window.addEventListener('load', hideLoader);
                window.addEventListener('pageshow', function(e){ if(e.persisted) hideLoader(); });

                // Show loader for internal navigation links
                document.addEventListener('click', function(ev){
                    const a = ev.target.closest && ev.target.closest('a');
                    if(!a || !a.href) return;
                    // ignore anchors, external links, mailto, tel, downloads, or links opening new tab
                    const href = a.getAttribute('href') || '';
                    if(href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                    try{
                        const url = new URL(a.href, location.href);
                        if(url.origin !== location.origin) return; // external
                    }catch(e){ return; }
                    if(a.target && a.target !== '' && a.target !== '_self') return;
                    if(a.hasAttribute('data-no-loader')) return;
                    showLoader();
                }, true);

                // Show loader on form submit
                document.addEventListener('submit', function(ev){
                    const form = ev.target;
                    if(form && form.closest) {
                        if(form.hasAttribute('data-no-loader')) return;
                        showLoader();
                    }
                }, true);

                // Wrap fetch to show loader during requests
                if(window.fetch){
                    const rawFetch = window.fetch.bind(window);
                    window.fetch = function(){
                        showLoader();
                        return rawFetch.apply(this, arguments).finally(function(){ hideLoader(); });
                    };
                }

                // Expose for debugging if needed
                window.__globalLoader = { show: showLoader, hide: hideLoader };
            })();
        </script>
</body>
</html>
