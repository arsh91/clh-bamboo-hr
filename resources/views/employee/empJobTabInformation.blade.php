@extends('layouts.app')
@section('title', 'Employee Job Tab Details')
@section('content')

<section class="section profile">
<div class="row">
    <div class="col-xl-4">

        <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="<?php echo $base64Image; ?>" alt="API Image">
                <h2 class="text-center">{{$empData['firstName']}} {{$empData['lastName']}}</h2>
                <h3>{{$empData['jobTitle']}}</h3>
                <a href="https://clhmentalhealth.bamboohr.com/employees/employee.php?id={{$empData['ID']}}" target="_blank" class="btn btn-success">Link To Bamboo Hr</a>
            </div>
        </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->              
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">

                  <h5 class="card-title">Profile Details</h5>
                  
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Department</div>
                    <div class="col-lg-9 col-md-8">{{$empData['department']}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Job</div>
                    <div class="col-lg-9 col-md-8">{{$empData['jobTitle']}}</div>
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
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
           
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Employment Status</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Effective Date</th>
                            <th scope="col">Employment Status</th>                            
                            <th scope="col">Comment</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['employmentStatus']))
                                @foreach($allTableArray['employmentStatus'] as $empStatus)
                                <tr>
                                    <td>{{$empStatus[0]}}</td>
                                    <td>{{$empStatus[1]}}</td>
                                    <td>{{$empStatus[2]}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="3">No records found.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##employement status-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Compensation</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Effective Date</th>
                            <th scope="col">Pay Schedule</th>                            
                            <th scope="col">Pay Type</th>
                            <th scope="col">Pay Rate</th>
                            <th scope="col">Overtime</th>
                            <th scope="col">Change Reason</th>
                            <th scope="col">Comment</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['compensation']))
                                @foreach($allTableArray['compensation'] as $empComp)
                                <tr>
                                    <td>{{$empComp[0]}}</td>
                                    <td>{{$empComp[7]}}</td>
                                    <td>{{$empComp[2]}}</td>
                                    <td>{{$empComp[1]}}</td>
                                    <td>{{$empComp[3]}}</td>
                                    <td>{{$empComp[4]}}</td>
                                    <td>{{$empComp[5]}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="7">No compensation entries have been added.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##compensation-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Direct Deposit Information</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Bank Name</th>
                            <th scope="col">Bank Account Type</th>                            
                            <th scope="col">Routing Number</th>
                            <th scope="col">Account Number</th>
                            <th scope="col">Percentage</th>
                            <th scope="col">Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['customDirectDepositInformation']))
                                @foreach($allTableArray['customDirectDepositInformation'] as $empDirectDeposit)
                                <tr>
                                    <td>{{$empDirectDeposit[0]}}</td>
                                    <td>{{$empDirectDeposit[1]}}</td>
                                    <td>{{$empDirectDeposit[2]}}</td>
                                    <td>{{$empDirectDeposit[3]}}</td>
                                    <td>{{$empDirectDeposit[4]}}</td>
                                    <td>{{$empDirectDeposit[5]}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="6">No direct deposit information entries have been added.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##direct deposit information-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Federal Income Tax Information</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Federal Filing Status</th>
                            <th scope="col">Federal Allowances</th>                            
                            <th scope="col">Federal Additional Withholding Amount</th>
                            <th scope="col">Income Tax Filing State</th>
                            <th scope="col">Unemployment Filing State</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['customFederalIncomeTaxInformation']))
                                @foreach($allTableArray['customFederalIncomeTaxInformation'] as $empFederalIncomeTax)
                                <tr>
                                    <td>{{$empFederalIncomeTax[0]}}</td>
                                    <td>{{$empFederalIncomeTax[4]}}</td>
                                    <td>{{$empFederalIncomeTax[1]}}</td>
                                    <td>{{$empFederalIncomeTax[2]}}</td>
                                    <td>{{$empFederalIncomeTax[3]}}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5">No federal income tax information entries have been added.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##state tax-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">State Income Tax Filing Information</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">State Filing Status</th>
                            <th scope="col">State Allowances</th>                            
                            <th scope="col">State Additional Withholding Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['customStateIncomeTaxFilingInformation']))
                                @foreach($allTableArray['customStateIncomeTaxFilingInformation'] as $empStateTax)
                                <tr>
                                    <td>{{$empStateTax[0]}}</td>
                                    <td>{{$empStateTax[1]}}</td>
                                    <td>{{$empStateTax[2]}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="3">No state income tax filing information entries have been added.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##state tax-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Requisition</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Date Requested</th>
                                <th scope="col">Name</th>                            
                                <th scope="col">Intended Date of Hire</th>
                                <th scope="col">Department 1</th>                            
                                <th scope="col">Reason for Requisition</th>                            
                                <th scope="col">Job Title 1</th>                            
                                <th scope="col">Job Title 2</th>                            
                                <th scope="col">Pay Rate</th>                            
                                <th scope="col">Paid Per</th>                            
                                <th scope="col">Comment</th>                            
                                <th scope="col">Benefits</th>                            
                                <th scope="col">Location</th>                            
                                <th scope="col">Full-Time/Part-Time</th>                            
                                <th scope="col">Phone</th>                            

                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($allTableArray['customRequisition']))
                                    @foreach($allTableArray['customRequisition'] as $empReq)
                                    <tr>
                                        <td>{{$empReq[0]}}</td>
                                        <td>{{$empReq[1]}}</td>
                                        <td>{{$empReq[2]}}</td>
                                        <td>{{$empReq[6]}}</td>
                                        <td>{{$empReq[7]}}</td>
                                        <td>{{$empReq[8]}}</td>
                                        <td>{{$empReq[9]}}</td>
                                        <td>{{$empReq[3]}}</td>
                                        <td>{{$empReq[4]}}</td>
                                        <td>{{$empReq[5]}}</td>
                                        <td>{{$empReq[10]}}</td>
                                        <td>{{$empReq[11]}}</td>
                                        <td>{{$empReq[12]}}</td>
                                        <td>{{$empReq[13]}}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="14">No requisition entries have been added.</td></tr>
                                @endif                                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!--##Requisition tax-->

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of References</h5>
                    <table class="table table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Names</th>
                            <th scope="col">Emails</th>
                            <th scope="col">Phone Numbers</th>
                            <th scope="col">Checked</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if(!empty($allTableArray['customListofReferences']))
                                @foreach($allTableArray['customListofReferences'] as $empReferenceList)
                                <tr>
                                    <td>{{$empReferenceList[0]}}</td>
                                    <td>{{$empReferenceList[1]}}</td>
                                    <td>{{$empReferenceList[2]}}</td>
                                    <td>{{$empReferenceList[3]}}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="4">No list of references entries have been added.</td></tr>
                            @endif                                                    
                        </tbody>
                    </table>
                </div>
            </div><!--##state tax-->
            <a href="{{ route('employees.detail', $empId) }}" class="btn btn-warning"> <i class="bi bi-arrow-left"></i> Go Back</a>

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