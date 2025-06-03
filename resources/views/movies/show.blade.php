<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $movie->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/3">
                            <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}" class="w-full rounded-lg shadow-md">
                        </div>
                        <div class="md:w-2/3 md:pl-6 mt-4 md:mt-0">
                            <h3 class="text-2xl font-bold">{{ $movie->title }}</h3>
                            <p class="text-gray-600 mt-2">{{ $movie->genre }}</p>
                            <p class="mt-4">{{ $movie->description }}</p>
                            <p class="btn btn-primary mt-4">
                                <a href="{{ route('showtimes.Payment', $movie->id) }}">booking Movie</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 