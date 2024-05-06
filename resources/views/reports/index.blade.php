@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<style>
.highlight {
    background-color: yellow; /* Set the background color to yellow */
}

.documnenttdx {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.processingButton {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        font-size: 16px;
        display: inline-block;
        width: 100%;
    }
</style>

<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                <!-- @if(auth()->user()->role->name == 'SUPER_ADMIN' || auth()->user()->role->name == 'ADMIN') -->
                <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
<!-- 
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Job <span>| Empty Field Job Count</span></h5>

                  <div class="d-flex align-items-center">
                    <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div> -->
                    <div class="ps-3">
                      <h6>{{$empty_job_field}}</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('job')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
<!-- 
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Personal <span>| Empty Field Personal Count</span></h5>

                  <div class="d-flex align-items-center">
                    <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div> -->
                    <div class="ps-3">
                      <h6>{{$empty_personal_field}}</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('personal')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
<!-- 
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Emergency <span>| Empty Field Emergency Count</span></h5>

                  <div class="d-flex align-items-center">
                    <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div> -->
                    <div class="ps-3">
                      <h6>{{$empty_emergency_field}}</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('emergency')" href="javascript:void(0)">View All</button>

                </div>

              </div>
            </div>
                <!-- Employee related tabs -->
                <!-- <div class="container mt-5">
                          sdfsgfdgf
                </div> -->
                <!-- End Employee Tabs -->
                <!-- @endif -->
            </div><!-- End Right side columns -->
            <div class="row">
                <!-- @if(auth()->user()->role->name == 'SUPER_ADMIN' || auth()->user()->role->name == 'ADMIN') -->
                <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
<!-- 
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Expired Doucument <span>| Expired Doucument Count</span></h5>

                  <div class="d-flex align-items-center">
                    <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div> -->
                    <div class="ps-3">
                      <h6>In progress</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
<!-- 
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                <h5 class="card-title">Going To Expire Doucument <span>| Going To Expire Doucument Count</span></h5>

                  <div class="d-flex align-items-center">
                    <!-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div> -->
                    <div class="ps-3">
                      <h6>In progress</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                </div>

              </div>
            </div>
           
                <!-- Employee related tabs -->
                <!-- <div class="container mt-5">
                          sdfsgfdgf
                </div> -->
                <!-- End Employee Tabs -->
                <!-- @endif -->
            </div><!-- End Right side columns -->
        </div>


                    <!--start: Add users Modal -->
                    <div class="modal fade" id="addUsers" tabindex="-1" aria-labelledby="role" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style="width:505px;">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="role">Add User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                         
                                    <div class=" modal-body" id="detail">
                                       
                                        <div class="row mb-3 mt-4" >
                                            <label for="first_name" class="col-sm-3 col-form-label required">First Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="first_name" id="first_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Add User Modal -->

</section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
    $(document).ready(function() {
   
   
});
function openusersModal(tab) {
    $('#addUsers').modal('show');

    $.ajax({
                url: '/empty-field-detail', 
                method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tab: tab
            },
   
                success: function(response){
                  $('#detail').empty();
                  $.each(response, function(index, item) {
                    var listItem = `
                      <div class="col-sm-9">
                          <p type="text">Field Name: ${item.field_name}</p>
                      </div>
                      <div class="col-sm-9">
                          <p type="text">Employee Count: ${item.field_name}</p>
                      </div>
                      <hr>
                  `;
console.log(listItem);
    
        $('#detail').append(listItem);
    });
                    // $('.document-' + rowId).html(response);
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });
  }



</script>
<!--end::Page Custom Javascript-->
@endsection