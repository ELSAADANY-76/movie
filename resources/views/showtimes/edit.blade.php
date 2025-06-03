<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Showtime for ') }}{{ $showtime->movie->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('showtimes.update', $showtime) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Start Time -->
                        <div>
                            <x-label for="start_time" :value="__('Start Time')" />
                            <x-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time', $showtime->start_time->format('Y-m-d\TH:i'))" required />
                            <x-input-error for="start_time" class="mt-2" />
                        </div>

                        <!-- Hall Number -->
                        <div>
                            <x-label for="hall_number" :value="__('Hall Number')" />
                            <x-input id="hall_number" class="block mt-1 w-full" type="number" name="hall_number" :value="old('hall_number', $showtime->hall_number)" required min="1" />
                            <x-input-error for="hall_number" class="mt-2" />
                        </div>

                        <!-- Total Seats -->
                        <div>
                            <x-label for="total_seats" :value="__('Total Seats')" />
                            <x-input id="total_seats" class="block mt-1 w-full" type="number" name="total_seats" :value="old('total_seats', $showtime->total_seats)" required min="1" />
                            <x-input-error for="total_seats" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-label for="price" :value="__('Price')" />
                            <x-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', $showtime->price)" required min="0" />
                            <x-input-error for="price" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('movies.show', $showtime->movie) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-button>
                                {{ __('Update Showtime') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 