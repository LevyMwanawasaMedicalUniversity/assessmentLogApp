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
                    <table id="myTable" class="table-auto w-full mt-4">
                        <div class="flex justify-between items-center">
                            <a class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('roles.create') }}">Add Role</a>
                        </div>
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </tfoot>
                        @if(count($roles ) > 0)
                        <tbody>
                            @foreach ($roles as $key => $role)
                            <tr class="border-t border-b hover:bg-gray-100">
                                <td class="px-4 py-2">{{ $role->name }}</td>
                                <td class="px-4 py-2">
                                    <div class="btn-group flex justify-end" role="group" aria-label="Button group">
                                        <a type="button" href="{{ route('roles.show', $role->id) }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Show
                                            </button>
                                        </a>
                                        <a type="button" href="{{ route('roles.edit', $role->id) }}">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display: inline">
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
                                <h3 class="text-center">No Roles.</h3>
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