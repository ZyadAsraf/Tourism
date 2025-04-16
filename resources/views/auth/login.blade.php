<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-600">Welcome Back</h2>
        <p class="text-sm text-gray-500 mt-1">Sign in to your account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#d2ac71] shadow-sm focus:ring-[#d2ac71]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-[#d2ac71] hover:text-[#c19c61] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d2ac71]" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
        
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">Don't have an account? 
                <a href="{{ route('register') }}" class="text-[#d2ac71] hover:text-[#c19c61] font-medium">
                    Register now
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

