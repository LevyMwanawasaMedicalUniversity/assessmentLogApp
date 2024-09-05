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
                    <div class="card-body">
                        <h5 class="card-title">Student Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-4 col-form-label">Student Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->FirstName}} {{$studentDetails->Surname}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="studentNumber" class="col-sm-4 col-form-label">Student Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->ID}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="studyMode" class="col-sm-4 col-form-label">Mode Of Study</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->StudyType}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="email" class="col-sm-4 col-form-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->PrivateEmail}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="programme" class="col-sm-4 col-form-label">Programme</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->Name}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="school" class="col-sm-4 col-form-label">School</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="{{$studentDetails->Description}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{$results->first()->assesment_type_name}} results for {{$results->first()->course_code}} {{$componentName}}</h4>
                        <div class="col-md-12">
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

                                @if (session('warning'))
                                    <div class="alert alert-warning">
                                        {{ session('warning') }}
                                    </div>
                                @endif
                            </div>
                        {{-- <div class="col-md-12">
                            <div class="alert alert-info">
                                <p class="text-white">Below are the marks recorded for each {{$results->first()->assesment_type_name}} in {{$results->first()->course_code}}</p>
                            </div>
                        </div> --}}
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
