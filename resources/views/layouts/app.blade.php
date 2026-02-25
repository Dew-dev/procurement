<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Procurements')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    @php
        $isAdmin = auth()->user()?->role === 'admin';
    @endphp

    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('contracts.index') }}" class="font-semibold text-lg">Procurements</a>
                <nav class="hidden md:flex items-center gap-4 text-sm text-slate-600">
                    <a class="hover:text-slate-900 {{ request()->routeIs('contracts.*') ? 'text-slate-900 font-medium' : '' }}" href="{{ route('contracts.index') }}">Contracts</a>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-1 rounded-full {{ $isAdmin ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
                <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-emerald-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
