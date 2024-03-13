@extends('layouts.app')
@section('title', 'Catalog')
@section('content')
<section class="section catalog">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary my-3" onClick="openusersModal()" href="javascript:void(0)">Add Catalog</button>
                    <!-- <h5 class="card-title">Table with stripped rows</h5> -->
                    <br>

                    <!-- Table with stripped rows -->
                    <div class="box-header with-border" id="filter-box">
                        <div class="box-body table-responsive" style="margin-bottom: 5%">
                            <table class="datatable table table-striped my-2" id="users_table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Base Price</th>
                                        <th scope="col">SKU</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($catalogs as $index => $data)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ucfirst($data->name) ?? ''}} </td>
                                        <td>{{$data->title ?? ''}}</td>
                                        <td>{{$data->base_price ?? ''}}</td>
                                        <td>{{$data->sku ?? ''}}</td>
                                        <td>
                                            @if ($data->image)
                                            <img src="{{ asset('storage/' . $data->image) }}" height="40" width="70" alt="Catalog Image">
                                            @else
                                                ---
                                            @endif 
                                        </td>
                                        <td>
                                             @if($data->status == 'draft')
                                            <span class="badge rounded-pill bg-warning">{{$data->status}}</span>
                                            @else
                                            <span class="badge rounded-pill  bg-success">{{$data->status}}</span>
                                            @endif
                                        </td>
                                        <td> 
                                            <i onClick="editCatalogs('{{ $data->id }}')" href="javascript:void(0)" class="fa fa-edit fa-fw pointer btn-fa-catalog"></i>

                                            <i onClick="deleteCatalogs('{{ $data->id }}')" href="javascript:void(0)" class="fa fa-trash fa-fw pointer btn-fa-catalog"></i></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Table with stripped rows -->


                    <!--start: Add users Modal -->
                    <div class="modal fade" id="addCatalog" tabindex="-1" aria-labelledby="role" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="role">Add Catalog</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="addCatalogsForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="alert alert-danger" style="display:none"></div>
                                        <div class="row mb-3 mt-4">
                                            <label for="title" class="col-sm-3 col-form-label required">Title</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title" id="title">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            
                                            <label for="content" class="col-sm-3 col-form-label required">Content</label>
                                            <div class="col-sm-9">
                                            <textarea class="form-control" name="content" style="height: 100px" id="content"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="name" class="col-sm-3 col-form-label required">Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="name" id="name">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="sku" class="col-sm-3 col-form-label required">SKU</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="sku" id="sku">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="base_price" class="col-sm-3 col-form-label required">Base Price</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" name="base_price" id="base_price" step=".01">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="image" class="col-sm-3 col-form-label required">Image</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="image" id="image">
                                            </div>
                                        </div>
                                         <!-- <div class="row mb-3">
                                            <label for="date" class="col-sm-3 col-form-label required">Date</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="date" id="date">
                                            </div>
                                        </div> -->
                                        <div class="row mb-3">
                                            <label for="status" class="col-sm-3 col-form-label ">Status</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-select" id="status">
                                                    <option value="draft">Draft</option>
                                                    <option value="publish">Publish</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Add User Modal -->



                 <!--start: Edit users Modal -->
                 <div class="modal fade" id="editCatalogs" tabindex="-1" aria-labelledby="role" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="role">Edit Catalog</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editCatalogsForm">
                                    @csrf
                                    <div class=" modal-body">
                                    <div class="alert alert-danger" style="display:none"></div>
                                        <div class="row mb-3 mt-4">
                                            <label for="edit_title" class="col-sm-3 col-form-label required">Title</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title" id="edit_title">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            
                                            <label for="edit_content" class="col-sm-3 col-form-label required">Content</label>
                                            <div class="col-sm-9">
                                            <textarea class="form-control" name="content" style="height: 100px" id="edit_content"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="edit_name" class="col-sm-3 col-form-label required">Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="name" id="edit_name">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="edit_sku" class="col-sm-3 col-form-label required">SKU</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="sku" id="edit_sku">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="edit_base_price" class="col-sm-3 col-form-label required">Base Price</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" name="base_price" id="edit_base_price" step=".01">
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-4">
                                            <label for="edit_image" class="col-sm-3 col-form-label required">Image</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="image" id="edit_image">
                                            </div>
                                        </div>
                                         <!-- <div class="row mb-3">
                                            <label for="date" class="col-sm-3 col-form-label required">Date</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="date" id="date">
                                            </div>
                                        </div> -->
                                        <div class="row mb-3">
                                            <label for="edit_status" class="col-sm-3 col-form-label ">Status</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-select" id="edit_status">
                                                    <option value="draft">Draft</option>
                                                    <option value="publish">Publish</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" name="users_id" id="catalog_id" value="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Edit User Modal -->

            </div>
        </div>
    </div>
    </div>
</section>

@endsection
@section('custom_js')
<script>
    $(document).ready(function() {

        $('#users_table').DataTable({
            "order": []

        });
    });


    function openusersModal() {
            $('.alert-danger').html('');
            $('#first_name').val('');
            $('#addCatalog').modal('show');
    }

    $('#addCatalogsForm').submit(function(event) {
        var imageFile = $('#image')[0].files[0];

        event.preventDefault();
        var formData = new FormData(this);
        formData.append('image',imageFile);
        console.log(formData)
        $.ajax({
            type: 'POST',
            url: "{{ url('/catalogs/add')}}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: (data) => {
                if (data.errors) {
                    $('.alert-danger').html('');
                    $.each(data.errors, function(key, value) {
                        $('.alert-danger').show();
                        $('.alert-danger').append('<li>' + value + '</li>');
                    })
                } else {
                    $('.alert-danger').html('');
                    $("#addCatalog").modal('hide');
                    location.reload();
                }
            },
            error: function(data) {}
        });

    });


    function editCatalogs(id) {
        $('.alert-danger').html('');
        $('#catalog_id').val(id);
        $.ajax({
            type: "GET",
            url: "{{ url('/catalogs/edit') }}" + '/' + id, 
            dataType: 'json',
            success: (res) => {
                if (res.catalogs != null) {
                    $('#editCatalogs').modal('show');
                    $('#edit_name').val(res.catalogs.name);
                    $('#edit_title').val(res.catalogs.title);
                    $('#edit_content').val(res.catalogs.content);
                    $('#edit_sku').val(res.catalogs.sku); 
                    $('#edit_base_price').val(res.catalogs.base_price);  
                    $('#edit_status option[value="' + res.catalogs.status + '"]').attr('selected',
                    'selected');
                }
            }
        });
    }


    $('#editCatalogsForm').submit(function(event) {
        id =   $('#catalog_id').val();
        event.preventDefault();
        var imageFile = $('#edit_image')[0].files[0];
        var formData = new FormData(this);
        formData.append('image',imageFile);
        $.ajax({
            type: "POST",
            url: `{{ route('catalogs.update', ['catalog' => ':id']) }}`.replace(':id', id),
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.errors) {
                    $('.alert-danger').html('');
                    $.each(res.errors, function(key, value) {
                        $('.alert-danger').show();
                        $('.alert-danger').append('<li>' + value + '</li>');
                    })
                } else {
                    $('.alert-danger').html('');
                    $("#editCatalogs").modal('hide');
                    location.reload();
                }
            }
        });
    });

    function deleteCatalogs(id) {
        console.log(id);
        if (confirm("Are you sure You Want To Delete Catalog ?")) {
            var token = $('meta[name="csrf-token"]').attr('content'); // Retrieve CSRF token from meta tag

            $.ajax({
                type: "DELETE",
                url: `{{ route('catalogs.destroy', ['catalog' => ':id']) }}`.replace(':id', id),
                data: {
                    _token: token, // Include CSRF token in the request data
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle errors
                }
            });
        }
    }
    
</script>
@endsection