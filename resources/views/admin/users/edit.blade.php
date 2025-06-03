<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-label for="name" :value="__('Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" 
                                    :value="old('name', $user->name)" required />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-label for="email" :value="__('Email')" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" 
                                    :value="old('email', $user->email)" required />
                            <x-input-error for="email" class="mt-2" />
                        </div>

                        <!-- Credit -->
                        <div>
                            <x-label for="credit" :value="__('Credit')" />
                            <x-input id="credit" class="block mt-1 w-full" type="number" step="0.01" name="credit" 
                                    :value="old('credit', $user->credit)" required min="0" />
                            <x-input-error for="credit" class="mt-2" />
                        </div>

                        <!-- Password (Optional) -->
                        <div>
                            <x-label for="password" :value="__('New Password (leave blank to keep current)')" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            <x-input-error for="password" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-label for="password_confirmation" :value="__('Confirm New Password')" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" 
                                    name="password_confirmation" />
                            <x-input-error for="password_confirmation" class="mt-2" />
                        </div>

                        <!-- Roles -->
                        <div>
                            <x-label for="roles" :value="__('Roles')" />
                            <div class="mt-2 space-y-2">
                                @foreach($roles as $role)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="ml-2">{{ ucfirst($role->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error for="roles" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-button>
                                {{ __('Update User') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 