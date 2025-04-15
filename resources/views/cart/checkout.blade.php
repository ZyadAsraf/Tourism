@extends('layouts.app')

@section('title', 'Checkout - Massar')

@section('content')
    <div class="mb-6">
        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span>Back to Trip Plan</span>
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-600 mb-2">Checkout</h1>
        <p class="text-gray-500">Complete your booking to confirm your Egyptian adventure</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('cart.store') }}" id="checkout-form" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="total" value="{{ $total }}">
                <input type="hidden" name="stripeToken" id="stripe-token">
                <input type="hidden" name="phone" id="phone-input">

                <div class="card p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-600">Contact Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">First Name</label>
                            <input type="text" name="first_name" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Last Name</label>
                            <input type="text" name="last_name" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Email</label>
                            <input type="email" name="email" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Phone</label>
                            <input type="tel" name="phone" id="phone" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-600 mb-1">Special Requests (Optional)</label>
                        <textarea name="special_requests" rows="3" class="w-full p-2 border border-gray-200 rounded-md" placeholder="Any special requirements or requests..."></textarea>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-600">Payment Method</h2>

                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary transition-colors relative">
                            <input type="radio" name="payment_method" id="credit_card" value="credit_card" class="absolute top-4 right-4">
                            <label for="credit_card" class="flex items-start cursor-pointer">
                                <div class="flex-shrink-0 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-700">Credit/Debit Card</h3>
                                    <p class="text-sm text-gray-500">Pay securely with your credit or debit card</p>

                                    <div class="mt-4" id="card-details">
                                        <label for="card-element" class="block text-gray-700 mb-1">Card Details</label>
                                        <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white"></div>
                                        <div id="card-errors" class="text-red-500 text-sm mt-2"></div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary transition-colors relative">
                            <input type="radio" name="payment_method" id="paypal" value="paypal" class="absolute top-4 right-4">
                            <label for="paypal" class="flex items-start cursor-pointer">
                                <div class="flex-shrink-0 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.384a.64.64 0 0 1 .632-.537h6.012c2.658 0 4.53.714 5.272 2.149.651 1.244.654 2.81.009 4.354-.766 1.826-2.2 2.943-4.126 3.253a.636.636 0 0 0 .096 1.254h1.078a.64.64 0 0 1 .656.739l-.86 4.296a.639.639 0 0 1-.631.537h-4.156a.64.64 0 0 1-.632-.537l-.228-1.19zm9.826-16.468c-.684-1.296-2.332-1.943-4.864-1.943H6.012a1.92 1.92 0 0 0-1.898 1.61L.998 20.028a1.92 1.92 0 0 0 1.898 2.23h4.604a1.92 1.92 0 0 0 1.898-1.61l.228-1.158h4.157a1.92 1.92 0 0 0 1.898-1.61l.86-4.335h-1.078c-1.4 0-2.366-1.246-2.04-2.61 1.384-.452 3.235-1.34 4.195-3.616.836-1.98.821-4.046-.816-5.85z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-700">PayPal</h3>
                                    <p class="text-sm text-gray-500">Pay securely with your PayPal account</p>
                                </div>
                            </label>
                        </div>

                        <div class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary transition-colors relative">
                            <input type="radio" name="payment_method" id="pay_on_arrival" value="pay_on_arrival" class="absolute top-4 right-4" checked>
                            <label for="pay_on_arrival" class="flex items-start cursor-pointer">
                                <div class="flex-shrink-0 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-700">Pay on Arrival</h3>
                                    <p class="text-sm text-gray-500">Pay when you arrive at the attraction</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="terms" name="terms" class="mr-2" required>
                    <label for="terms" class="text-gray-600">I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
                </div>

                <button type="submit" id="submit-button" class="btn-primary w-full py-3">Complete Booking</button>
            </form>
        </div>

        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Trip Summary</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Number of Attractions:</span>
                        <span class="text-gray-600">{{ count($attractions) }}</span>
                    </div>

                    @foreach ($attractions as $attraction)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ $attraction['title'] }} ({{ $attraction['quantity'] }}
                                {{ $attraction['quantity'] > 1 ? 'guests' : 'guest' }})</span>
                            <span class="text-gray-500">{{ $attraction['subtotal'] }}£E</span>
                        </div>
                    @endforeach

                    <div class="border-t border-gray-200 pt-4 flex justify-between font-bold">
                        <span>Total</span>
                        <span>{{ $total }}£E</span>
                    </div>
                </div>

                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="font-bold text-gray-600 mb-2">Important Information</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Free cancellation up to 24 hours before</li>
                        <li>• Please arrive 15 minutes before your scheduled time</li>
                        <li>• Bring a valid ID for verification</li>
                        <li>• Confirmation will be sent to your email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        var stripe = Stripe('{{ config('services.stripe.key') }}');
        var elements = stripe.elements();
        var cardElement = elements.create('card');
        cardElement.mount('#card-element');

        var form = document.getElementById('checkout-form');
        var submitButton = document.getElementById('submit-button');
        var phoneInput = document.getElementById('phone');
        var phoneHiddenInput = document.getElementById('phone-input');
        var cardDetails = document.getElementById('card-details');
        var creditCardRadio = document.getElementById('credit_card');
        var payOnArrivalRadio = document.getElementById('pay_on_arrival');
        var paypalRadio = document.getElementById('paypal');

        // Show/hide card details based on payment method
        function toggleCardDetails() {
            if (creditCardRadio.checked) {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        }

        creditCardRadio.addEventListener('change', toggleCardDetails);
        payOnArrivalRadio.addEventListener('change', toggleCardDetails);
        paypalRadio.addEventListener('change', toggleCardDetails);
        toggleCardDetails(); // Initial state

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Update hidden phone input
            phoneHiddenInput.value = phoneInput.value;

            // Disable the submit button to prevent repeated clicks
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            if (creditCardRadio.checked) {
                // Handle credit card payment
                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) {
                        // Show error in #card-errors element
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        submitButton.disabled = false;
                        submitButton.textContent = 'Complete Booking';
                    } else {
                        // Send token to server
                        document.getElementById('stripe-token').value = result.token.id;
                        form.submit();
                    }
                });
            } else if (paypalRadio.checked) {
                // Handle PayPal payment
                // In a real implementation, you would redirect to PayPal
                // For now, we'll just submit the form
                form.submit();
            } else {
                // Handle pay on arrival
                form.submit();
            }
        });
    </script>
@endsection
