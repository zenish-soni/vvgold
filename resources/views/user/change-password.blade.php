@extends('layouts/user_master')
@section('title',$title)
@section('content')
@include('user/includes/breadcrumb')
<div class="card">
    <div class="card-body">
        <form id="personalInfo" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">

            <div class="row mb-3">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Old Password</label>
                <div class="col-lg-8 fv-row fv-plugins-icon-container">
                    <input type="password" id="pw_old" class="form-control" name="pw_old" placeholder="Enter Old Password" />
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">New Password</label>
                <div class="col-lg-8 fv-row fv-plugins-icon-container">
                    <input type="password" id="pw_new" class="form-control" name="pw_new" placeholder="New Password" />
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Confirm New Password</label>
                <div class="col-lg-8 fv-row fv-plugins-icon-container">
                    <input type="password" id="pw_newtwo" class="form-control" name="pw_newtwo" placeholder="Confirm New Password" />
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script type="text/javascript">
    var updatePassword = "{{route('profile.update_password')}}";
    $(document).ready(function(){
        $("#personalInfo").validate({
            rules:{
                pw_old:{required: true,minlength:6},
                pw_new:{required: true,minlength:6},
                pw_newtwo:{equalTo : "#pw_new"}
            },
            messages:{
              pw_newtwo:{
                equalTo:'Password not match'
              }  
            }, 
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function (form){
                $("#personalInfo .btn-block").attr('disabled','disabled').html("Loading");
                var formData = $("form#personalInfo").serialize();
                $.ajax({
                    type: "POST",
                    url: updatePassword,
                    headers:
                    {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    statusCode:{
                        419:function(){
                            window.location.href = window.location.href;
                        }
                    },
                    success: function(response) {
                        $('html, body').animate({ scrollTop: 0}, 2000);
                        $("#personalInfo .btn-block").removeAttr('disabled').html('Update Profile');
                        ShowNotify(response);
                        if(response.IsSuccess){
                            location.reload();
                        }
                    }
                });
            }
        })
    });
</script>
@endsection