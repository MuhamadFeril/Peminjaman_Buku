<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
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

        /* Alert auto-dismiss helper */
        .alert-fade-out { transition: opacity .4s ease, transform .4s ease; }
        .alert-hidden { opacity: 0; transform: translateY(-8px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'App') }}</a>
            <div class="collapse navbar-collapse">
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
                                @if (\Illuminate\Support\Facades\Route::has('profile.show'))
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
                    @endguest
                </ul>
        </div>
    </nav>

    <main class="container">
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
</body>
</html>
