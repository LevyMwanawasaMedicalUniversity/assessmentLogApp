@extends('layouts.admin')

@section('title', 'Application Settings')
@section('breadcrumb', 'Application Settings')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Application Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('admin.settings.academic_year') }}" class="btn btn-primary">
                            Manage Academic Year
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Setting Name</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                    <tr>
                                        <td>{{ $setting->display_name }}</td>
                                        <td>{{ $setting->value }}</td>
                                        <td>{{ $setting->description }}</td>
                                        <td>
                                            <a href="{{ route('admin.settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                        </td>
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
@endsection
