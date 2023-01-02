@extends('layouts/user_master')
@section('title',$title)
@section('content')
@include('user/includes/breadcrumb')
<div class="card">
    <div class="card-header header-elements">
        <h4 class="me-2">{{$title}}</h4>
    </div>

    <div class="card-body">
        <form class="row justify-content-end" data-bind="with:$root.SearchModel">
            <div class="col-md-3">
                <fieldset class="form-group">
                    <select class="form-control" data-bind="options:$root.users, optionsText:'name', optionsValue:'id', value:$data.user_id, optionsCaption:'Select User', select2:{ placeholder: 'Select User'}"></select>
                </fieldset>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary glow" data-bind="click:$root.ApplyFilter">@lang('messages.search_txt')</button>
                <button class="btn btn-warning glow" data-bind="click:$root.ClearSearch">@lang('messages.clear_txt')</button>
            </div>

            <div class="col-md-3 text-end custom-header" data-bind="with:$root.pager">
                <label>@lang('messages.show_txt') 
                    <select class="show-record"  data-bind="options:$data.pageSizeOptions(),value:$data.selectedPageSize"></select>
                    @lang('messages.entries_txt') 
                </label>
            </div>
        </form>

        <div class="table-responsive text-nowrap mt-3">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Phone Number</th>
                        <th>Total Weight</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length > 0, foreach:$root.Records">
                    <tr>
                        <td data-bind="text:$data.Index"></td>
                        <td data-bind="text:$data.user.name"></td>
                        <td data-bind="text:$data.user.phone_number"></td>
                        <td data-bind="text:$data.total_weight"></td>
                        <td data-bind="text:$data.created_at"></td>
                        <td>

                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item" data-bind="click:$root.showDetailHandler">
                                            View Details
                                        </button>
                                    </li>
                                    <!-- ko if:$data.status() == 1 -->
                                    <li>
                                        <button class="dropdown-item" data-bind="click:$root.changeStatus.bind($data,2)">
                                            Approved
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-bind="click:$root.changeStatus.bind($data,3)">
                                            Rejected
                                        </button>
                                    </li>
                                    <!-- /ko -->

                                    <!-- ko if:$data.status() == 2 -->
                                    <li>
                                        <a class="dropdown-item" data-bind="attr:{href:$data.invoiceUrl}">
                                            Generate Pdf
                                        </a>
                                    </li>

                                    <li data-bind="visible:$data.viewInvoice">
                                        <a class="dropdown-item" data-bind="attr:{href:$data.viewInvoice}" target="_blank">
                                            View PDF
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" data-bind="attr:{href:$data.invoiceShortUrl}">
                                            Generate Short Pdf
                                        </a>
                                    </li>

                                    <li data-bind="visible:$data.viewShortInvoice">
                                        <a class="dropdown-item" data-bind="attr:{href:$data.viewShortInvoice}" target="_blank">
                                            View Short PDF
                                        </a>
                                    </li>
                                    <!-- /ko -->
                                </ul>
                            </div>

                        </td>
                    </tr>
                </tbody>
                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length == 0">
                    <tr>
                        <td colspan="11" align="middle">@lang('messages.no_record_found_txt')</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-3" data-bind="with:pager, visible: $data.Records().length > 0">
            <div class="col-sm-12 col-md-5">
                <span class="showing-text">
                    @lang('messages.showing_txt') <!-- ko text:FirstItemIndex() --><!-- /ko --> @lang('messages.to_txt') <!-- ko text:LastItemIndex() --><!-- /ko --> @lang('messages.of_txt') <!-- ko text:iTotalRecords() --><!-- /ko --> @lang('messages.entries_txt')
                </span>
            </div>

            <div class="col-sm-12 col-md-7">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item prev" data-bind="css:{'disabled':currentPage()== 1}">
                        <a class="page-link" data-bind="click: firstPage">
                            <i class="bx bx-chevrons-left"></i>
                        </a>
                    </li>
                     <li class="page-item prev" data-bind="css:{'disabled':currentPage()== 1}">
                        <a class="page-link" data-bind="click: previousPage">
                            <i class="bx bx-chevron-left"></i>
                        </a>
                    </li>
                    <!-- ko foreach: $data.pagesToShow() -->
                    <li class="page-item" data-bind="css: { active: $data.pageNumber == $parent.currentPage() }">
                        <a class="page-link" data-bind="attr: {title:$data.pageNumber},text: $data.pageNumber, click: $parent.gotoPage,attr:{disabled:$data.pageNumber === $parent.currentPage()}"></a>
                    </li>
                    <!-- /ko -->

                    <li class="page-item next" data-bind="css:{'disabled':currentPage() == allPages().length}">
                        <a class="page-link" data-bind="click: nextPage">
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    </li>
                    <li class="page-item next" data-bind="css:{'disabled':currentPage() == allPages().length}">
                        <a class="page-link" data-bind="click: lastPage">
                            <i class="bx bx-chevrons-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('popup')
<div class="modal fade" id="viewDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Detail Total Weight: <span data-bind="text:$root.totalWeight"></span></h5>
                <button type="button" class="btn-close" class="close" data-bind="click:$root.closeModal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Weight</th>
                            <th>Quantity</th>
                            <th>Total Weight</th>
                        </tr>
                    </thead>

                    <tbody  class="table-border-bottom-0" data-bind="visible:$root.details().length > 0, foreach:$root.details">
                        <tr>
                            <td data-bind="text:$index() + 1"></td>
                            <td>
                                <img class="img-fluid" data-bind="attr:{src:$data.image}" style="width:100px;height:auto" />
                            </td>
                            <td data-bind="text:$data.code"></td>
                            <td data-bind="text:$data.size_name"></td>
                            <td data-bind="text:$data.weight"></td>
                            <td data-bind="text:$data.quantity"></td>
                            <td data-bind="text:$data.quantity() * $data.weight()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bind="click:$root.closeModal">Close</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('public/vendor/select2/js/select2.min.js')}}"></script>
<script type="text/javascript">
    var getRecords = "{{$records}}", changeStatus="{{$changeStatus}}", users=@json($users);
</script>
<script src="{{asset('public/js/pagejs/orders.js?'.time())}}"></script>
@endsection