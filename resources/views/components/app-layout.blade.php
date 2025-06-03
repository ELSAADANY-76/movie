<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Movie Booking') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-gray-800 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-lg font-semibold">{{ config('app.name', 'Movie Booking') }}</a>
            <div>
                <a href="{{ route('movies.index') }}" class="mx-2 hover:text-gray-300">Movies</a>
                
                @guest
                    <a href="{{ route('login') }}" class="mx-2 hover:text-gray-300">Login</a>
                    <a href="{{ route('register') }}" class="mx-2 hover:text-gray-300">Register</a>
                @else
                    @if(auth()->user()->hasRole(['admin', 'manager', 'staff']))
                         <a href="{{ route('admin.dashboard') }}" class="mx-2 hover:text-gray-300">Admin Dashboard</a>
                    @endif

                    <a href="{{ route('bookings.index') }}" class="mx-2 hover:text-gray-300">My Bookings</a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="mx-2 hover:text-gray-300 focus:outline-none">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <div>
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html> 