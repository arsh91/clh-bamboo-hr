@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<style>
.highlight {
    background-color: yellow; /* Set the background color to yellow */
}

</style>

<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                @if(auth()->user()->role->name == 'SUPER_ADMIN')
                <!-- Employee related tabs -->
                <div class="container mt-5">
                    <!-- Bootstrap Tabs -->
                    <!-- <ul class="nav nav-tabs d-flex" id="myTabjustified" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-justified" type="button" role="tab" aria-controls="home" aria-selected="false" tabindex="-1">Employee's Information</button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-justified" type="button" role="tab" aria-controls="profile" aria-selected="true">Blank Fields</button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-justified" type="button" role="tab" aria-controls="contact" aria-selected="false" tabindex="-1">Training Overview From TalentLMS</button>
                        </li>
                    </ul> -->

                    <!-- Tab content -->
                    <!-- <div class="tab-content pt-2" id="myTabjustifiedContent"> -->
                        <!-- <div class="tab-pane fade active show" id="home-justified" role="tabpanel" aria-labelledby="home-tab"> -->
                            <!-- Table with stripped rows -->
                            <div class="box-header with-border" id="filter-box">
                                <div class="box-body table-responsive" style="margin-bottom: 5%">
                                    <table class="datatable table table-striped my-2" id="employee_table">
                                        <thead>
                                            <tr>
                                                @foreach($employeeFieldsIndexes as $key => $fields)
                                                <th scope="col">{{ucfirst($key)}}</th>
                                                @endforeach
                                                <th>Blank Fields</th>
                                                <th>Expiration Dates Count</th>
                                                <th>Documents</th>
                                                <th>Documents Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($empMainArr as $key => $fields)
                                                <tr class="employee-row" data-row-id="{{ $fields['ID'] }}" data-department="{{ $fields['department'] }}" data-division="{{ $fields['division'] }}" data-jobinfo="{{ $fields['jobTitle'] }}">
                                                
                                                    <td><a href="{{ route('employees.detail', $fields['ID']) }}">{{$fields['ID']}}</a>      </td>
                                                    <td><?php print_r($fields['photo']); ?></td>
                                                    <td>{{$fields['firstname']}}</td>
                                                    <td>{{$fields['lastname']}}</td>
                                                    <td>{{$fields['designation']}}</td>
                                                    <td>{{$fields['email']}}</td>
                                                    <td>{{$fields['department']}}</td>
                                                    <td>{{$fields['manager']}}</td>
                                                    <td>{{$fields['jobTitle']}}</td>
                                                    <td>{{$fields['division']}}</td>
                                                    <td id="row-{{ $fields['ID'] }}">
                                                        <span class="job-{{ $fields['ID'] }}"> <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                            </div></span>
                                                       
                                                    </td>
                                                    <td id="row-{{ $fields['ID'] }}">
                                                      
                                                        <span class="tracker-{{ $fields['ID'] }}"> <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                            </div></span>
                                                    </td>
                                                    <td id="row-{{ $fields['ID'] }}" class="text-center">
                                                          <a href="{{ route('employees.documents', ['id' => $fields['ID']]) }}" target="_blank">
                                                            <i class="bi bi-folder-fill" style="font-size:30px"></i>
                                                        </a>
                                                  </td>
                                                  <td id="row-{{ $fields['ID'] }}">
                                                  <span class="document-{{ $fields['ID'] }}"> <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                            </div></span>
                                                  </td>
                                                </tr>
                                            @endforeach
                                            <?php
                                            /*
                                            foreach ($empMainArr as $key => $fields) {
                                                echo '<tr>';
                                                foreach ($fields as $key => $val) {
                                                    echo '<td>';
                                                    print_r($fields[$key]);
                                                    echo '</td>';
                                                    
                                                }
                                                echo '<td><button class="btn btn-success" style="display: none;">View</button></td>';
                                                echo '</tr>';
                                            } */
                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" name="json_ids" id="empIdsAr" value="{{$empIdsAr}}">
                            </div>
                            <!-- End Table with stripped rows -->
                        <!-- </div> -->
                        <!-- <div class="tab-pane fade" id="profile-justified" role="tabpanel" aria-labelledby="profile-tab">
                            We will show blank data here
                        </div>
                        <div class="tab-pane fade" id="contact-justified" role="tabpanel" aria-labelledby="contact-tab">
                            Other Information
                        </div> -->
                    <!-- </div> -->
                </div>
                <!-- End Employee Tabs -->
                @endif
            </div><!-- End Right side columns -->

        </div>
</section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
$(document).ready(function() {
    var table = $('#employee_table').DataTable();

    // Event handler for the DataTables draw event
    table.on('draw.dt', function() {
        var searchValue = table.search(); // Get the current search term

        // Remove previous highlighting
        table.rows().nodes().to$().removeClass('highlight');

        // If search term is not empty
        if (searchValue !== '') {
            // Iterate through each cell in the table body
            table.cells().every(function() {
                var cellData = this.data();
                var rowNode = this.node();

                // Check if cell value matches the search term
                if (cellData.includes(searchValue)) {
                    var elementToFind = $(rowNode).find('.btn-success');
                    elementToFind.show();
                    $(this.node()).parent().addClass('highlight'); // Add highlight class to the row
                    return false; // Break out of the loop after finding the first match in each row
                }
            });
        }else if (searchValue === '' || table.rows({ search: 'applied' }).count() === 0) {
            // Hide the button
            $('.btn-success').hide();
        }
    

        
    });
    //Will show empty field counts here from different tabs
    function fetchRowData(rowId) {
            $.ajax({
                url: '/employee/row/' + rowId, // Route to get data for a single row
                method: 'GET',
                success: function(response){
                    // Handle the response, e.g., append data to a table row
                    $('.job-' + rowId).html(response); // Assuming there's a row with id "row-{rowId}" in your HTML
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });
        }

        function fetchTimeTrackerRowData(rowId, division, department, jobInfo) {
            $.ajax({
                url: '/employee/row/timetracker/' + rowId, 
                method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            division: division,
            department: department,
            jobInfo: jobInfo
        },
   
                success: function(response){
                    // Handle the response, e.g., append data to a table row
                    $('.tracker-' + rowId).html(response); // Assuming there's a row with id "row-{rowId}" in your HTML
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });
        }

        function fetchDoucumentCount(rowId, division, department, jobInfo) {
            $.ajax({
                url: '/doucument/row/count/' + rowId, 
                method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            division: division,
            department: department,
            jobInfo: jobInfo
        },
   
                success: function(response){
                    // Handle the response, e.g., append data to a table row
                    $('.document-' + rowId).html(response); // Assuming there's a row with id "row-{rowId}" in your HTML
                },
                error: function(xhr, status, error){
                    console.error(error);
                }
            });
        }

        function fetchDataForVisibleRows() {
                table.rows({page: 'current'}).nodes().each(function (node, index) {
                    var rowId = $(node).data('row-id');
                    var division = $(node).data('division');
                    var jobInfo = $(node).data('jobinfo');
                    var department = $(node).data('department');

                    fetchRowData(rowId);
                    fetchTimeTrackerRowData(rowId, division, department, jobInfo);
                    fetchDoucumentCount(rowId, division, department, jobInfo);

                });
            }

            // Fetch data for visible rows when the page changes
            table.on('draw', function () {
                fetchDataForVisibleRows();
            });

            // Initial fetch for the visible rows on page load
            fetchDataForVisibleRows();
});


</script>
<!--end::Page Custom Javascript-->
@endsection