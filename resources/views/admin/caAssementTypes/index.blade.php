<x-app-layout>
    <main id="main" class="main">

    <div class="pagetitle">
        <h1>Assessment Types</h1>
        <nav>
            {{ Breadcrumbs::render() }}
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Assessment Types</h5>
                            
                            <a class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('caAssessmentTypes.create') }}">Add CA Type</a>
                            
                        </div>
                        <!-- Table with hoverable rows -->
                        <table id="myTable" class="table table-hover">
                        {{-- <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for courses.." class="shadow appearance-none border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> --}}
                        <thead>
                            <tr>
                            
                            {{-- <th scope="col">#</th> --}}
                            <th scope="col">Name</th>
                            <th scope="col">Creation date</th>
                            <th class="text-right" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($assessmentTypes as $assessmentType)
                            
                            <tr>
                                {{-- <th scope="row">1</th> --}}
                                <td>{{$assessmentType->assesment_type_name}}</td>
                                <td>{{$assessmentType->created_at}}</td>
                                <td class="text-right">
                                    <div  class="btn-group text-right" role="group" aria-label="Button group">
                                        
                                        <a href="{{ route('caAssessmentTypes.edit', $assessmentType->id) }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-l-none">
                                                Edit
                                            </button>
                                        </a>
                                        <form method="POST" action="{{ route('caAssessmentTypes.destroy', $assessmentType->id) }}" style="display: inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-l-none" onclick="return confirm('Are you sure you want to delete this item?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td> 
                            </tr>                            
                            @endforeach
                            
                        </tbody>
                        </table>
                        {{-- {!! $assessmentType->links('pagination::bootstrap-4') !!} --}}
                        <!-- End Table with hoverable rows -->

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
    

    

</x-app-layout>