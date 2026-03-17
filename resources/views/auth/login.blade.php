<x-guest-layout>
    <div data-animate>
    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
                <p class="text-sm text-gray-600 mt-1">Sign in to continue to Volunteer Management System</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-white shadow-sm p-6">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Google Login Button -->
    <div class="mb-6">
        <a href="{{ route('google.login') }}" 
           class="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>
    </div>

    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with email</span>
        </div>
    </div>

    @if ($errors->any())
    <div x-data="{init(){ $el.scrollIntoView({behavior:'smooth', block:'start'}); $el.focus(); }}" tabindex="-1" class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="assertive">
        <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
        <ul class="list-disc ms-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ loading:false }" x-on:submit="loading=true; $nextTick(()=>{ const el = document.querySelector('[aria-invalid=\"true\"]'); if(el) el.focus(); })">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="ms-3 bg-emerald-600 text-white px-5 py-2 rounded-md hover:bg-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500" :class="loading ? 'btn-loading opacity-90' : ''" :disabled="loading">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
            <!-- Footer Links -->
            <div class="text-center mt-8">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-lions-green font-semibold hover:text-lions-green/80 transition-colors duration-200">Register</a>
                </p>
                <p class="text-gray-600 mt-4">
                    <a href="{{ route('home') }}" class="text-lions-green font-semibold hover:text-lions-green/80 transition-colors duration-200 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7 7 7 7m0 14l-9-9-9 9"></path>
                        </svg>
                        Back to Home
                    </a>
                </p>
            </div>
    </div>
    </div>
</x-guest-layout>
