@extends('layouts.app')
@section('title', 'Catalog')
@section('sub-title', 'Show Catalog')
@section('content')
<section class="section catalog">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Catalog Title:</label>
                        <div class="col-sm-9">
                            {{$catalog->title ?? 'NA'}}
                        </div>
                    </div>

                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">SKU:</label>
                        <div class="col-sm-9">
                            {{$catalog->sku ?? 'NA' }}
                        </div>
                    </div>

                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Content:</label>
                        <div class="col-sm-9">
                            {{$catalog->content ?? 'NA' }}
                        </div>
                    </div>

                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Base Price:</label>
                        <div class="col-sm-9">
                            {{$catalog->base_price }}
                        </div>
                    </div>
                    @if($catalog->status == 'publish')
                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Publish Date:</label>
                        <div class="col-sm-9">
                            {{$catalog->publish_date ?? 'NA'}}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Status:</label>
                        <div class="col-sm-9">
                            @if($catalog->status == 'draft')
                            <span class="badge rounded-pill bg-warning">Draft</span>
                            @else
                            <span class="badge rounded-pill  bg-success">Publish</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-1 mt-4">
                        <label for="" class="col-sm-3">Image:</label>
                        <div class="col-sm-9">
                            @if ($catalog->image == null)
                            No Uploaded Image Found
                            @else
                            <button type="button" class="btn btn-outline-primary btn-sm mb-1">
                                @php
                                $extension = pathinfo($catalog->image, PATHINFO_EXTENSION);
                                $iconClass = '';

                                switch ($extension) {
                                case 'pdf':
                                $iconClass = 'bi-file-earmark-pdf';
                                break;
                                case 'doc':
                                case 'docx':
                                $iconClass = 'bi-file-earmark-word';
                                break;
                                case 'xls':
                                case 'xlsx':
                                $iconClass = 'bi-file-earmark-excel';
                                break;
                                case 'jpg':
                                case 'jpeg':
                                case 'png':
                                $iconClass = 'bi-file-earmark-image';
                                break;
                                // Add more cases for other file extensions as needed
                                default:
                                $iconClass = 'bi-file-earmark';
                                break;
                                }
                                @endphp
                                <i class="bi {{ $iconClass }} mr-1" onclick="window.open('{{ asset('storage').'/'.$catalog->image }}', '_blank')"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-title">
                <h4>Related History</h4>
            </div>
            <div class="card">

                <div class="card-body">
                    <!-- Table with stripped rows -->
                    <div class="box-header with-border my-4" id="filter-box">
                        <div class="box-body table-responsive" style="margin-bottom: 5%">
                            <table class="datatable table table-striped my-2" id="product_table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <!-- <th scope="col">Name</th> -->
                                        <th scope="col">Name</th>
                                        <th scope="col">Slug</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Regular Price</th>
                                        <th scope="col">Sale Price</th>
                                        <th scope="col">Publish Date</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($products as $index => $product)
                                    <tr>
                                        <th scope="row">{{ $product['id'] }}</th>

                                        <td>{{ucfirst($product['name']) ?? 'NA'}}</td>
                                        <td>{{ isset($product['sku']) && $product['sku'] ? $product['sku'] : 'NA' }}</td>
                                        <td>{{$product['price'] ?? 'NA'}}</td>
                                        <td>{{$product['regular_price'] ?? 'NA'}}</td>
                                        <td>{{$product['sale_price'] ?? 'NA'}}</td>
                                        <td>{{isset($product['publish_date']) && $product['publish_date'] ? $product['publish_date'] : 'NA'}}</td>
                                        <td>{{isset($product['stock']) && $product['stock'] ? $product['stock'] : 'NA'}}</td>
                                        <td>


                                            @if (isset($product['images']) && isset($product['images']['thumbnail']))
                                            @if (Str::startsWith($product['images']['thumbnail'], ['http://', 'https://']))
                                            <!-- External link -->
                                            <a href="{{ $product['images']['thumbnail'] }}" target="_blank">
                                                <img src="{{ $product['images']['thumbnail'] }}" height="40" width="70" alt="Catalog Image">
                                            </a>
                                            @else
                                            <i class="bi bi-file-earmark-image mr-1"></i>

                                            @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($product['status'] == 'publish')
                                            <span class="badge rounded-pill bg-success">{{ucfirst($product['status'])}}</span>
                                            @else
                                            <span class="badge rounded-pill  bg-warning">{{ucfirst($product['status'])}}</span>
                                            @endif
                                        </td>
                                        <td>
                                        
                                            <a href="javascript:void(0)" onClick="openGalleryModal('{{ isset($product['gallery_images']) ? addslashes(json_encode($product['gallery_images'])) : '' }}')" class="btn btn-default-border">Gallery</a>




                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                    <!-- End Table with stripped rows -->

                </div>
            </div>

            <!-- <div class="card-title">
                <h4>Reports</h4>
            </div> -->
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <!-- Reports -->
                        <div class="col-12">

                            <div class="card-body">
                                <h5 class="card-title">Reports</h5>
                                <!-- Line Chart -->
                                <div id="reportsChart"></div>
                                <!-- End Line Chart -->
                            </div>
                        </div>
                        <!-- End Reports -->
                    </div>
                </div>
            </div>

            <!--start: Add users Modal -->
            <div class="modal fade" id="showGallery" tabindex="-1" aria-labelledby="role" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="role">Show Gallary</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="galleryContainer"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-default">Save</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--end: Add User Modal -->
    </div>
    </div>
</section>
@endsection

@section('custom_js')
<script>
    $(document).ready(function() {

        $('#product_table').DataTable({
            "order": []

        });
    });

    function openGalleryModal(galleryData) {
        // console.log(galleryData);
         // Convert the JSON string to an array
         var images = JSON.parse(galleryData);
        
        // Get the gallery container
        var galleryContainer = document.getElementById("galleryContainer");

        // Clear previous content
        galleryContainer.innerHTML = "";

        // Check if images array is null or empty
        if (images === null || images.length === 0) {
            var noImageText = document.createElement("p");
            noImageText.textContent = "No Image";
            galleryContainer.appendChild(noImageText);
        } else {
            // Display "Loading..." text while images are being loaded
            var loadingText = document.createElement("p");
            loadingText.textContent = "Loading...";
            galleryContainer.appendChild(loadingText);

            // Loop through each image URL and create img elements
            images.forEach(function(imageUrl) {
                // Create img element
                var img = document.createElement("img");

                // Set src attribute
                img.src = imageUrl;

                // Set alt attribute
                img.alt = "Gallery Image";

                // Add styling if needed
                img.style.maxWidth = "100px"; // Example styling

                // Append img element to the gallery container
                galleryContainer.appendChild(img);
            });

            // Hide the "Loading..." text after 2 seconds
            setTimeout(function() {
                galleryContainer.removeChild(loadingText);
            }, 2000);
            
        }
        // $('.alert-danger').html('');
        // $('#first_name').val('');
        $('#showGallery').modal('show');
    }

    document.addEventListener("DOMContentLoaded", () => {
        const data = @json($data);
        // console.log(data);
        new ApexCharts(document.querySelector("#reportsChart"), {
            series: [{
                name: 'Revenue',
                data: [11, 32, 45, 32, 34, 52, 41]
            }, {
                name: 'Customers',
                data: [15, 11, 32, 18, 9, 24, 11]
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                },
            },
            markers: {
                size: 4
            },
            colors: ['#4154f1', '#2eca6a', '#ff771d'],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.4,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            }
        }).render();
    });
</script>
@endsection