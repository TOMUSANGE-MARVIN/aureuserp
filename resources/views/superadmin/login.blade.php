<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console — Sign In</title>
    <script>
        // Apply theme before paint to avoid flash
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .gradient-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(99,102,241,0.15) 0%, transparent 60%),
                        radial-gradient(ellipse at 0% 80%, rgba(99,102,241,0.08) 0%, transparent 50%);
        }
        .dark .gradient-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(99,102,241,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 0% 80%, rgba(99,102,241,0.06) 0%, transparent 50%);
        }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 50px white inset; }
        .dark input:-webkit-autofill { -webkit-box-shadow: 0 0 0 50px #0f0f1a inset; -webkit-text-fill-color: #f9fafb; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-[#080812] gradient-bg flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-16 w-auto object-contain mx-auto mb-4 shadow-lg rounded-2xl">
        </div>

        <!-- Card -->
        <div class="bg-white dark:bg-[#0f0f1a] rounded-3xl border border-gray-100 dark:border-white/5 shadow-xl shadow-gray-200/60 dark:shadow-none p-8">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="/superadmin/login" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="admin@yourplatform.com"
                           class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-red-400 dark:border-red-600' : 'border-gray-200 dark:border-white/10' }}
                                  bg-white dark:bg-[#1a1a2e] text-gray-900 dark:text-white placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors text-sm">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10
                                  bg-white dark:bg-[#1a1a2e] text-gray-900 dark:text-white placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors text-sm">
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-xl transition-colors shadow-sm shadow-indigo-500/20 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign in to Console
                </button>
            </form>
        </div>

        <!-- Footer note -->
        <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-6">
            This is a restricted area. Unauthorised access is prohibited.
        </p>

        <!-- Dark mode toggle -->
        <div class="flex justify-center mt-4">
            <button onclick="
                const dark = !document.documentElement.classList.contains('dark');
                document.documentElement.classList.toggle('dark', dark);
                localStorage.setItem('theme', dark ? 'dark' : 'light');
            " class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            </button>
        </div>
    </div>

</body>
</html>
