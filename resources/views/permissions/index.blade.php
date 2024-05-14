<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Permissions
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table id="myTable" class="table-auto w-full mt-4">
                        <div class="flex justify-between items-center">
                            <a class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('permissions.create') }}">Add Permissions</a>
                        </div>
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Auth Guard</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Auth Guard</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </tfoot>
                        @if(count($permissions) > 0)
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr class="border-t border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{ $permission->name }}</td>
                                <td class="px-4 py-2">{{ $permission->guard_name }}</td>
                                
                                <td class="px-4 py-2">
                                    <div class="btn-group flex justify-end" role="group" aria-label="Button group">                                        
                                        <a type="button" href="{{ route('permissions.edit', $permission->id) }}">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                        <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}" style="display: inline">
                                            @csrf
                                            @method('DELETE')                                            
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-l-none" onclick="return confirm('Are you sure you want to delete this item?')">
                                                    Delete
                                                </button>                                              
                                        </form> 
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @else
                        <tbody>
                        <tr>                            
                            <h3 class="text-center">No Permissions.</h3>
                        </tr>
                        </tbody>
                            @endif
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>