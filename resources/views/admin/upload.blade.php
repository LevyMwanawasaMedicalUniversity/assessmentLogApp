<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $results->CourseDescription }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full px-4">
                                <div class="bg-white shadow rounded-lg">
                                    <div class="px-6 py-4">
                                        <h4 class="text-lg font-bold">Import Student and Send Dockets</h4>

                                        <div class="w-full">
                                            @if (session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            @if (session('error'))
                                                <div class="alert alert-danger">
                                                    {{ session('error') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="px-6 py-4">
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="excelFile" class="font-bold text-lg">Choose Excel (xlsx) File</label>
                                                <input type="file" name="excelFile" accept=".xlsx" class="form-control" id="excelFileInput" class="border-2 border-blue-500 p-2 rounded bg-gray-200 cursor-pointer">
                                            </div>
                                            <div class="mb-4">
                                                <div id="filePreview" class="hidden"></div>
                                            </div>
                                            <div class="loader" id="loader" class="hidden">
                                                <div id="percentage">0%</div>
                                                <!-- You can add loading spinner or text here -->
                                                Loading...
                                            </div>
                                            <div class="flex flex-wrap -mx-4">
                                                <div class="w-full md:w-1/3 px-4">
                                                    <div class="mb-4">
                                                        <label for="academicYear" class="font-bold text-lg">Academic Year</label>
                                                        <select name="academicYear" class="form-control" required class="w-full p-2 border rounded">
                                                            <option value="2023">2023</option>
                                                            <option value="2024">2024</option>
                                                            <option value="2025">2025</option>
                                                            <option value="2026">2026</option>
                                                            <option value="2027">2027</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="w-full md:w-1/3 px-4">
                                                    <div class="mb-4">
                                                        <label for="term" class="font-bold text-lg">Term</label>
                                                        <select name="term" class="form-control" required class="w-full p-2 border rounded">
                                                            <option value="Term-2">Term-2</option>
                                                            <option value="Term-1">Term-1</option>                                    
                                                            <option value="Term-3">Term-3</option>
                                                            <option value="Term-4">Term-4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="w-full md:w-1/3 px-4">
                                                    <div class="mb-4">
                                                        <label for="status" class="font-bold text-lg">Type Of Exam</label>
                                                        <select name="status" class="form-control" required class="w-full p-2 border rounded">
                                                            <option value="3">Deferred And Sups</option>
                                                            <option value="1">LMMU Exam</option>   
                                                            <option value="2">NMCZ Exam</option>                                                                  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary bg-blue-500 text-white rounded px-4 py-2">Upload</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        const excelFileInput = document.getElementById('excelFileInput');
                        const filePreview = document.getElementById('filePreview');
                        const loader = document.getElementById('loader');
                        const percentage = document.getElementById('percentage');

                        excelFileInput.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const preview = document.createElement('div');
                                    preview.innerHTML = `
                                        <strong>File Preview:</strong><br>
                                        File Name: ${file.name}<br>
                                        File Type: ${file.type}<br>
                                        File Size: ${formatBytes(file.size)}
                                    `;
                                    filePreview.innerHTML = '';
                                    filePreview.appendChild(preview);
                                    filePreview.style.display = 'block';
                                };
                                reader.readAsDataURL(file);
                            } else {
                                filePreview.style.display = 'none';
                            }
                        });

                        function formatBytes(bytes, decimals = 2) {
                            if (bytes === 0) return '0 Bytes';

                            const k = 1024;
                            const dm = decimals < 0 ? 0 : decimals;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

                            const i = Math.floor(Math.log(bytes) / Math.log(k));

                            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                        }

                        document.querySelector('form').addEventListener('submit', (e) => {
                            loader.style.display = 'block';
                            const form = e.target;
                            const formData = new FormData(form);

                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', form.action);

                            // Progress event to update percentage
                            xhr.upload.addEventListener('progress', (event) => {
                            if (event.lengthComputable) {
                                const percentComplete = (event.loaded / event.total) * 100;
                                percentage.textContent = percentComplete.toFixed(2) + '%';
                            }
                        });

                        xhr.send(formData);
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>