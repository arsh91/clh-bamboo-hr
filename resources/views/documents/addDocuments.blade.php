@extends('layouts.app')
@section('title', 'Employee Document Folders')
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
  margin-right: 10px;
  background: #eeeeee70;
  padding: 5px;
  min-height: 1.5em;
  overflow-y: scroll;
  height: 400px;
}
.facet-list li {
  margin: 5px;
  padding: 5px;
}
.facet-list li.placeholder {
  height: 1.2em
}
.facet {
  border: 1px solid #ccc !important;
  background-color: #fafafa;
  cursor: move;
  border-radius:unset !important;
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
        <div class="col-lg-8">

        <div class="card">
          <div class="alert alert-warning" role="alert" id="sortingMessage" style="display:none;">
            You need to select the Department and Job Title first!
          </div>
            <div class="card-body ">
            <h5 class="card-title">Add/Edit Folder</h5>
        <form>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Department</label>
                  <div class="col-sm-10">
                    <select class="form-select department sortingCheck" aria-label="Default select example">
                      <option selected="">Open this select menu</option>
                      @foreach($departmentArray as $key => $value)
                          <option value="{{ $key }}">{{ $key }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Job Title</label>
                  <div class="col-sm-10 ">
                    <select class="form-select job sortingCheck" aria-label="Default select example">
                      <option selected="">Open this select menu</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  
                  <div class="col-lg-6">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">All Documents</h5>


                    <ul class="list-group facet-list" id="allFacets">
                    @foreach($employeeAllDocumentsArr as $key => $fields)
                  
                    <li class="list-group-item facet" data-value="{{$fields['docId']}}"> {{$fields['docName']}}</li>
                    @endforeach
                    </ul>

                  </div>
                </div>
                </div>
                <div class="col-lg-6">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Employee Documents</h5>
                    <span><i>Drop Documents Here</i></span>
                    <ul class="list-group facet-list" id="userFacets"  style="height:376px;">
                      
                    </ul>
                  </div>
                </div>
                </div>

                  <!-- <label class="col-sm-2 col-form-label">Submit Button</label> -->
                  <div class="col-sm-10">
                    <button type="button" class="btn btn-primary" id="btnGetArrays">Submit</button>
                  </div>
                </div>

              </form>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('custom_js')
<!--begin::Page Custom Javascript(used by this page)-->

<script>
  var options = @json($departmentArray);
  var employeeAllDocumentsArr = @json($employeeAllDocumentsArr);
$(document).ready(function() {
  var userFacetsList = document.getElementById('userFacets');
  $('.department').change(function() {
    userFacetsList.innerHTML = '';
    //$("#userFacets").append('<li class="list-group-item facet">drop Here</li>');
    var selectedValue = $(this).val();
    var valueForKey2 = options[selectedValue];
    $('.job').empty();
    // Add a default option
    $('.job').append($('<option>', {
        value: '',
        text: 'Open this select menu',
        selected: true
    }));

    // Add options from the options variable
    if (valueForKey2 && valueForKey2['job']) {
      $.each(valueForKey2['job'], function(key, value) {
          $('.job').append($('<option>', {
              value: value,
              text: value
          }));
      });
  }
    // You can perform further actions with the selected value here
});

function handleSortable()
{
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
}

//we will ask the user to select the values from options first only then we will enable documents
$("#allFacets, #userFacets" ).sortable({
  start: function( event, ui ) {
    $('#sortingMessage').show('1000');
    $("#sortingMessage").delay(2000).fadeOut(300);
  }
});




  $(".sortingCheck").change(function () {
      var department = $('.department').val();
      var job = $('.job').val();
      if (department != "" && job != "") {
          handleSortable(); 
      } else 
      {      
        if ($("#allFacets, #userFacets").sortable("instance")) {
        $("#allFacets, #userFacets").sortable("destroy");
      }
    }
  }); //##WILL CHECK IF BOTH SELECT BOX HAS SOME VALUE THEN ENABLE THE SORTINF

  $('.job').change(function() 
  {
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
          userFacetsList.innerHTML = '';
         // $("#userFacets").append('<li class="list-group-item facet">drop Here</li>');
          if(response.length > 0)
          {
            var folderData = response[0]['folder'];
            folderData.forEach(function(itemText) {
              var listItem = document.createElement('li');
              listItem.classList.add('list-group-item', 'facet');
              listItem.setAttribute('data-value', itemText.folder_id);
              $.each(employeeAllDocumentsArr, function(index, doc) {
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
  }); //##ON CHANGE OF JOB THAT AJAX WILL RUN HERE

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
            window.location.reload();
          },
          error: function(xhr, status, error){
              console.error(error);
          }
        });
        return false;
    });

 

</script>
<!--end::Page Custom Javascript-->
@endsection