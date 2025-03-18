<x-app-layout>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>My Courses</h1>
            @include('layouts.alerts')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                Welcome to your courses dashboard. Here you can view, upload, and manage your course assessments.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <!-- Include the Livewire component -->
        @livewire('coordinator.coordinator-courses', ['basicInformationId' => $basicInformationId])

    </main>

    <!-- Include the necessary scripts for Excel export -->
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
</x-app-layout>
