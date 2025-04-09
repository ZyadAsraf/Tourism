@extends('layouts.app')

@section('title', 'Book ' . $attraction['title'] . ' - TravelEgypt')

@section('content')
  <div class="mb-6">
      <a href="{{ route('attractions.show', $attraction['slug']) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          <span>Back to attraction</span>
      </a>
  </div>
  
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
          <h1 class="text-3xl font-bold text-gray-600 mb-6">Book {{ $attraction['title'] }}</h1>
          
          <form action="{{ route('booking.payment', $attraction['slug']) }}" method="POST" class="space-y-8">
              @csrf
              <div class="card p-6">
                  <h2 class="text-xl font-bold mb-4 text-gray-600">Trip Details</h2>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div>
                          <label class="block text-gray-600 mb-1">Date</label>
                          <input type="date" name="date" class="w-full p-2 border border-gray-200 rounded-md" required>
                      </div>
                      <div>
                          <label class="block text-gray-600 mb-1">Time</label>
                          <select name="time" class="w-full p-2 border border-gray-200 rounded-md" required>
                              <option value="">Select a time</option>
                              <option value="morning">Morning (9:00 AM)</option>
                              <option value="afternoon">Afternoon (1:00 PM)</option>
                              <option value="evening">Evening (5:00 PM)</option>
                          </select>
                      </div>
                  </div>
                  
                  <div class="mb-4">
                      <label class="block text-gray-600 mb-1">Number of Guests</label>
                      <select name="guests" class="w-full p-2 border border-gray-200 rounded-md" required>
                          <option value="1">1 Guest</option>
                          <option value="2">2 Guests</option>
                          <option value="3">3 Guests</option>
                          <option value="4">4 Guests</option>
                          <option value="5">5+ Guests</option>
                      </select>
                  </div>
                  
                  <div>
                      <label class="block text-gray-600 mb-1">Special Requests (Optional)</label>
                      <textarea name="special_requests" rows="3" class="w-full p-2 border border-gray-200 rounded-md" placeholder="Any special requirements or requests..."></textarea>
                  </div>
              </div>
              
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
                          <input type="tel" name="phone" class="w-full p-2 border border-gray-200 rounded-md" required>
                      </div>
                  </div>
              </div>
              
              <div class="space-y-2 mb-4">
                  <div class="flex justify-between">
                      <span class="text-gray-600">Price per person</span>
                      <span class="text-gray-600">{{ $attraction['price'] }}£E</span>
                  </div>
                  <div class="flex justify-between font-bold">
                      <span>Starting from</span>
                      <span>{{ $attraction['price'] }}£E</span>
                  </div>
                  <p class="text-xs text-gray-500 text-center">Final price will be calculated based on number of guests</p>
              </div>
              
              <button type="submit" class="btn-primary w-full py-3">Continue to Payment</button>
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
                      <span class="text-gray-600">To be selected</span>
                  </div>
                  <div class="flex justify-between">
                      <span class="text-gray-600">Time</span>
                      <span class="text-gray-600">To be selected</span>
                  </div>
                  <div class="flex justify-between">
                      <span class="text-gray-600">Guests</span>
                      <span class="text-gray-600">To be selected</span>
                  </div>
              </div>
              
              <div class="space-y-2 mb-4">
                  <div class="flex justify-between">
                      <span class="text-gray-600">Price per person</span>
                      <span class="text-gray-600">{{ $attraction['price'] }}£E</span>
                  </div>
                  <div class="flex justify-between font-bold">
                      <span>Total</span>
                      <span>{{ $attraction['price'] }}£E</span>
                  </div>
              </div>
              
              <div class="bg-gray-100 p-4 rounded-lg">
                  <h3 class="font-bold text-gray-600 mb-2">Important Information</h3>
                  <ul class="text-sm text-gray-600 space-y-2">
                      <li>• No payment required now</li>
                      <li>• Free cancellation up to 24 hours before</li>
                      <li>• Please arrive 15 minutes before your scheduled time</li>
                      <li>• Bring a valid ID for verification</li>
                  </ul>
              </div>
          </div>
      </div>
  </div>
@endsection

