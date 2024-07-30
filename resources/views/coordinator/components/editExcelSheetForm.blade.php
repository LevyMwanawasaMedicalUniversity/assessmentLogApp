<div class="bg-white shadow rounded-lg p-6 border border-gray-300">
    <!-- Header -->
    {{-- <div class="mb-6">
        <h4 class="text-lg font-bold text-gray-800">Upload Excel Sheet Of Marks</h4>
    </div> --}}

    <!-- Alert Messages -->
    <div class="w-full mb-6">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- File Upload Form -->
    <form action="{{ route('coordinator.updateCAFromExcelSheet') }}" method="POST" enctype="multipart/form-data" class="p-4 bg-light border rounded shadow-sm">
        @csrf

        <!-- Form Header -->
        <div class="mb-4">
            <h3 class="font-weight-bold text-primary">Update Course Assessment</h3>
            <p class="text-muted">Please upload the Excel file to update the course assessment information.</p>
        </div>

        <!-- Excel File Input -->
        <div class="form-group mb-4">
            <label for="excelFile" class="font-weight-bold text-lg text-dark">Choose "Excel Workbook" (xlsx) File</label>
            <input type="hidden" name="course_id" value="{{ $courseId }}">
            <input type="hidden" name="course_assessment_id" value="{{ $courseAssessmentId }}">
            <input type="hidden" name="course_code" value="{{ $results->CourseName }}">
            <input type="hidden" name="basicInformationId" value="{{ $basicInformationId }}">
            <input type="hidden" name="study_id" value="{{ $results->StudyID }}">
            <input type="hidden" name="delivery" value="{{ $delivery }}">
            <input type="file" name="excelFile" accept=".xlsx, .xlsm" class="form-control-file" id="excelFileInput" required>
            <small class="form-text text-muted">Accepted formats: .xlsx, .xlsm</small>
        </div>

        <!-- File Preview -->
        <div class="form-group mb-4 d-none" id="filePreview"></div>

        <!-- Loader -->
        <div class="form-group mb-4 d-none text-center" id="loader">
            <div id="percentage" class="text-muted">0%</div>
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p>Loading...</p>
        </div>

        <!-- Academic Year Dropdown -->
        <div class="form-group mb-6">
            <label for="academicYear" class="font-weight-bold text-lg text-dark">Academic Year</label>
            <select name="academicYear" class="form-control w-auto" required>
                @for ($year = 2019; $year <= date('Y'); $year++)
                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="btn btn-primary btn-block">
                Upload
            </button>
        </div>
    </form>
</div>
