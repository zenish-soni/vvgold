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
                    <input type="text" class="form-control" placeholder="@lang('messages.list_search_placeholder_txt')" data-bind="value:$data.name" />
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
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Company Name</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Is Approved ?</th>
                        <th>Business Card</th>
                        <th>Business Card 2</th>
                    </tr>
                </thead>

                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length > 0, foreach:$root.Records">
                    <tr>
                        <td data-bind="text:$data.Index"></td>
                        <td data-bind="text:$data.name"></td>
                        <td data-bind="text:$data.phone_number"></td>
                        <td data-bind="text:$data.company_name"></td>
                        <td data-bind="text:$data.state"></td>
                        <td data-bind="text:$data.city"></td>
                        <td data-bind="text:$data.address"></td>
                        <td>
                            <label class="form-switch">
                                <input type="checkbox" data-bind="checked: $data.approved_status,attr:{name:$data.id()+'is_approved',id:$data.id()},click:$root.approveChange">
                                <i></i>
                            </label>
                        </td>
                        <td>
                            <!-- ko if:$data.business_photo --> 
                            <a data-bind="attr:{href:$data.business_photo}" target="_blank">
                                <img class="img-fluid" data-bind="attr:{src:$data.business_photo}" style="width:100px;height:100px;object-fit:contain;" />
                            </a>
                            <!-- /ko -->
                        </td>
                        <td>
                            <!-- ko if:$data.second_business_photo --> 
                            <a data-bind="attr:{href:$data.second_business_photo}" target="_blank">
                                <img class="img-fluid" data-bind="attr:{src:$data.second_business_photo}" style="width:100px;height:100px;object-fit:contain;" />
                            </a>
                            <!-- /ko -->
                        </td>
                    </tr>
                </tbody>
                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length == 0">
                    <tr>
                        <td colspan="10" align="middle">@lang('messages.no_record_found_txt')</td>
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

@section('script')
<script type="text/javascript">
    var getRecords = "{{$records}}", changeStatus="{{$changeStatus}}", changeApproved="{{$changeApproved}}";
</script>
<script src="{{asset('public/js/pagejs/users/user.js?'.time())}}"></script>
@endsection