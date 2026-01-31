<x-guest-layout>
    <div data-animate>
    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
                <p class="text-sm text-gray-600 mt-1">Join the Volunteer Management System</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-white shadow-sm p-6">
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
    <form method="POST" action="{{ route('register') }}" x-data="{ loading:false }" x-on:submit="loading=true; $nextTick(()=>{ const el = document.querySelector('[aria-invalid=\"true\"]'); if(el) el.focus(); })">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required aria-invalid="{{ $errors->has('role') ? 'true' : 'false' }}">
                <option value="volunteer" {{ old('role', 'volunteer') === 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                <option value="officer" {{ old('role') === 'officer' ? 'selected' : '' }}>Officer</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="ms-4 bg-emerald-600 text-white px-5 py-2 rounded-md hover:bg-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500" :class="loading ? 'btn-loading opacity-90' : ''" :disabled="loading">
                {{ __('Register') }}
            </button>
        </div>
    </form>
            </div>
            <p class="text-center text-sm text-gray-600 mt-4">By continuing, you agree to our <a href="#" class="text-emerald-700 hover:underline">Terms</a> and <a href="#" class="text-emerald-700 hover:underline">Privacy Policy</a>.</p>
        </div>
    </div>
    </div>
</x-guest-layout>
