<<<<<<< Updated upstream
<x-guest-layout>
=======
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-600">Create an Account</h2>
        <p class="text-sm text-gray-500 mt-1">Join TravelEgypt to discover amazing attractions</p>
    </div>

>>>>>>> Stashed changes
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="username" :value="__('username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="firstname" :value="__('firstname')" />
            <x-text-input id="firstname" class="block mt-1 w-full" type="text" name="firstname" :value="old('firstname')" required autofocus autocomplete="firstname" />
            <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="lastname" :value="__('lastname')" />
            <x-text-input id="lastname" class="block mt-1 w-full" type="text" name="lastname" :value="old('lastname')" required autofocus autocomplete="lastname" />
            <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

<<<<<<< Updated upstream
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
=======
        <!-- Terms and Conditions -->
        <div class="mt-4">
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-[#d2ac71] shadow-sm focus:ring-[#d2ac71]" name="terms" required>
                <span class="ms-2 text-sm text-gray-600">I agree to the <a href="#" class="text-[#d2ac71] hover:text-[#c19c61]">Terms and Conditions</a></span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-[#d2ac71] hover:text-[#c19c61] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d2ac71]" href="{{ route('login') }}">
>>>>>>> Stashed changes
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
<<<<<<< Updated upstream
</x-guest-layout>
=======
</div>
@endsection
>>>>>>> Stashed changes
