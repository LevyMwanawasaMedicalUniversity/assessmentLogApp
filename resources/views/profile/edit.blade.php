<x-app-layout>
    <main id="main">
        <div class="container py-4">
            <h1 class="mb-4">Edit Profile</h1>
            @include('layouts.alerts')
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-4">
                                <!-- Update Profile Information Form -->
                                @include('profile.partials.update-profile-information-form')
                            </div>
                            <div class="mb-4">
                                <!-- Update Password Form -->
                                @include('profile.partials.update-password-form')
                            </div>
                            <!-- Uncomment to include Delete User Form -->
                            {{-- <div class="mb-4">
                                @include('profile.partials.delete-user-form')
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>