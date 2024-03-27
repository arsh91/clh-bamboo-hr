@extends('layouts.app')
@section('title', 'Employee Detail')
@section('content')
<style>
.highlight {
    background-color: yellow; /* Set the background color to yellow */
}

</style>

<section class="section profile">
      <div class="row">
        <div class="col-xl-4">

        <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="<?php echo $base64Image; ?>" alt="API Image">
                <h2>{{$empData['firstName']}} {{$empData['lastName']}}</h2>
                <h3>{{$empData['jobTitle']}}</h3>
                <a href="https://clhmentalhealth.bamboohr.com/employees/employee.php?id={{$empData['ID']}}" target="_blank" class="btn btn-success">Link To Bamboo Hr</a>
            </div>
        </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview" aria-selected="true" role="tab">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-job" aria-selected="true" role="tab">Job</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-emergency" aria-selected="true" role="tab">Emergency</button>
                </li>
              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8">{{$empData['firstName']}} {{$empData['lastName']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Department</div>
                    <div class="col-lg-9 col-md-8">{{$empData['department']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Job</div>
                    <div class="col-lg-9 col-md-8">{{$empData['jobTitle']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Location</div>
                    <div class="col-lg-9 col-md-8">{{$empData['location']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8">{{$empData['mobilePhone']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8">{{$empData['workEmail']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Supervisor</div>
                    <div class="col-lg-9 col-md-8">{{$empData['supervisor']}}</div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Status</div>
                    <div class="col-lg-9 col-md-8">
                    @if (array_key_exists('employmentStatus', $empData))

                      @if($empData['employmentStatus'] == 'Active')
                        <button type="button" class="btn btn-success mb-2">{{$empData['employmentStatus']}}</button></div>
                      @elseif($empData['employmentStatus'] == 'Inactive')
                        <button type="button" class="btn btn-danger mb-2">{{$empData['employmentStatus']}}</button></div>
                      @endif
                    @else
                    N/A
                    @endif
                      </div>

                </div>

                <!--second tab-->
                <div class="tab-pane fade profile-edit pt-3" id="profile-job" role="tabpanel">

                  <!-- Profile Edit Form -->
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Job Information Table</h5>

                      <!-- Table with hoverable rows -->
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Effective Date</th>
                            <th scope="col">Location</th>                            
                            <th scope="col">Department</th>
                            <th scope="col">Division</th>
                            <th scope="col">Job Title	</th>
                            <th scope="col">Reports To	</th>
                          </tr>
                        </thead>
                        <tbody>
                          
                          @if(!empty($jobFields))
                            @foreach($jobFields as $key=> $jobs)
                              <tr>
                                <td>{{$jobs[0]}}</td>
                                <td>{{$jobs[1]}}</td>
                                <td>{{$jobs[2]}}</td>
                                <td>{{$jobs[3]}}</td>
                                <td>{{$jobs[4]}}</td>
                                <td>{{$jobs[5]}}</td>
                              </tr>
                            @endforeach
                            @else
                              <tr><td colpan="4">No records found for the employee.</td></tr>
                            @endif
                                                    
                        </tbody>
                      </table>
                      <!-- End Table with hoverable rows -->
                    </div>
                  </div>
                </div><!--###end of second tab--->

                <!--emergency third tab-->
                <div class="tab-pane fade profile-edit pt-3" id="profile-emergency" role="tabpanel">

                  <!-- Profile Edit Form -->
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Emergency Contact</h5>

                      <!-- Table with hoverable rows -->
                      <table class="table table-hover" id="emergency_table">
                        <thead>
                          <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Relationship</th>
                            <th scope="col">homePhone</th>
                            <th scope="col">addressLine1</th>
                            <th scope="col">addressLine2	</th>
                            <th scope="col">mobilePhone	</th>
                            <th scope="col">email	</th>
                            <th scope="col">zipcode	</th>
                            <th scope="col">city	</th>
                            <th scope="col">state	</th>
                            <th scope="col">country	</th>
                            <th scope="col">workPhone	</th>
                            <th scope="col">workPhoneExtension	</th>
                            <th scope="col">primaryContact	</th>
                          </tr>
                        </thead>
                        <tbody>
                          
                          @if(!empty($emergencyContacts)) 
                          @php
                              
                              $collectionLength = count($emergencyContacts);
                          @endphp
                           <tr>
                              @for ($i = 0; $i < $collectionLength; $i++)
                             
                                <td>
                                  {{ $emergencyContacts[$i] }}
                                </td>
                              @endfor
                            </tr>
                            @else
                              <!--<tr><td colspan="12">No records found for the employee.</td></tr>-->
                            @endif
                                                    
                        </tbody>
                      </table>
                      <!-- End Table with hoverable rows -->
                    </div>
                  </div>
                </div><!--###end of second tab--->

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
      <div class="row">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Emergency Contact Empty Fields</h5>
            <ul class="list-group">
              @if(!empty($emptyEmeregencyFields))
              @foreach($emptyEmeregencyFields as $key=> $val)
              <li class="list-group-item">
                {{$val}}
              </li>
              @endforeach
              @endif
            </ul>
          </div>
        </div>
    </section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
$(document).ready(function() {
    var table = $('#emergency_table').DataTable({
            "scrollX": true, // Enable horizontal scrolling
            "scrollY": "400px", // Set the height of the vertical scrolling
            "scrollCollapse": true, // Allow the table to be scrolled without the scrollbar being visible
            "paging": false // Disable pagination (optional)
        });
});
</script>
<!--end::Page Custom Javascript-->
@endsection