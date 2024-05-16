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
.chart-container canvas#donutChart{
  width: 400px !important;
    height: 400px !important;
 }
 .chart-container {
    display: flex;
    justify-content: center;
    margin: 25px auto;
}
</style>

<section class="section dashboard">
    <div class="row">
    <div class="link-div mb-3">
          @if($report_status && ($report_status['status']=== 'requested' || $report_status['status']=== 'inprocess'))
          <p class="processingButton">
              Report Generation is IN-PROGRESS.
          </p>
          

          @else
              <a href="javascript:void(0);" id="generateData" class="btn btn-success">Generate Data</a>
              @if($report_status && $report_status['status']=== 'created')
              <p id="dateValue">
              Report data generated on: {{$report_status['date']}}
              </p>
              @endif
              <p class="processingButton d-none">
              Report Generation is IN-PROGRESS.
              </p>
          @endif
          </div>
             <!-- Chart -->
        <div class="chart-container">
            <canvas id="donutChart"></canvas>
        </div>
            <!-- Chart end -->
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
        var confirmed = confirm("Generating report will clear the old data and regenerate the report in 5-10 mins. Are you sure?");
        if(confirmed){
          $(this).hide();
          $('#dateValue').hide();
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
        }
    });
});
function openusersModal(tab) {
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
                
                  var arrayDocName = {
                    'License': "Driver's License",
                    'Insurance': "Driver's Insurance",
                    'Record': "Driving Record",
                    'Professional_License': "Professional License",
                    'First_Aid': "First Aid/CPR",
                    'Tact_II': "Tact II",
                    'TB_Test': "TB Test",
                    'Professional_Liability': "Professional Liability",
                    'Other_Professional_License': "Other Professional License",
                    'RCYCP_Certification': "RCYCP Certification",
                    'DEA_Registration': "DEA Registration",
                    'Psychiatric_Nurse_Practitioner_Certification': "Psychiatric Nurse Practitioner Certification",
                    'CDS_Registration': "CDS Registration",
                    'National_Practitioner_Data_Bank': "National Practitioner Data Bank",
                    'Annual_Evaluation': "Annual Evaluation Expiration Date",
                    'Annual_EvaluationJC': "JCAHO/Annual Trainings Expiration Date",
                    'JCAHO': "JCAHO/Annual Trainings Expiration Date",
                    '72_Hour_Treatment_Plan': "72 Hour Treatment Plan",
                    '30_Day_Treatment_Plan': "30 Day Treatment Plan",
                    '90_Day_Treatment_Plan': "90 Day Treatment Plan",
                    'Psych_Evaluation': "Psych Evaluation",
                    'Safe_Environment_Plan': "Safe Environment Plan",
                    'Physical': "Physical",
                    'Dental': "Dental",
                    'Vision': "Vision",
                    'Sexual_Abuse_Awareness': "Sexual Abuse Awareness and Prevention Certification",
                    'Medication_Technician_Certificate': "Medication Technician Certificate"
                };
                $.each(response, function(index, item) {
                  if(tab === 'document' || tab === 'expired'|| tab === 'going_to_expire'){
                    var displayName = arrayDocName.hasOwnProperty(item.name) ? arrayDocName[item.name] : item.name;
                  }else{
                    var displayName = item.name
                  }
                  
                    $('#employee_detail_table tbody').append('<tr><td>' + displayName + '</td><td>' + item.count + '</td></tr>');
                });
                $('#employee_detail_table').DataTable();
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });

            $('#addUsers').modal('show');
    
  }
  
  var expired =  @json($expired);
  var going_to_expire =  @json($going_to_expire);
  var total =  @json($total);
  var not_expired = total - (expired + going_to_expire);
// Data for the chart
var data = {
            labels: ['Expired', 'Going to expired', 'Not Expired'],
            datasets: [{
                data: [expired, going_to_expire, not_expired],
                backgroundColor: [
               
                    '#DC3545',
                    '#FFC107',
                    '#198754'
                ],
                hoverOffset: 4
            }]
        };

        // Create a donut chart
        var donutChart = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: data,
        });

</script>
<!--end::Page Custom Javascript-->
@endsection