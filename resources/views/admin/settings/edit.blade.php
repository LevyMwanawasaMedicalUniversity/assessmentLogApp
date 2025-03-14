@extends('layouts.admin')

@section('title', 'Edit Setting')
@section('breadcrumb', 'Edit Setting')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Setting: {{ $setting->display_name }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update', $setting->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="display_name">Display Name</label>
                            <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name', $setting->display_name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="value">Value</label>
                            @if($setting->type == 'boolean')
                                <select class="form-control" id="value" name="value">
                                    <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            @elseif($setting->type == 'number')
                                <input type="number" class="form-control" id="value" name="value" value="{{ old('value', $setting->value) }}" required>
                            @else
                                <input type="text" class="form-control" id="value" name="value" value="{{ old('value', $setting->value) }}" required>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $setting->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Setting</button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
