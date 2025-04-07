@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#d2ac71] focus:ring focus:ring-[#d2ac71] focus:ring-opacity-20 rounded-md shadow-sm']) !!}>

