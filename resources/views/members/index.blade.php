<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Our Members') }}
        </h2>
    </x-slot>

    @include('members.partials.members-home', ['members' => $members])
</x-app-layout>
