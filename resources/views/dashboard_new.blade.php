<x-app-layout>
    <main id="main" class="main">

        <div class="pagetitle">
        <h1>Dashboard</h1>
        @include('layouts.alerts')
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>

        <section class="section dashboard">
        <div class="row">

            <div class="col-lg-8">
            <div class="row">

                <div class="col-xxl-4 col-md-4">
                    @livewire('dashboard.students-with-ca')

                <div class="col-xxl-4 col-md-4">
                    @livewire('dashboard.courses-from-edurole')

                <div class="col-xxl-4 col-md-4">
                    @livewire('dashboard.courses-from-lmmax')

                <div class="col-12">
                    @livewire('dashboard.course-with-ca-per-programme')

                <div class="col-12">
                    @livewire('dashboard.ca-per-school')

                <div class="col-12">
                    @livewire('dashboard.deans-per-school')

            </div>

            <div class="col-lg-4">
                @livewire('dashboard.coordinators-traffic')

        </div>
        </section>

</x-app-layout>
