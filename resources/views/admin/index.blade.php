<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-between">
                <a href="{{route('admin.viewCoordinators')}}">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105">
                        <div class="p-6 text-gray-900">
                            {{ __("User Management") }}
                        </div>
                    </div>
                </a>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105">
                    <div class="p-6 text-gray-900">
                        {{ __("Roles") }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105">
                    <div class="p-6 text-gray-900">
                        {{ __("Permissions") }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105">
                    <div class="p-6 text-gray-900">
                        {{ __("Card 4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>