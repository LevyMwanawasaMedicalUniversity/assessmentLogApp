<x-app-layout>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Set Components</h1>
        @include('layouts.alerts')
        {{-- <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                Please ensure that you make your upload under the correct delivery mode (<span style="color:blue"><b>Fulltime</b></span> or <span style="color:green"><b>Distance</b></span>) for each course.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> --}}
        <nav>
            {{-- {{ Breadcrumbs::render() }} --}}
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <form method="POST" action="{{ route('coordinator.updateCourseWithComponents', $courseId) }}" class="row g-3">
                        {{-- @method('patch') --}}
                        @csrf
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title">{{$courseDetails->CourseDescription}}</h5>
                                
                            </div>
                            
                            <table id="myTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Select</th>
                                        <th scope="col" class="text-end">Component Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courseComponents as $courseComponent)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="hidden" name="courseId" value="{{ $courseId }}">
                                                <input type="hidden" name="basicInformationId" value="{{ $basicInformationId }}">
                                                <input type="hidden" name="delivery" value="{{ $delivery }}">
                                                <input type="hidden" name="studyId" value="{{ $studyId }}">
                                                <input type="hidden" name="academicYear" value="{{ $academicYear }}">
                                                <input type="checkbox"
                                                    name="courseComponent[{{ $courseComponent->course_components_id }}]"
                                                    value="{{ $courseComponent->course_components_id }}"
                                                    class='courseComponent'
                                                    {{ in_array($courseComponent->course_components_id, $courseComponentAllocated) ? 'checked' : '' }}
                                                    onclick="toggleInput(this, {{ in_array($courseComponent->course_components_id, $courseComponentAllocated) ? 1 : 0 }})">
                                            </td>
                                            <td class="text-end">{{ $courseComponent->component_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">                                    
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-4 rounded-0">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
</x-app-layout>