<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="UserName" :value="__('UserName')" />
            <x-text-input id="UserName" class="block mt-1 w-full" type="text" name="UserName" :value="old('UserName')" required autofocus autocomplete="UserName" />
            <x-input-error :messages="$errors->get('UserName')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="FirstName" :value="__('FirstName')" />
            <x-text-input id="FirstName" class="block mt-1 w-full" type="text" name="FirstName" :value="old('FirstName')" required autofocus autocomplete="FirstName" />
            <x-input-error :messages="$errors->get('FirstName')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="LastName" :value="__('LastName')" />
            <x-text-input id="LastName" class="block mt-1 w-full" type="text" name="LastName" :value="old('LastName')" required autofocus autocomplete="LastName" />
            <x-input-error :messages="$errors->get('LastName')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="birthdate" :value="__('birthdate')" />
            <x-text-input id="birthdate" class="block mt-1 w-full" type="date" name="birthdate" :value="old('birthdate')" required autofocus autocomplete="birthdate" />
            <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="PhoneNumber" :value="__('PhoneNumber')" />
            <x-text-input id="PhoneNumber" class="block mt-1 w-full" type="text" name="PhoneNumber" :value="old('PhoneNumber')" required autofocus autocomplete="PhoneNumber" />
            <x-input-error :messages="$errors->get('PhoneNumber')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="Email" :value="__('Email')" />
            <x-text-input id="Email" class="block mt-1 w-full" type="Email" name="Email" :value="old('Email')" required autocomplete="Email" />
            <x-input-error :messages="$errors->get('Email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
