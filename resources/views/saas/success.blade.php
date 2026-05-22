<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome — AureusERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-violet-900 via-violet-700 to-indigo-600 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-12 max-w-lg w-full text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-3">You're all set! 🎉</h1>
        <p class="text-gray-500 mb-2">Your organisation <strong class="text-violet-700">{{ $company }}</strong> has been created.</p>
        <p class="text-gray-500 mb-8">Check your email for login details and get started with your free trial.</p>
        <a href="/admin"
           class="block w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 rounded-xl transition text-lg">
            Go to Your Dashboard →
        </a>
    </div>
</body>
</html>
