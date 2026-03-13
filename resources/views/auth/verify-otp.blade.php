<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-slate-900">Verify your email</h1>
                @if($email)
                    <p class="text-sm text-slate-500">
                        We’ve sent a 6-digit code to <span class="font-semibold">{{ $email }}</span>
                    </p>
                @else
                    <p class="text-sm text-slate-500">
                        Enter the 6-digit verification code sent to your email.
                    </p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                @if(session('status'))
                    <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                        <ul class="list-disc ms-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify.post') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Verification Code</label>
                        <input type="text"
                               name="otp"
                               maxlength="6"
                               pattern="\d*"
                               inputmode="numeric"
                               placeholder="123456"
                               required
                               class="w-full tracking-[0.3em] text-center text-lg px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-slate-500">This code expires in 5 minutes.</p>
                    </div>

                    <button type="submit"
                            class="w-full mt-3 inline-flex justify-center items-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700">
                        Verify Email
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

