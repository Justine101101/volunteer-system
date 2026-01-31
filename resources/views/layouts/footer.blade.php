<footer class="bg-white border-t border-gray-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Club Name -->
        <div class="text-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cordillera Adivay Lions Club</h3>
            
            <!-- Navigation Links -->
            <nav class="flex justify-center items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                <span class="text-gray-400">•</span>
                <a href="{{ route('about') }}" class="hover:text-gray-900 transition">About</a>
                <span class="text-gray-400">•</span>
                <a href="{{ route('events.index') }}" class="hover:text-gray-900 transition">Events</a>
                <span class="text-gray-400">•</span>
                <a href="{{ route('contact') }}" class="hover:text-gray-900 transition">Contact</a>
            </nav>
            
            <!-- Social Media Links -->
            <div class="flex justify-center items-center gap-4 mt-4">
                <a href="https://www.facebook.com/profile.php?id=61566458549684" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="text-lions-green hover:text-lions-green-light transition duration-300"
                   aria-label="Follow us on Facebook">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="text-center text-sm text-gray-500">
            © {{ date('Y') }} Volunteer System. All rights reserved.
        </div>
    </div>
</footer>
