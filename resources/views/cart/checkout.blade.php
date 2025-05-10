@extends('layouts.app')

@section('title', 'Checkout - Massar')

@section('content')
    <div class="mb-6">
        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary">
            <!-- back icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
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
            <form action="{{ route('cart.process-checkout') }}" method="POST" id="stripe-form" class="space-y-8">
                @csrf

                <!-- Hidden inputs for Stripe -->
                <input type="hidden" name="total" value="{{ $total }}">
                <input type="hidden" name="stripeToken" id="stripe-token">

                <div class="card p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-600">Contact Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Phone Number</label>
                            <input type="tel" name="PhoneNumber" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Booking Time</label>
                            <input type="date" name="BookingTime" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Quantity</label>
                            <input type="number" name="Quantity" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">AttractionStaffId</label>
                            <input type="text" name="AttractionStaffId" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Visit Date</label>
                            <input type="date" name="VisitDate" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Total Cost</label>
                            <input type="number" name="TotalCost" step="0.01" class="w-full p-2 border border-gray-200 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">State</label>
                            <select name="state" class="w-full p-2 border border-gray-200 rounded-md">
                                <option value="">Select</option>
                                <option value="valid">Valid</option>
                                <option value="not valid">Not Valid</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Ticket Type</label>
                            <select name="TicketTypesId" class="w-full p-2 border border-gray-200 rounded-md bg-white" required>
                                <option value="">Select Ticket Type</option>
                                @foreach($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->id }}">{{ $ticketType->Title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-600">Payment Method</h2>

                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-md p-4 relative">
                            <input type="radio" name="payment_method" id="credit_card" value="credit_card"
                                class="absolute top-4 right-4" checked>
                            <label for="credit_card" class="flex items-start cursor-pointer">
                                <div class="flex-shrink-0 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-700">Credit/Debit Card</h3>
                                    <p class="text-sm text-gray-500">Pay securely with your credit or debit card</p>

                                    <label for="card-element" class="block text-gray-700 mb-1 mt-4">Card Details</label>
                                    <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white" style="width: 100%"></div>
                                    <div id="card-errors" class="text-red-500 text-sm mt-2"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="terms" name="terms" class="mr-2" required>
                    <label for="terms" class="text-gray-600">I agree to the <a href="#"
                            class="text-primary">Terms and Conditions</a> and <a href="#"
                            class="text-primary">Privacy Policy</a></label>
                </div>

                <button type="button" class="btn-primary w-full py-3" onclick="createToken()">Complete Booking</button>
            </form>
        </div>

        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Trip Summary</h2>

                <div class="space-y-4 mb-6">
                    @foreach($attractions as $attraction)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Attraction:</span>
                            <span class="text-gray-600">{{ $attraction['title'] }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ $attraction['quantity'] }} guest(s)</span>
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
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        function createToken() {
            
            var form = document.getElementById('stripe-form');
            form.submit();
        }
    </script>
@endsection
