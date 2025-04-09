<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Massar - Discover Egypt\'s Hidden Gems')</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    :root {
        --color-primary: #4b4f29;
        --color-primary-dark: #3a3e1e;
        --color-bg: #f6efe6;
        --color-text: #717171;
        --color-light-gray: #f3f3f3;
    }
    
    body {
        background-color: var(--color-bg);
        color: var(--color-text);
        font-family: 'Inter', sans-serif;
    }
    
    .btn-primary {
        background-color: var(--color-primary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s;
        display: inline-block;
        text-align: center;
    }
    
    .btn-primary:hover {
        background-color: var(--color-primary-dark);
    }
    
    .btn-outline {
        border: 1px solid white;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s;
        display: inline-block;
    }
    
    .btn-outline:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .card {
        background-color: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .text-primary {
        color: var(--color-primary);
    }
    
    .bg-primary {
        background-color: var(--color-primary);
    }
    
    .star-rating {
        color: var(--color-primary);
    }
</style>
</head>
<body>

<div class="container mx-auto px-4 py-8">
    <header class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}">
                <img src="/images/massar-logo.png" alt="Massar Logo" class="h-12">
            </a>
            <h1 class="text-2xl font-bold text-[#4b4f29]">Massar</h1>
        </div>
        
        <div class="hidden md:block flex-1 max-w-md mx-4">
            <form action="{{ route('attractions.search') }}" method="GET">
                <div class="relative">
                    <input type="text" name="query" placeholder="Search attractions, locations..." 
                           class="w-full py-2 px-4 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="submit" class="absolute right-3 top-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <nav class="hidden md:flex gap-6">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary">Home</a>
            <a href="{{ route('attractions.index') }}" class="text-gray-600 hover:text-primary">Attractions</a>
            <div class="relative group">
                <a href="#" class="text-gray-600 hover:text-primary flex items-center gap-1">
                    Categories
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block z-10">
                    <div class="py-1">
                        @foreach($categories ?? [] as $slug => $name)
                            <a href="{{ route('attractions.category', $slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-primary flex items-center gap-1">
                My Trip
                @php
                    $cartCount = App\Http\Controllers\CartController::getCartCount();
                @endphp
                @if($cartCount > 0)
                    <span class="bg-primary text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-primary">Profile</a>

        </nav>
    
        <div class="flex items-center gap-4">
            @auth
                <span class="text-gray-600">Welcome, {{ Auth::user()->firstname }}</span>
                <!-- Profile Dropdown Link -->
               
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-primary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary">Register</a>
            @endauth
        </div>
    </header>
    

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
    
    <footer class="mt-12 py-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('images/massar-logo.png') }}" alt="Massar Logo" class="h-12">
                    <h2 class="text-xl font-bold text-[#4b4f29]">Massar</h2>
                </div>
                <p class="text-sm text-gray-500 mb-4">Discover the wonders of Egypt with our curated attractions and experiences.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-primary hover:text-primary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-primary hover:text-primary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-primary hover:text-primary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h3 class="font-bold text-lg mb-4">Categories</h3>
                <ul class="space-y-2">
                    @foreach($categories ?? [] as $slug => $name)
                        <li>
                            <a href="{{ route('attractions.category', $slug) }}" class="text-gray-600 hover:text-primary">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div>
                <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-600 hover:text-primary">Home</a></li>
                    <li><a href="{{ route('attractions.index') }}" class="text-gray-600 hover:text-primary">All Attractions</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-primary">My Trip Plan</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-primary">About Us</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-primary">Contact</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-primary">FAQs</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-bold text-lg mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>123 Tourism Street, Cairo, Egypt</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>info@massar.com</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>+20 123 456 7890</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-6 flex flex-col md:flex-row justify-between items-center">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Massar. All rights reserved.</p>
            <div class="flex gap-4 mt-4 md:mt-0">
                <a href="#" class="text-sm text-gray-500 hover:text-primary">Privacy Policy</a>
                <a href="#" class="text-sm text-gray-500 hover:text-primary">Terms of Service</a>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
