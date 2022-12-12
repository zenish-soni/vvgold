@extends('layouts/user_master')
@section('title',$title)
@section('content')
@include('user/includes/breadcrumb')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{$title}}</h4>
    </div>

    <div class="card-content">
        <div class="card-body">
            <form class="form form-vertical" action="javascript:;" id="recordForm">
                <div class="form-body">
                    <div class="row">

                        <div class="col-12 col-md-12 mb-3">
                            <label>Bank Name<span class="danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{$setting->bank_name}}" />
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label>Bank Account Name<span class="danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" value="{{$setting->bank_account_name}}" />
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label>Bank Account Number<span class="danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{$setting->bank_account_number}}" />
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label>Branch Name<span class="danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="branch_name" name="branch_name" value="{{$setting->branch_name}}" />
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label>IFSC Code<span class="danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="bank_account_ifsc_number" name="bank_account_ifsc_number" value="{{$setting->bank_account_ifsc_number}}" />
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label>Bank Image</label>
                            <div class="form-group">
                                <input type="file" class="form-control" id="bank_photo" name="bank_photo" accept="image/*" />
                            </div>
                        </div>

                        @if(!empty($setting->bank_photo))
                        <div class="col-12 col-md-12 mb-3">
                            <label>View Bank Image</label>
                            <div class="form-group">
                                <img src="{{\App\Infrastructure\AppConstant::getImage($setting->bank_photo)}}" style="width:200px;height:auto" />
                            </div>
                        </div>
                        @endif

                        <div class="col-12 d-flex">
                            <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script type="text/javascript">
    var updateURL = "{{route('update_setting')}}";
    $(document).ready(function(){
        $("#recordForm").validate({
            rules:{
                bank_name:{required: true},
                bank_account_name:{required: true},
                bank_account_number:{required: true,number:6},
                branch_name:{required: true},
                bank_account_ifsc_number:{required: true},
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function (form){
                myApp.showPleaseWait();

                $("#recordForm .btn-block").attr('disabled','disabled').html("Loading");
                var formData = new FormData($("form#recordForm")[0]);

                AjaxCall(updateURL,formData, "POST", "", false,true).done(function (response) {
                    ShowNotify(response);
                    $('html, body').animate({ scrollTop: 0}, 2000);
                    $("#recordForm .btn-block").removeAttr('disabled').html('Update Profile');

                    if(response.IsSuccess){
                        location.reload();
                    }
                });
            }
        })
    });
</script>
@endsection