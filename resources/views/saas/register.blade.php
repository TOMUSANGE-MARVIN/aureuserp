<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started — AureusERP</title>
    <script>
        (function() {
            const stored = localStorage.getItem('theme');
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',
                            400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',
                            800:'#5b21b6',900:'#4c1d95'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }

        /* ── Plan card selection ── */
        .plan-card { cursor: pointer; transition: all .2s; }
        .plan-card.selected {
            border-color: #7c3aed !important;
            background: rgba(109,40,217,0.05) !important;
        }
        .dark .plan-card.selected {
            border-color: #8b5cf6 !important;
            background: rgba(139,92,246,0.12) !important;
        }

        /* ── Input focus ── */
        .form-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
            color: #111;
        }
        .form-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }
        .dark .form-input {
            background: #1a1a24;
            border-color: rgba(255,255,255,0.1);
            color: #e8e8f0;
        }
        .dark .form-input::placeholder { color: #44445a; }
        .dark .form-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.2);
        }

        /* ── Left panel wire mesh ── */
        .left-mesh {
            position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0;
        }
        .left-mesh::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 70% 50% at 50% 40%, transparent 20%, rgba(76,29,149,0.5) 70%, rgba(76,29,149,0.85) 100%);
        }

        /* ── Submit button ── */
        .submit-btn {
            width: 100%;
            background: #111;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 14px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: background .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .submit-btn:hover { background: #2a2a2a; }
        .submit-btn:active { transform: scale(0.99); }
        .dark .submit-btn { background: #7c3aed; }
        .dark .submit-btn:hover { background: #6d28d9; }

        /* ── Dark overrides ── */
        .dark body { background: #0c0c10; }
        .dark .register-panel { background: #16161e; border-color: rgba(255,255,255,0.07); }
        .dark .form-label { color: #9898a8; }
        .dark .heading { color: #f0f0f5; }
        .dark .subtext { color: #6e6e80; }
        .dark .plan-card { background: #1a1a26; border-color: rgba(255,255,255,0.07); }
        .dark .plan-name { color: #e0e0ea; }
        .dark .plan-desc { color: #6e6e80; }
        .dark .plan-price { color: #f0f0f5; }
        .dark .plan-price-unit { color: #55556a; }
        .dark .feature-text { color: #7070808; }
        .dark .divider { border-color: rgba(255,255,255,0.06); }
        .dark .signin-link { color: #8b5cf6; }
        .dark .error-box { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #f87171; }
        .dark .theme-toggle-btn { color: #a78bfa; border-color: rgba(255,255,255,0.1); }
        .dark .theme-toggle-btn:hover { background: rgba(255,255,255,0.07); }
        .icon-sun { display: none; }
        .icon-moon { display: block; }
        .dark .icon-sun  { display: block; }
        .dark .icon-moon { display: none; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-[#0c0c10] flex">

<!-- ══════════════ LEFT PANEL ══════════════ -->
<div class="hidden lg:flex lg:w-[42%] xl:w-[38%] bg-brand-900 relative flex-col justify-between p-10 overflow-hidden flex-shrink-0">
    <!-- Wire mesh background -->
    <div class="left-mesh">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" preserveAspectRatio="xMidYMid slice">
            <defs>
                <radialGradient id="lmg" cx="50%" cy="40%" r="60%">
                    <stop offset="0%" stop-color="rgba(167,139,250,0.2)"/>
                    <stop offset="100%" stop-color="rgba(109,40,217,0)"/>
                </radialGradient>
            </defs>
            <ellipse cx="50%" cy="40%" rx="55%" ry="45%" fill="url(#lmg)"/>
            <g stroke="rgba(167,139,250,0.15)" stroke-width="0.8" fill="none">
                <line x1="0%" y1="100%" x2="100%" y2="100%"/>
                <line x1="0%" y1="88%"  x2="100%" y2="88%"/>
                <line x1="0%" y1="76%"  x2="100%" y2="76%"/>
                <line x1="0%" y1="66%"  x2="100%" y2="66%"/>
                <line x1="0%" y1="57%"  x2="100%" y2="57%"/>
                <line x1="0%" y1="50%"  x2="100%" y2="50%"/>
                <line x1="0%" y1="44%"  x2="100%" y2="44%"/>
                <line x1="0%" y1="39%"  x2="100%" y2="39%"/>
            </g>
            <g stroke="rgba(167,139,250,0.12)" stroke-width="0.8" fill="none">
                <line x1="50%" y1="39%" x2="0%"   y2="100%"/>
                <line x1="50%" y1="39%" x2="10%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="20%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="30%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="40%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="50%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="60%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="70%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="80%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="90%"  y2="100%"/>
                <line x1="50%" y1="39%" x2="100%" y2="100%"/>
            </g>
        </svg>
    </div>

    <!-- Top: Logo + back link -->
    <div class="relative z-10 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 no-underline">
            <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-8 w-auto object-contain">
        </a>
        <a href="/" class="text-brand-200 hover:text-white text-xs flex items-center gap-1 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to site
        </a>
    </div>

    <!-- Middle: Headline + plan chooser -->
    <div class="relative z-10 flex-1 flex flex-col justify-center py-10">
        <p class="text-brand-300 text-xs font-semibold uppercase tracking-widest mb-3">Start free · No card needed</p>
        <h1 class="text-3xl font-bold text-white leading-snug mb-2">Choose a plan,<br>launch in minutes.</h1>
        <p class="text-brand-200/70 text-sm mb-8 leading-relaxed">Every plan includes a {{ $plans->first()?->trial_days ?? 14 }}-day free trial. Cancel anytime.</p>

        <div class="space-y-3">
            @foreach($plans as $plan)
            <div class="plan-card rounded-2xl border-2 border-white/15 p-4 bg-white/5"
                 onclick="selectPlan({{ $plan->id }}, this)">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="plan-name font-semibold text-white text-sm">{{ $plan->name }}</div>
                        <div class="plan-desc text-brand-200/70 text-xs mt-0.5">{{ $plan->description }}</div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <span class="plan-price text-white font-bold text-lg">${{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="plan-price-unit text-brand-300 text-xs">/mo</span>
                    </div>
                </div>
                @if($plan->features)
                <div class="flex flex-wrap gap-x-3 gap-y-1 mt-2">
                    @foreach(array_slice($plan->features, 0, 3) as $feat)
                    <span class="feature-text text-brand-200/60 text-[11px] flex items-center gap-1">
                        <svg class="w-3 h-3 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $feat['value'] ?? $feat }}
                    </span>
                    @endforeach
                    <span class="feature-text text-brand-200/60 text-[11px] flex items-center gap-1">
                        <svg class="w-3 h-3 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Up to {{ $plan->max_users }} users
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Bottom: Social proof -->
    <div class="relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex -space-x-2">
                @foreach(['A','B','C','D'] as $l)
                <div class="w-8 h-8 rounded-full bg-brand-600 border-2 border-brand-900 flex items-center justify-center text-white text-xs font-bold">{{ $l }}</div>
                @endforeach
            </div>
            <p class="text-brand-200/80 text-xs leading-snug">Joined by <span class="text-white font-semibold">10,000+</span> companies<br>across Africa & Asia</p>
        </div>
        <p class="text-brand-300/50 text-[11px]">© {{ date('Y') }} AureusERP. All rights reserved.</p>
    </div>
</div>

<!-- ══════════════ RIGHT PANEL (Form) ══════════════ -->
<div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
    <!-- Top bar -->
    <div class="flex items-center justify-between px-8 py-5">
        <!-- Mobile: logo -->
        <a href="/" class="lg:hidden flex items-center gap-2 no-underline">
            <img src="{{ asset('images/aura.png') }}" alt="Logo" class="h-7 w-auto object-contain">
        </a>
        <div class="hidden lg:block"></div>

        <div class="flex items-center gap-3">
            <!-- Dark mode toggle -->
            <button class="theme-toggle-btn w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors" id="themeToggle" aria-label="Toggle dark mode">
                <svg class="icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="icon-sun w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400">Already have an account?</span>
            <a href="/admin/login" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:underline">Sign in</a>
        </div>
    </div>

    <!-- Form area -->
    <div class="flex-1 flex items-center justify-center px-6 py-8">
        <div class="w-full max-w-lg">

            <!-- Heading -->
            <div class="mb-8">
                <h2 class="heading text-2xl font-bold text-gray-900 dark:text-white mb-1">Create your organisation</h2>
                <p class="subtext text-sm text-gray-500 dark:text-gray-400">Fill in the details below to get started. Takes under 2 minutes.</p>
            </div>

            <!-- Errors -->
            @if($errors->any())
            <div class="error-box bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <ul class="text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Mobile: Plan selector (collapsed) -->
            <div class="lg:hidden mb-6 p-4 rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#16161e] register-panel">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Select Plan</p>
                <div class="space-y-2">
                    @foreach($plans as $plan)
                    <label class="plan-card flex items-center justify-between rounded-xl border-2 border-gray-100 dark:border-white/07 p-3 bg-gray-50 dark:bg-[#1a1a26]" onclick="selectPlan({{ $plan->id }}, this)">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="plan_mobile" class="accent-brand-600" {{ $loop->first ? 'checked' : '' }}>
                            <div>
                                <div class="plan-name text-sm font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</div>
                                <div class="plan-desc text-xs text-gray-400">{{ $plan->max_users }} users</div>
                            </div>
                        </div>
                        <div class="plan-price text-sm font-bold text-gray-900 dark:text-white">${{ number_format($plan->price_monthly, 0) }}<span class="plan-price-unit font-normal text-gray-400 text-xs">/mo</span></div>
                    </label>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('saas.register.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="plan_id" id="selected_plan_id" value="{{ $plans->first()?->id }}">

                <!-- Row 1 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Organisation Name <span class="text-red-400">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                            class="form-input" placeholder="Acme Corp">
                    </div>
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Your Full Name <span class="text-red-400">*</span></label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                            class="form-input" placeholder="John Doe">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Work Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="form-input" placeholder="john@acmecorp.com">
                    </div>
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="form-input" placeholder="+1 234 567 8900">
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="pwdInput" required
                                class="form-input pr-10" placeholder="Min. 8 characters">
                            <button type="button" onclick="togglePwd('pwdInput','pwdEye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="pwdEye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="form-label block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Confirm Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="pwdInput2" required
                                class="form-input pr-10" placeholder="Repeat password">
                            <button type="button" onclick="togglePwd('pwdInput2','pwdEye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="pwdEye2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" required class="mt-0.5 accent-brand-600 rounded flex-shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        I agree to the <a href="#" class="text-brand-600 dark:text-brand-400 hover:underline">Terms of Service</a> and <a href="#" class="text-brand-600 dark:text-brand-400 hover:underline">Privacy Policy</a>
                    </span>
                </label>

                <!-- Submit -->
                <button type="submit" class="submit-btn">
                    Create Organisation
                    <span class="w-7 h-7 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                    </span>
                </button>

                <!-- Trust badges -->
                <div class="flex items-center justify-center gap-5 pt-1">
                    @foreach([['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','SSL Secure'],['M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z','No card needed'],['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','14-day trial']] as $b)
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b[0] }}"/></svg>
                        {{ $b[1] }}
                    </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectPlan(planId, el) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    // Select all cards for same plan (desktop + mobile)
    document.querySelectorAll('.plan-card').forEach(c => {
        if (c.getAttribute('onclick') === `selectPlan(${planId}, this)`) c.classList.add('selected');
    });
    document.getElementById('selected_plan_id').value = planId;
}

function togglePwd(inputId, eyeId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Auto-select first plan
document.addEventListener('DOMContentLoaded', () => {
    const first = document.querySelector('.plan-card');
    if (first) first.classList.add('selected');
});

// Dark mode toggle
document.getElementById('themeToggle').addEventListener('click', () => {
    const dark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
});
</script>
</body>
</html>
