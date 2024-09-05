<x-app-layout>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>{{$studentDetails->ID}} Results Details</h1>
        @include('layouts.alerts')
        <nav>
            {{-- {{ Breadcrumbs::render() }} --}}
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Student Details</h5>
                                        <div class="row">
                                            <!-- Image Section -->
                                            <div class="col-12 text-center mb-3">
                                                <div style="width: 180px; height: 200px; overflow: hidden; border: 2px solid black; margin: 0 auto;">
                                                    <img src="//edurole.lmmu.ac.zm/datastore/identities/pictures/{{ $studentDetails->ID }}.png" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </div>

                                            <!-- Student Details -->
                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">Student Name</small>
                                                <p class="mb-1">{{$studentDetails->FirstName}} {{$studentDetails->Surname}}</p>
                                            </div>

                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">Student Number</small>
                                                <p class="mb-1">{{$studentDetails->ID}}</p>
                                            </div>

                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">Mode of Study</small>
                                                <p class="mb-1">{{$studentDetails->StudyType}}</p>
                                            </div>

                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">Email</small>
                                                <p class="mb-1">{{$studentDetails->PrivateEmail}}</p>
                                            </div>

                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">Programme</small>
                                                <p class="mb-1">{{$studentDetails->Name}}</p>
                                            </div>

                                            <div class="col-12 text-center mb-2">
                                                <small class="text-muted">School</small>
                                                <p class="mb-1">{{$studentDetails->Description}}</p>
                                            </div>
                                        </div>
                                                                                </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{$results->first()->assesment_type_name}} results for {{$results->first()->course_code}} {{$componentName}}</h4>
                                            
                                        
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-primary">
                                                <tr>
                                                    {{-- <th>#</th> --}}
                                                    <th>{{$results->first()->assesment_type_name}}</th>                             
                                                    <th>Mark</th>   
                                                    <th class="text-end">Details</th>
                                                </tr>
                                                </thead>
                                                <tbody> 
                                                    @foreach($results as $result)
                                                    @php
                                                        

                                                    @endphp
                                                        <tr>                                    
                                                            {{-- <td>{{$loop->iteration}}</td> --}}
                                                            <td>{{$result->assesment_type_name}} {{$loop->iteration}}</td>                                    
                                                            {{-- <td >{{$result->cas_score}} %</td>  --}}
                                                            <td>
                                                                <span class="badge bg-primary">{{$result->cas_score}}%</span>
                                                            </td>
                                                            @if($result->description)
                                                            <td class="text-end">{{$result->description}}</td> 
                                                            @else                                  
                                                            <td class="text-end">None Provided</td>
                                                            @endif 
                                                        </tr>
                                                    @endforeach                  
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>     
            </div>
        </div>
    </section>
</main><!-- End #main -->

<script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        XLSX.writeFile(wb, "ALL COORDINATORS.xlsx");
    });
</script>
</x-app-layout>
