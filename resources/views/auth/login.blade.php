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
            </div>
            <p class="text-center text-sm text-gray-600 mt-4">Don't have an account? 
                <a href="{{ route('register') }}" class="text-emerald-700 font-medium hover:underline">Register</a>
            </p>
        </div>
    </div>
    </div>
</x-guest-layout>
