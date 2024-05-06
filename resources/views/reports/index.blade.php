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
    <div class="link-div mb-3">
          @if($report_status && ($report_status['status']=== 'requested' || $report_status['status']=== 'inprocess'))
          <p class="processingButton">
              Report generation is In Progress. We'll share the link to download the report in 4-5 mins.
          </p>
          

          @else
              <a href="javascript:void(0);" id="generateData" class="btn btn-success">Generate Data</a>
              @if($report_status && $report_status['status']=== 'created')
              {{$report_status['date']}}
              @endif
              <p class="processingButton d-none">
              Report generation is In Progress. We'll share the link to download the report in 4-5 mins.
              </p>
          @endif
          </div>
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                <!-- @if(auth()->user()->role->name == 'SUPER_ADMIN' || auth()->user()->role->name == 'ADMIN') -->
                <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Job <span>| Empty Field Job Count</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h6>{{$empty_job_field}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('job')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Personal <span>| Empty Field Personal Count</span></h5>

                  <div class="d-flex align-items-center">
                
                    <div class="ps-3">
                      <h6>{{$empty_personal_field}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('personal')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Emergency <span>| Empty Field Emergency Count</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h6>{{$empty_emergency_field}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('emergency')" href="javascript:void(0)">View All</button>

                </div>

              </div>
            </div>
            </div><!-- End Right side columns -->
            <div class="row">
                <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Expired Time Tracker  <span>| Expired Time Tracker Count</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h6>{{$expired}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('expired')" href="javascript:void(0)">View All</button>
                </div>  
              </div>
            </div>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                <h5 class="card-title">Going To Expire Time Tracker <span>| Going To Expire Time Tracker Count</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h6>{{$going_to_expire}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('going_to_expire')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
          
                <!-- @endif -->
            </div><!-- End Right side columns -->
            <div class="row">
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Empty Doucument <span>| Empty Doucument Count</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h6>{{$empty_doucument_field}}</h6>
                    </div>
                  </div>
                  <button class="btn btn-default my-3" onclick="openusersModal('document')" href="javascript:void(0)">View All</button>
                </div>

              </div>
            </div>
            </div>
        </div>


        <!--start: Add users Modal -->
        <div class="modal fade" id="addUsers" tabindex="-1" aria-labelledby="role" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="width:505px;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detail-heading">Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
              
                        <div class=" modal-body" id="detail">
                        <table class="datatable table table-striped my-2" id="employee_detail_table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Employee Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <!-- <tr><td>gfgfhfgh</td><td>gfhgfhgfh</td></tr> -->
                                        </tbody>
                                    </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
            

</section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
    $(document).ready(function() {
      var table = $('#employee_detail_table').DataTable();
      $('#generateData').on('click', function() {
        $(this).hide();
        $('.downloadLink').hide();
        $('.processingButton').removeClass('d-none').attr('disabled', true);
        $.ajax({
                url: '/generate-data', 
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
   
                success: function(response){
                 
                },
                error: function(xhr, status, error){  
                    console.error(error);
                }
        });
    });
});
function openusersModal(tab) {

    $('#addUsers').modal('show');
    var heading = tab.replace(/_/g, " ");
                  $('#detail-heading').text(heading.charAt(0).toUpperCase() + heading.substring(1) + ' detail');
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
                  $('#employee_detail_table').DataTable().destroy();
                  $('#employee_detail_table tbody').empty();
                
                // Append new data to the table
                $.each(response, function(index, item) {
                    $('#employee_detail_table tbody').append('<tr><td>' + item.name + '</td><td>' + item.count + '</td></tr>');
                });
                $('#employee_detail_table').DataTable();
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });


    
  }



</script>
<!--end::Page Custom Javascript-->
@endsection