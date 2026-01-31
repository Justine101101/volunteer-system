<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
        <input id="update_password_current_password" 
               name="current_password" 
               type="password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('current_password', 'updatePassword') border-red-500 @enderror" 
               autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
        <input id="update_password_password" 
               name="password" 
               type="password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password', 'updatePassword') border-red-500 @enderror" 
               autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
        <input id="update_password_password_confirmation" 
               name="password_confirmation" 
               type="password" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
               autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end pt-4">
        <button type="submit" 
                class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300 shadow-md hover:shadow-lg">
            Update Password
        </button>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="ml-4 text-sm text-green-600 font-medium"
            >{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
