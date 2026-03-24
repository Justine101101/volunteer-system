<x-guest-layout>
    <div class="min-h-[70vh] py-10 px-4">
        <div class="max-w-4xl mx-auto rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Privacy Policy</h1>
            <p class="mt-2 text-sm text-slate-500">
                Volunteer Management System - Effective Date: {{ now()->format('F d, Y') }}
            </p>

            <div class="mt-6 space-y-6 text-slate-700 leading-relaxed">
                <section>
                    <h2 class="text-lg font-semibold text-slate-900">1. Information We Collect</h2>
                    <p class="mt-2">
                        We collect account and volunteer information such as name, email address, profile photo, role, and participation records.
                        We also store event registrations, attendance, and communication activity needed to operate the platform.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">2. How We Use Information</h2>
                    <p class="mt-2">
                        Your information is used to manage volunteer applications, event participation, attendance, in-app notifications, and essential email updates.
                        We use data only for volunteer management and organization operations.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">3. Data Storage and Security</h2>
                    <p class="mt-2">
                        Data is stored in the system database and supporting cloud services used by this application.
                        Reasonable technical controls are applied to protect account and activity records from unauthorized access.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">4. Data Sharing</h2>
                    <p class="mt-2">
                        We do not sell personal information. Data may be shared only with authorized administrators and service providers
                        required to run the Volunteer Management System.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">5. Retention</h2>
                    <p class="mt-2">
                        We retain data for as long as needed for volunteer operations, reporting, legal compliance, and security auditing.
                        Retention periods may vary by data type and organizational policy.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">6. Your Choices</h2>
                    <p class="mt-2">
                        You may request profile updates, account assistance, or notification preference changes through your account settings or administrators.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">7. Policy Updates</h2>
                    <p class="mt-2">
                        We may update this Privacy Policy when system features or legal requirements change. Continued use of the platform
                        after updates indicates acceptance of the revised policy.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">8. Contact</h2>
                    <p class="mt-2">
                        For privacy concerns or data-related requests, please contact the organization through the Contact page.
                    </p>
                </section>
            </div>

            <div class="mt-8">
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    Back to Registration
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>

