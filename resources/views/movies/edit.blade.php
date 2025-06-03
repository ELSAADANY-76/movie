<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Movie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('movies.update', $movie) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-label for="title" :value="__('Title')" />
                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $movie->title)" required autofocus />
                            <x-input-error for="title" class="mt-2" />
                        </div>

                        <!-- Genre -->
                        <div>
                            <x-label for="genre" :value="__('Genre')" />
                            <x-input id="genre" class="block mt-1 w-full" type="text" name="genre" :value="old('genre', $movie->genre)" required />
                            <x-input-error for="genre" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('description', $movie->description) }}</textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <!-- Poster URL -->
                        <div>
                            <x-label for="poster_url" :value="__('Poster URL')" />
                            <x-input id="poster_url" class="block mt-1 w-full" type="text" name="poster_url" :value="old('poster_url', $movie->poster_url)" required />
                            <x-input-error for="poster_url" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('movies.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-button>
                                {{ __('Update Movie') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 