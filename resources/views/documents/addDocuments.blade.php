@extends('layouts.app')
@section('title', 'Employee Detail')
@section('content')
<style>
  .facet-container{
  width: 330px;
}
.right {
  float: right;
}
.left {
  float: left;
}
p {
  clear: both;
  padding-top: 1em;
}
.facet-list {
  list-style-type: none;
  margin: 0;
  padding: 0;
  margin-right: 10px;
  background: #eee;
  padding: 5px;
  width: 143px;
  min-height: 1.5em;
  font-size: 0.85em;
}
.facet-list li {
  margin: 5px;
  padding: 5px;
  font-size: 1.2em;
  width: 120px;
}
.facet-list li.placeholder {
  height: 1.2em
}
.facet {
  border: 1px solid #bbb;
  background-color: #fafafa;
  cursor: move;
}
.facet.ui-sortable-helper {
  opacity: 0.5;
}
.placeholder {
  border: 1px solid orange;
  background-color: #fffffd;
}
  </style>

<section class="section profile">
      <div class="row">
        <div class="col-lg-6">

        <div class="card">
            <div class="card-body ">
            <h5 class="card-title">Add/Edit Folder</h5>
        <form>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Department</label>
                  <div class="col-sm-10">
                    <select class="form-select department" aria-label="Default select example">
                      <option selected="">Open this select menu</option>
                      <!-- <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option> -->
                      @foreach($departmentArray as $key => $value)
                          <option value="{{ $key }}">{{ $key }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Job Title</label>
                  <div class="col-sm-10 ">
                    <select class="form-select job" aria-label="Default select example">
                      <option selected="">Open this select menu</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                <div class="col-lg-6">

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Default List Group</h5>


    <ul class="list-group" id="allFacets">
    @foreach($employeeAllDocumentsArr as $key => $fields)
  
    <li class="list-group-item" data-value="{{$fields['docId']}}"> {{$fields['docName']}}</li>
                                                @endforeach
      <!-- <li class="list-group-item">An item</li>
      <li class="list-group-item">A second item</li>
      <li class="list-group-item">A third item</li>
      <li class="list-group-item">A fourth item</li>
      <li class="list-group-item">And a fifth one</li> -->
    </ul>

  </div>
</div>
</div>
<div class="col-lg-6">

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Default List Group</h5>
    <ul class="list-group" id="userFacets">
      <li class="list-group-item">drop Here</li>
    </ul>
  </div>
</div>
</div>

<!-- <div class="facet-container">
  <div class="left">
    <label>All Facets</label>
    <ul id="allFacets" class="facet-list">
      <li class="facet">Facet 2</li>
      <li class="facet">Facet 3</li>
      <li class="facet">Facet 5</li>
    </ul>
  </div>
  <div class="right">
    <label>User Facets</label>
    <ul id="userFacets" class="facet-list">
      <li class="facet">Facet 1</li>
      <li class="facet">Facet 4</li>
    </ul>
  </div>
</div>
<p>Drag & drop to rearrange items within a list or between lists.</br>Double-click to move item from one list to the bottom of the other.</p> -->
                  <label class="col-sm-2 col-form-label">Submit Button</label>
                  <div class="col-sm-10">
                    <button type="button" class="btn btn-primary" id="btnGetArrays">Submit Form</button>
                  </div>
                </div>

              </form>
            </div>
        </div>

        </div>

      <!---##Date tracker--->
    </section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
  var options = @json($departmentArray);
  var employeeAllDocumentsArr = @json($employeeAllDocumentsArr);
console.log(employeeAllDocumentsArr, 'ppppp')
$(document).ready(function() {

  $('.department').change(function() {
    var selectedValue = $(this).val();
    console.log('Selected value:', selectedValue);
    var valueForKey2 = options[selectedValue];
    console.log(valueForKey2); 

    $('.job').empty();

    // Add a default option
    $('.job').append($('<option>', {
        value: '',
        text: 'Open this select menu',
        selected: true
    }));

    // Add options from the options variable
    $.each(valueForKey2['job'], function(key, value) {
        $('.job').append($('<option>', {
            value: value,
            text: value
        }));
    });
    // You can perform further actions with the selected value here
});
$("#allFacets, #userFacets").sortable({
      connectWith: "ul",
      placeholder: "placeholder",
      delay: 150
    })
    .disableSelection()
    .dblclick( function(e){
      var item = e.target;
      if (e.currentTarget.id === 'allFacets') {
        //move from all to user
        $(item).fadeOut('fast', function() {
          $(item).appendTo($('#userFacets')).fadeIn('slow');
        });
      } else {
        //move from user to all
        $(item).fadeOut('fast', function() {
          $(item).appendTo($('#allFacets')).fadeIn('slow');
        });
      }
    });

      $('.job').change(function() {
        var job = $(this).val();
      var department = $('.department').val();
      $.ajax({
                url: '/get-saved-folder', 
                method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            job: job,
            department: department
        },

            success: function(response){
              var userFacetsList = document.getElementById('userFacets');
              userFacetsList.innerHTML = '';
              $("#userFacets").append('<li class="list-group-item">drop Here</li>');
              if(response.length > 0){
                console.error(response[0]['folder']);
                var folderData = response[0]['folder'];

            // Clear existing content
            
            folderData.forEach(function(itemText) {
                var listItem = document.createElement('li');
                listItem.classList.add('list-group-item');
                listItem.setAttribute('data-value', itemText.folder_id);
                    $.each(employeeAllDocumentsArr, function(index, doc) {
                    // Check if the docId matches
                    if (doc.docId === itemText['folder_id'].toString()) {
                        listItem.textContent = doc.docName;
                    }
                });
                userFacetsList.appendChild(listItem);
                });
              }
                
            },
            error: function(xhr, status, error){
                console.error(error);
            }
        });
  });
});

$("#btnGetArrays").click(function() {
     var job = $('.job').val();
      var department = $('.department').val();
        var allFacetsArray = $("#allFacets li").map(function() {
          return $(this).data("value");
        }).get();
        var userFacetsArray = $("#userFacets li").map(function() {
          return $(this).data("value");
        }).get();

        $.ajax({
                url: '/save-folder', 
                method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            job: job,
            department: department,
            folder: userFacetsArray
        },

            success: function(response){
            },
            error: function(xhr, status, error){
                console.error(error);
            }
        });
        console.log("All Facets:", allFacetsArray);
        console.log("User Facets:", userFacetsArray);
    });

 

</script>
<!--end::Page Custom Javascript-->
@endsection