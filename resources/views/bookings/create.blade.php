<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Book Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium">{{ $showtime->movie->title }}</h3>
                        <p class="mt-2 text-gray-600">
                            Showtime: {{ $showtime->start_time->format('M d, Y h:i A') }}
                        </p>
                        <p class="mt-1 text-gray-600">
                            Available Seats: {{ $showtime->available_seats }}
                        </p>
                        <p class="mt-1 text-gray-600">
                            Price per Ticket: ${{ number_format($showtime->price, 2) }}
                        </p>
                        <p class="mt-1 text-gray-600">
                            Your Credit Balance: ${{ number_format(auth()->user()->credit, 2) }}
                        </p>
                    </div>

                    <form action="{{ route('bookings.store', $showtime) }}" method="POST" class="mt-6">
                        @csrf
                        <div class="mb-4">
                            <label for="seats" class="block text-sm font-medium text-gray-700">Number of Seats</label>
                            <div class="mt-1">
                                <input type="number" min="1" max="{{ $showtime->available_seats }}" 
                                    name="seats" id="seats" 
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            @error('seats')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Seat Selection</label>
                            <div class="mt-2 grid grid-cols-8 gap-2">
                                @for($i = 1; $i <= 40; $i++)
                                    <div class="relative">
                                        <input type="checkbox" name="selected_seats[]" value="{{ $i }}" 
                                            id="seat-{{ $i }}" class="hidden peer"
                                            @if(in_array($i, $showtime->booked_seats)) disabled @endif>
                                        <label for="seat-{{ $i }}" 
                                            class="block w-full h-8 text-center leading-8 border rounded cursor-pointer
                                            peer-checked:bg-indigo-600 peer-checked:text-white
                                            @if(in_array($i, $showtime->booked_seats)) bg-gray-200 cursor-not-allowed @else hover:bg-gray-100 @endif">
                                            {{ $i }}
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            @error('selected_seats')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Book Tickets
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seatsInput = document.getElementById('seats');
            const seatCheckboxes = document.querySelectorAll('input[name="selected_seats[]"]');
            
            seatsInput.addEventListener('change', function() {
                const maxSeats = parseInt(this.value);
                let selectedCount = 0;
                
                seatCheckboxes.forEach(checkbox => {
                    if (!checkbox.disabled) {
                        if (selectedCount >= maxSeats) {
                            checkbox.checked = false;
                        }
                        checkbox.addEventListener('change', function() {
                            if (this.checked) {
                                selectedCount++;
                                if (selectedCount > maxSeats) {
                                    this.checked = false;
                                    selectedCount--;
                                }
                            } else {
                                selectedCount--;
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 