@extends('layouts/user_master')
@section('title',$title)
@section('content')
@include('user/includes/breadcrumb')
@if(!empty($sliders))
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Slider Images</h4>
    </div>

    <div class="card-content">
        <div class="card-body">
            <div class="row">
                <table class="table table-hover table-bordered mb-0 small-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sliders as $slider)
                        <tr>
                            <td>
                                <img src="{{$slider['value']}}" class="img-fluid"  alt="Preview" style="width: 100px;height: auto;" />
                            </td>
                            <td>
                                <a class="remove-image" data-key="{{$slider['key']}}" href="javascript:;" style="display: inline;">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mt-2">
    <div class="card-body">
        <form id="recordForm" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">

            <div class="col-12 col-md-12">
                <label>Image<span class="danger">*</span></label>
                <div class="form-group">
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" />
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script type="text/javascript">
    var updateURL = "{{route('mobile_slider.store')}}", deleteSlider = "{{route('mobile_slider.delete')}}";
    $(document).ready(function(){
        $("#recordForm").validate({
            rules:{
                image:{required: true}
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function (form){
                myApp.showPleaseWait();

                $("#recordForm .btn-block").attr('disabled','disabled').html("Loading");
                var formData = new FormData($("form#recordForm")[0]);

                AjaxCall(updateURL,formData, "POST", "", false,true).done(function (response) {
                    $('html, body').animate({ scrollTop: 0}, 2000);
                    $("#recordForm .btn-block").removeAttr('disabled').html('Update Profile');
                    ShowNotify(response);
                    if(response.IsSuccess){
                        location.reload();
                    }
                });
            }
        });

        $("body").on('click','.remove-image',function(){
            const imageId = $(this).data('key');

            swalWithBootstrapButtons({
                title: 'Are you sure?',
                text: "Once deleted, you will not be able to recover this record!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'No, cancel',
            }).then((result) => {
                if (result.value) {
                    AjaxCall(deleteSlider,ko.toJSON({image:imageId}), "post", "json", "application/json",true).done(function (response) {
                        ShowNotify(response);
                        if(response.IsSuccess){
                            location.reload();
                        }
                    });
                }
            })
        })
    });
</script>
@endsection