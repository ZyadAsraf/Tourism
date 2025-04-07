@extends('layouts.app')

@section('title', 'Payment - ' . $attraction['title'] . ' - TravelEgypt')

@section('content')
<div class="mb-6">
    <a href="{{ route('booking.form', $attraction['slug']) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        <span>Back to booking details</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <h1 class="text-3xl font-bold text-gray-600 mb-6">Payment Method</h1>
        
        <form action="{{ route('booking.process', $attraction['slug']) }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Hidden fields to carry over booking details -->
            <input type="hidden" name="date" value="{{ $bookingDetails['date'] }}">
            <input type="hidden" name="time" value="{{ $bookingDetails['time'] }}">
            <input type="hidden" name="guests" value="{{ $bookingDetails['guests'] }}">
            @if(isset($bookingDetails['special_requests']))
                <input type="hidden" name="special_requests" value="{{ $bookingDetails['special_requests'] }}">
            @endif
            
            <!-- Payment Methods -->
            <div class="card p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Select Payment Method</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary transition-colors relative">
                        <input type="radio" name="payment_method" id="credit_card" value="credit_card" class="absolute top-4 right-4" checked>
                        <label for="credit_card" class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700">Credit/Debit Card</h3>
                                <p class="text-sm text-gray-500">Pay securely with your credit or debit card</p>
                                <div class="flex gap-2 mt-2">
                                    <img src="/images/visa.png" alt="Visa" class="h-6">
                                    <img src="/images/mastercard.png" alt="Mastercard" class="h-6">
                                    <img src="/images/amex.png" alt="American Express" class="h-6">
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
                        <input type="radio" name="payment_method" id="apple_pay" value="apple_pay" class="absolute top-4 right-4">
                        <label for="apple_pay" class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.6 12.9c0-1.6 1.3-2.4 1.4-2.4-.8-1.1-1.9-1.3-2.3-1.3-1-.1-1.9.6-2.4.6s-1.2-.6-2-.6c-1 0-2 .6-2.5 1.5-1.1 1.9-.3 4.6.8 6.1.5.7 1.1 1.5 1.9 1.5.8 0 1.1-.5 2-.5s1.2.5 2 .5c.8 0 1.4-.7 1.9-1.5.6-.9.9-1.7.9-1.7-.1-.1-1.7-.7-1.7-2.7zM16.1 7.1c.4-.5.7-1.2.6-1.9-.6 0-1.3.4-1.7.9-.4.5-.7 1.2-.6 1.9.6 0 1.3-.4 1.7-.9z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700">Apple Pay</h3>
                                <p class="text-sm text-gray-500">Pay securely with Apple Pay</p>
                            </div>
                        </label>
                    </div>
                    
                    <div class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary transition-colors relative">
                        <input type="radio" name="payment_method" id="pay_on_arrival" value="pay_on_arrival" class="absolute top-4 right-4">
                        <label for="pay_on_arrival" class="flex items-start cursor-pointer">
                            <div class="flex-shrink-0 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700">Pay on Arrival</h3>
                                <p class="text-sm text-gray-500">Pay in cash or card when you arrive at the attraction</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Credit Card Details (shown only when credit card is selected) -->
            <div id="credit_card_details" class="card p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Card Details</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 mb-1">Card Number</label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" class="w-full p-2 border border-gray-200 rounded-md">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 mb-1">Expiry Date</label>
                            <input type="text" name="expiry_date" placeholder="MM/YY" class="w-full p-2 border border-gray-200 rounded-md">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">CVV</label>
                            <input type="text" name="cvv" placeholder="123" class="w-full p-2 border border-gray-200 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 mb-1">Cardholder Name</label>
                        <input type="text" name="cardholder_name" placeholder="John Doe" class="w-full p-2 border border-gray-200 rounded-md">
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Billing Address</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 mb-1">Country</label>
                        <select name="country" class="w-full p-2 border border-gray-200 rounded-md">
                            <option value="egypt">Egypt</option>
                            <option value="usa">United States</option>
                            <option value="uk">United Kingdom</option>
                            <option value="canada">Canada</option>
                            <option value="australia">Australia</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 mb-1">Address</label>
                        <input type="text" name="address" placeholder="123 Main St" class="w-full p-2 border border-gray-200 rounded-md">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 mb-1">City</label>
                            <input type="text" name="city" placeholder="Cairo" class="w-full p-2 border border-gray-200 rounded-md">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" placeholder="12345" class="w-full p-2 border border-gray-200 rounded-md">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center mb-6">
                <input type="checkbox" id="terms" name="terms" class="mr-2" required>
                <label for="terms" class="text-gray-600">I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3">Complete Payment</button>
        </form>
    </div>
    
    <div class="lg:col-span-1">
        <div class="card p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4 text-gray-600">Booking Summary</h2>
            
            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-200">
                <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-20 h-20 object-cover rounded-lg">
                <div>
                    <h3 class="font-bold text-gray-600">{{ $attraction['title'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $attraction['location'] }} • {{ $attraction['duration'] }}</p>
                </div>
            </div>
            
            <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                <div class="flex justify-between">
                    <span class="text-gray-600">Date</span>
                    <span class="text-gray-600">{{ $bookingDetails['date'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time</span>
                    <span class="text-gray-600">{{ $bookingDetails['time'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Guests</span>
                    <span class="text-gray-600">{{ $bookingDetails['guests'] }}</span>
                </div>
            </div>
            
          <div class="space-y-2 mb-4">
              <div class="flex justify-between">
                  <span class="text-gray-600">Price per person</span>
                  <span class="text-gray-600">{{ $attraction['price'] }}£E</span>
              </div>
              <div class="flex justify-between">
                  <span class="text-gray-600">Subtotal ({{ $bookingDetails['guests'] }} {{ $bookingDetails['guests'] > 1 ? 'guests' : 'guest' }})</span>
                  <span class="text-gray-600">{{ $pricing['subtotal'] }}£E</span>
              </div>
              <div class="flex justify-between">
                  <span class="text-gray-600">Tax (14%)</span>
                  <span class="text-gray-600">{{ number_format($pricing['tax'], 2) }}£E</span>
              </div>
              <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                  <span>Total</span>
                  <span>{{ number_format($pricing['total'], 2) }}£E</span>
              </div>
          </div>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="font-bold text-gray-600 mb-2">Important Information</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• Free cancellation up to 24 hours before</li>
                    <li>• Please arrive 15 minutes before your scheduled time</li>
                    <li>• Bring a valid ID for verification</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple script to show/hide credit card details based on payment method selection
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const creditCardDetails = document.getElementById('credit_card_details');
        
        function toggleCreditCardDetails() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            if (selectedMethod === 'credit_card') {
                creditCardDetails.style.display = 'block';
            } else {
                creditCardDetails.style.display = 'none';
            }
        }
        
        // Initial check
        toggleCreditCardDetails();
        
        // Add event listeners to all payment method radio buttons
        paymentMethods.forEach(method => {
            method.addEventListener('change', toggleCreditCardDetails);
        });
    });
</script>
@endsection

