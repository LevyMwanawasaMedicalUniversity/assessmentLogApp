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
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input value="{{ old('name') }}" 
                                type="text" 
                                class="form-control" 
                                name="name" 
                                placeholder="Name" required>
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input value="{{ old('email') }}"
                                type="email" 
                                class="form-control" 
                                name="email" 
                                placeholder="Email address" required>
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('role'))
                                <span class="text-danger">{{ $errors->first('role') }}</span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Save User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>