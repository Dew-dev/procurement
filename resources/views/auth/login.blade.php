<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Procurements</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h1 class="text-2xl font-semibold mb-1">Sign in</h1>
        <p class="text-slate-500 text-sm mb-6">Masuk untuk mengakses aplikasi procurement.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-300" id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-300" id="password" type="password" name="password" required>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-slate-900 text-white py-2.5 font-medium hover:bg-slate-700">Login</button>
        </form>

        <div class="mt-6 text-xs text-slate-500 space-y-1">
            <p>Admin: admin@procurements.test / password</p>
            <p>User: user@procurements.test / password</p>
        </div>
    </div>
</body>
</html>
