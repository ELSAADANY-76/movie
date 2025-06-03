<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Movies') }}
            </h2>
            @can('create movies')
                <a href="{{ route('movies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Movie
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($movies as $movie)
                            <div class="border rounded-lg overflow-hidden">
                                <img src="{{ asset($movie->poster_url) }}" alt="{{ $movie->title }}" class="w-full h-64 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $movie->title }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ $movie->genre }}</p>
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('movies.show', $movie) }}" class="text-blue-500 hover:text-blue-700">
                                            View Details
                                        </a>
                                        @can('edit movies')
                                            <a href="{{ route('movies.edit', $movie) }}" class="text-green-500 hover:text-green-700">
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $movies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 