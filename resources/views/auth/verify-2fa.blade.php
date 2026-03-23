<x-app-layout>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Two-Factor Verification</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Enter the 6-digit code sent to your email.
            </p>

            @if ($errors->any())
                <div class="mt-4 rounded-md border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-800 dark:text-red-300" role="alert">
                    <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('two_factor.verify.post') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="otp" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">OTP Code</label>
                    <input
                        id="otp"
                        name="otp"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('otp') border-red-500 @enderror"
                        value="{{ old('otp') }}"
                        required
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('otp')" />
                </div>

                <button type="submit" class="w-full px-4 py-3 bg-emerald-600 dark:bg-emerald-700 text-white font-semibold rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition duration-300">
                    Verify Code
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

