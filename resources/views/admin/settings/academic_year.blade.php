@extends('layouts.admin')

@section('title', 'Academic Year Settings')
@section('breadcrumb', 'Academic Year Settings')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Academic Year Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p>The academic year setting is used throughout the application for all assessment-related operations. 
                        Changing this value will affect all new assessments and imports.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.academic_year.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label for="academic_year" class="form-label">Current Academic Year</label>
                            <input type="number" class="form-control" id="academic_year" name="academic_year" 
                                   value="{{ old('academic_year', $currentAcademicYear) }}" 
                                   min="2000" max="2100" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Academic Year</button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">Back to Settings</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
