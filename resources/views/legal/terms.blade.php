<x-guest-layout>
    <div class="min-h-[70vh] py-10 px-4">
        <div class="max-w-4xl mx-auto rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Terms of Service</h1>
            <p class="mt-2 text-sm text-slate-500">
                Volunteer Management System - Effective Date: {{ now()->format('F d, Y') }}
            </p>

            <div class="mt-6 space-y-6 text-slate-700 leading-relaxed">
                <section>
                    <h2 class="text-lg font-semibold text-slate-900">1. Acceptance of Terms</h2>
                    <p class="mt-2">
                        By creating an account or using this Volunteer Management System, you agree to these Terms of Service.
                        If you do not agree, please do not use the platform.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">2. Platform Purpose</h2>
                    <p class="mt-2">
                        This platform is used to manage volunteers, events, attendance, and communication for community service activities.
                        You agree to use it only for lawful and authorized volunteer-related purposes.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">3. Account Responsibilities</h2>
                    <p class="mt-2">
                        You are responsible for maintaining accurate profile information and keeping your login credentials secure.
                        You are also responsible for activities performed through your account.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">4. Acceptable Use</h2>
                    <p class="mt-2">
                        You must not misuse the platform, send abusive content, attempt unauthorized access, or interfere with system operations.
                        Administrators may suspend or remove accounts that violate these terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">5. Event Registration and Participation</h2>
                    <p class="mt-2">
                        Event registrations may require admin approval. Submitting an application does not guarantee acceptance.
                        Organizers may modify event details, schedules, or participation status when needed.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">6. Limitation of Liability</h2>
                    <p class="mt-2">
                        This system is provided on an "as is" basis. While we aim for reliability, we do not guarantee uninterrupted service.
                        The organization is not liable for losses resulting from outages, delays, or unauthorized account access caused by user negligence.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">7. Changes to Terms</h2>
                    <p class="mt-2">
                        We may update these terms as the platform evolves. Continued use after updates means you accept the revised terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-slate-900">8. Contact</h2>
                    <p class="mt-2">
                        For concerns about these terms, please contact the organization through the Contact page.
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

