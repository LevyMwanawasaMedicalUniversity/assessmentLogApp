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
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input value="{{ old('name') }}" 
                                type="text" 
                                class="form-control" 
                                name="name" 
                                placeholder="Name" required>
                        </div>
        
                        <label for="permissions" class="form-label">Assign Permissions</label>
        
                        <table class="table table-striped">
                            <thead>
                                <th scope="col" width="1%"></th>
                                <th scope="col" width="20%">Name</th>
                                <th scope="col" width="1%">Guard</th> 
                            </thead>
        
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        <input type="checkbox" 
                                        name="permission[{{ $permission->name }}]"
                                        value="{{ $permission->name }}"
                                        class='permission'>
                                    </td>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->guard_name }}</td>
                                </tr>
                            @endforeach
                        </table>
        
                        <button type="submit" class="btn btn-primary"><i class="ri ri-check-double-line"></i> Save Role</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-dark"> <i class="ri ri-arrow-right-fill"></i>Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>