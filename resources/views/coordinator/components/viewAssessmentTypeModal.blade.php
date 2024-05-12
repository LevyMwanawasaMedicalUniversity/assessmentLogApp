<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="modal fade" id="viewCourseModal{{ $result->ID }}" tabindex="-1" role="dialog" aria-labelledby="viewCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="viewCourseModalLabel">
                        <b>Select Assessment Type To View</b>
                    </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <b><span >{{$result->CourseDescription}} - {{$result->CourseName}}</span></b>

                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="flex flex-col space-y-6"> <!-- Flex container with vertical spacing -->
                            <!-- First Block -->
                            <form method="GET" action="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt(1), 'courseIdValue' => encrypt($result->ID)]) }}">
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid green;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Assignment") }}
                                    </div>
                                </button>
                            </form>

                            <!-- Second Block -->
                            <form method="GET" action="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt(2), 'courseIdValue' => encrypt($result->ID)]) }}">
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid blue;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Test") }}
                                    </div>
                                </button>
                            </form>

                            <!-- Third Block -->
                            <form method="GET" action="{{ route('coordinator.viewAllCaInCourse', ['statusId' => encrypt(3), 'courseIdValue' => encrypt($result->ID)]) }}">
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid red;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Mock") }}
                                    </div>
                                </button>
                            </form>

                            <form method="GET" action="{{ route('coordinator.viewTotalCaInCourse', ['statusId' => encrypt($result->caType),'courseIdValue' => encrypt($result->ID)]) }}">
                                <button type="submit" class="w-full bg-white overflow-hidden shadow-sm sm:rounded-lg transform transition-transform duration-500 hover:scale-105 text-center" style="border: 2px solid black;">
                                    <div class="p-6 text-gray-900">
                                        {{ __("Total CA") }}
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