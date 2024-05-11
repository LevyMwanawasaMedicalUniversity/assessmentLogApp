<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $results->CourseDescription }} - {{$results->CourseName}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full px-4">
                                @include('coordinator.components.excelSheetorm')
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>