<div class="bg-white shadow rounded-lg p-6 border border-gray-300">
    <!-- Header -->
    <div class="mb-6">
        <h4 class="text-lg font-bold text-gray-800">Upload Excel Sheet Of Marks</h4>
    </div>

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
    <form action="{{route('coordinator.importCAFromExcelSheet')}}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Excel File Input -->
        <div class="mb-4">
            <label for="excelFile" class="font-bold text-lg text-gray-700">Choose Excel (xlsx) File</label>
            <input type="hidden" name="ca_type" value={{$caType}}>
            <input type="hidden" name="course_id" value={{$courseId}}>
            <input type="hidden" name="course_code" value={{$results->CourseName}}>
            <input type="hidden" name="basicInformationId" value={{$results->basicInformationId}}>            
            <input type="file" name="excelFile" accept=".xlsx" class="w-full border border-gray-300 rounded p-2 bg-white" id="excelFileInput">
        </div>

        <!-- File Preview -->
        <div class="mb-4 hidden" id="filePreview"></div>

        <!-- Loader -->
        <div class="mb-4 hidden" id="loader">
            <div id="percentage" class="text-gray-600">0%</div>
            <p>Loading...</p>
        </div>

        <!-- Academic Year Dropdown -->
        <div class="mb-6">
            <label for="academicYear" class="font-bold text-lg text-gray-700">Academic Year</label>
            <select name="academicYear" class="w-auto border border-gray-300 rounded p-2 bg-white" required>
                @for ($year = 2019; $year <= date('Y'); $year++)
                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-600">
                Upload
            </button>
        </div>
    </form>
</div>