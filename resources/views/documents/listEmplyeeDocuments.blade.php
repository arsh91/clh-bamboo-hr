@extends('layouts.app')
@section('title', 'Employee Detail')
@section('content')

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
                </div>
              </div>
            </div>
          </div>
        </div><!--##col-xl-8-->
      </div><!--##profile ROW-->
      

      <!--##DOCUMENTS ROW--->
      <div class="row">
        <div class="card">
          <div class="card-body">         
            <ul class="nav nav-tabs nav-tabs-bordered" id="myTabjustified" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link w-100 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-justified" type="button" role="tab" aria-controls="home" aria-selected="true">Document</button>
              </li>
            </ul>
            <div class="tab-content pt-2" id="myTabjustifiedContent">
                <div class="tab-pane fade active show" id="home-justified" role="tabpanel" aria-labelledby="home-tab">
                  <div class="row">
                    <div class="col-lg-6">                      
                      <h5 class="card-title">Document List</h5>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div><!--##row-->
              
      
      <!---SHOW THE Expire Date tracker data--->
      
      <!---##Date tracker--->
    </section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
$(document).ready(function() {

});
</script>
<!--end::Page Custom Javascript-->
@endsection