<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="modal fade" id="viewCourseModal{{ $result->ID }}" tabindex="-1" role="dialog" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="viewCourseModalLabel">
                        <b>Select Assessment Type</b>
                    </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Course Name: <span ><b>{{$result->CourseDescription}} {{$result->CourseName}} {{$result->ID}}</b></span>

                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="grid grid-rows-3 gap-6"> <!-- Grid with 3 equal-width columns -->
                            <!-- First Block -->
                            <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                @csrf
                                <input type="hidden" name="statusId" value="1">
                                <input type="hidden" name="courseIdValue" value={{$result->ID}}>
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid green;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Assignment") }}
                                    </div>
                                </button>
                            </form>

                            <!-- Second Block -->
                            <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                @csrf
                                <input type="hidden" name="statusId" value="2">
                                <input type="hidden" name="courseIdValue" value={{$result->ID}}>
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid blue;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Test") }}
                                    </div>
                                </button>
                            </form>

                            <!-- Third Block -->
                            <form method="POST" action="{{ route('coordinator.uploadCa') }}">
                                @csrf
                                <input type="hidden" name="statusId" value="3">
                                <input type="hidden" name="courseIdValue" value={{$result->ID}}>
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid red;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Mock") }}
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Course details go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>