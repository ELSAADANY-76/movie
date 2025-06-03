<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Showtimes for ') }}{{ $movie->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @can('create showtimes')
                        <div class="mb-6">
                            <a href="{{ route('showtimes.create', $movie) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Showtime
                            </a>
                        </div>
                    @endcan

                    @if($movie->showtimes->isEmpty())
                        <p class="text-gray-600">No showtimes available for this movie.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($movie->showtimes->sortBy('start_time') as $showtime)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold">{{ $showtime->start_time->format('M d, Y h:i A') }}</span>
                                        <span class="text-gray-600">Hall {{ $showtime->hall_number }}</span>
                                    </div>
                                    <div class="text-gray-600 mb-2">
                                        <p>Available Seats: {{ $showtime->available_seats }}</p>
                                        <p>Price: ${{ number_format($showtime->price, 2) }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        @auth
                                            @if(auth()->user()->hasRole('customer'))
                                                <a href="{{ route('bookings.create', $showtime) }}" 
                                                   class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    Book Now
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" 
                                               class="block w-full text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                                Login to Book
                                            </a>
                                        @endauth
                                        @can('edit showtimes')
                                            <a href="{{ route('showtimes.edit', $showtime) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">Edit</a>
                                        @endcan
                                        @can('delete showtimes')
                                            <form action="{{ route('showtimes.destroy', $showtime) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this showtime?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 