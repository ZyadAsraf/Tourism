<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Massar - Discover Egypt\'s Hidden Gems')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Reset and base styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --color-gold: #D4AF37;
            --color-gold-dark: #B38E2E;
            --color-bg: #f3f1ed;
            --color-text: #222;
            --color-light-gray: #f3f3f3;
        }

        body {
            font-family: 'Roboto', serif;
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.6;
            font-size: 20px;
            min-height: 100vh;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
            color: #111;
        }

        /* Navigation Bar */
        .navbar {
            width: 100vw;
            max-width: 100%;
            background: var(--color-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 2vw 0.5rem 2vw;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
            z-index: 10;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .nav-logo {
            font-family: 'Playfair Display', serif;
            color: var(--color-gold);
            font-size: 2rem;
            font-weight: 700;
            margin-right: 0.7rem;
        }

        .nav-search {
            display: flex;
            align-items: center;
        }

        .nav-search input {
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 0.4rem 1.2rem;
            font-size: 1rem;
            width: 420px;
            outline: none;
            background: #fafafa;
            transition: border 0.2s;
        }

        .nav-search input:focus {
            border: 1.5px solid var(--color-gold);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            list-style: none;
            margin: 0 1.5rem 0 1.5rem;
            padding: 0;
        }

        .nav-links li a {
            text-decoration: none;
            color: var(--color-text);
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            transition: color 0.2s;
            padding: 0.2rem 0.5rem;
        }

        .nav-links li a:hover {
            color: var(--color-gold);
        }

        .nav-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-login, .btn-register {
            background: var(--color-gold);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.3rem 1.1rem;
            font-size: 1rem;
            font-family: 'Playfair Display', serif;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login {
            background: var(--color-gold);
        }

        .btn-register {
            background: #e6d3a3;
            color: #7a6a3a;
            margin-left: 0.2rem;
        }

        .btn-login:hover {
            background: var(--color-gold-dark);
        }

        .btn-register:hover {
            background: var(--color-gold);
            color: #fff;
        }

        /* Responsive Styles */
        @media (max-width: 1100px) {
            .navbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
                padding: 0.5rem 1vw;
            }
            .nav-links {
                margin: 0.5rem 0;
                gap: 0.7rem;
            }
        }

        @media (max-width: 800px) {
            .navbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
                padding: 0.5rem 1vw;
            }
            .nav-links {
                flex-wrap: wrap;
                gap: 0.5rem;
                margin: 0.5rem 0;
            }
            .nav-search input {
                width: 120px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 600px) {
            .nav-logo {
                font-size: 1.1rem;
            }
            .nav-search input {
                width: 70px;
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
            .nav-links li a {
                font-size: 0.8rem;
                padding: 0.1rem 0.2rem;
            }
            .btn-login, .btn-register {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
        }

        /* Animation for navbar */
        .fade-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: opacity 1s cubic-bezier(0.4,0,0.2,1), transform 1s cubic-bezier(0.4,0,0.2,1);
        }

        .navbar {
            opacity: 0;
            transform: translateY(-20px);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="{{ route('home') }}" class="nav-logo">Massar</a>
            <form class="nav-search" action="{{ route('attractions.search') }}" method="GET">
                <input type="text" name="query" placeholder="Search attractions, locations..." aria-label="Search">
            </form>
        </div>
        <ul class="nav-links">
            <!-- Home -->
            <li><a href="{{ route('home') }}">Home</a></li>
            <!-- Attractions -->
            <li><a href="{{ route('attractions.index') }}">Attractions</a></li>
            <!-- Categories Dropdown -->
            <li>
                <a href="#" class="flex items-center gap-1">
                    Categories
                    <svg class="nav-arrow" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 8L10 13L15 8" stroke="#444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block z-10">
                    <div class="py-1">
                        @foreach($categories ?? [] as $slug => $name)
                            <a href="{{ route('attractions.category', $slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>                
            </li>
            <!-- My Trip / Cart -->
            <li><a href="{{ route('cart.index') }}">My Trip
            @php
                    $cartCount = App\Http\Controllers\CartController::getCartCount();
                @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-3 bg-primary text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                        {{ $cartCount }}
                    </span>
                @endif
            </a></li>
            <!-- Profile -->
            <li><a href="{{ route('profile.edit') }}">Profile</a></li>

            @auth
            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
                <!-- Admin Dashboard (visible only to admin) -->
                <a href="{{ url('/admin') }}" >Admin Dashboard</a>
            @endif
            @endauth
        </ul>

        <div class="nav-actions">
            @auth
                <span class="text-gray-600">Welcome, {{ Auth::user()->firstname }}</span>
                <!-- Profile Dropdown Link -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-login">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">Sign In</a>
                <a href="{{ route('register') }}" class="btn-register">Register</a>
            @endauth
        </div>
        
    </nav>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @yield('content')

    <!-- Footer -->
    <footer>
    <div class="footer-content">
        <div class="footer-brand">
            <div class="footer-logo">Massar</div>
            <p>Your journey begins with us.<br>Explore the world with confidence and comfort.</p>
        </div>
        <div class="footer-categories">
            <div class="footer-categories-title">Categories</div>
            <ul>
                @foreach($categories ?? [] as $slug => $name)
                    <li><a href="{{ route('attractions.category', $slug) }}">{{ $name }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="#" >About Us</a></li>
                <li><a href="{{ route('attractions.index') }}">Attractions</a></li>
                <li><a href="{{ route('cart.index') }}">My Trip</a></li>
                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p>Email: info@massar.com<br>Phone: +1 (555) 123-4567</p>
            <div class="footer-social">
                <a href="#" aria-label="Facebook">
                    <svg width="40" height="40" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0"/>
                    </svg>
                </a>
                <a href="#" aria-label="Twitter">
                    <svg width="40" height="40" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 0 0-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.057 0 14.009-7.496 14.009-13.986 0-.21-.005-.423-.015-.633A9.936 9.936 0 0 0 24 4.557z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Instagram">
                    <svg width="40" height="40" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608.974-.974 2.241-1.246 3.608-1.308C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.808 2.256 6.088 2.243 6.497 2.243 12c0 5.503.013 5.912.072 7.192.059 1.276.353 2.449 1.32 3.416.967.967 2.14 1.261 3.416 1.32 1.28.059 1.689.072 7.192.072s5.912-.013 7.192-.072c1.276-.059 2.449-.353 3.416-1.32.967-.967 1.261-2.14 1.32-3.416.059-1.28.072-1.689.072-7.192s-.013-5.912-.072-7.192c-.059-1.276-.353-2.449-1.32-3.416C21.449.425 20.276.131 19 .072 17.72.013 17.311 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                    </svg>
                </a>
                <a href="#" aria-label="LinkedIn">
                    <svg width="40" height="40" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 2.76 2.24 5 5 5h14c2.76 0 5-2.24 5-5v-14c0-2.76-2.24-5-5-5zm-11.75 20h-3v-10h3v10zm-1.5-11.25c-.966 0-1.75-.784-1.75-1.75s.784-1.75 1.75-1.75 1.75.784 1.75 1.75-.784 1.75-1.75 1.75zm15.25 11.25h-3v-5.5c0-1.381-.028-3.156-1.922-3.156-1.922 0-2.218 1.5-2.218 3.051v5.605h-3v-10h2.885v1.367h.041c.402-.762 1.384-1.563 2.848-1.563 3.045 0 3.607 2.005 3.607 4.614v5.582z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>@ 2025 Massar, All rights reserved,</p>
    </div>
</footer>

    <script>
        // Fade in navbar on page load
        window.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.classList.add('fade-in');
            }
        });
    </script>
</body>
@stack('styles')
@stack('scripts')
</html>