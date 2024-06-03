<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <div class="w-full">
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h4 class="text-lg leading-6 font-medium text-gray-900">{{ ucfirst($role->name) }} Role Assigned permissions</h4>
                                <div class="mt-2">
                                </div>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <div>
                                <!-- Here you can write extra buttons/actions for the toolbar -->
                                </div>
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 20%;">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 1%;">
                                                Guard
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($rolePermissions as $permission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $permission->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $permission->guard_name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>