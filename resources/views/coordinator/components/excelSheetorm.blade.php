<div class="bg-white shadow rounded-lg p-6">
    <!-- Header -->
    <div class="mb-6">
        <h4 class="text-lg font-bold text-gray-800">Import Student and Send Dockets</h4>
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
    <form action="" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Excel File Input -->
        <div class="mb-4">
            <label for="excelFile" class="font-bold text-lg text-gray-700">Choose Excel (xlsx) File</label>
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
                <option value="2023">2023  </option>
                <option value="2024">2024  </option>
                <option value="2025">2025  </option>
                <option value="2026">2026  </option>
                <option value="2027">2027  </option>
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