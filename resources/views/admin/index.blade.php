<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Page') }}
        </h2>
    </x-slot>

    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full px-4">
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                    <div class="grid grid-cols-3 gap-6"> <!-- Grid with 3 equal-width columns -->
                                        <!-- First Block -->
                                        <a href="{{route('admin.viewCoordinators')}}">
                                            <button type="button" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center">
                                                <div class="p-6 text-gray-900">
                                                    {{ __("View Coordinators") }}
                                                </div>
                                            </button>
                                        </a>                                        
                                        <!-- Second Block -->   
                                        <a href="{{route('admin.importCoordinators')}}">
                                            <button type="button" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center">
                                                <div class="p-6 text-gray-900">
                                                    {{ __("Import Coordinators") }}
                                                </div>
                                            </button>
                                        </a> 
                                        <!-- Third Block -->                                        
                                        <button type="" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center">
                                            <div class="p-6 text-gray-900">
                                                {{ __("User Management") }}
                                            </div>
                                        </button>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>