@extends('layouts/user_master')
@section('title',$title)
@section('content')
@include('user/includes/breadcrumb')
<div class="card">
    <div class="card-header header-elements">
        <h4 class="me-2">{{$title}}</h4>

        <div class="card-header-elements ms-auto">
            <button type="button" class="btn btn-primary" data-bind="click:$root.addRecord"><span class="tf-icon bx bx-plus bx-xs"></span> Add</button>
        </div>
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
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length > 0, foreach:$root.Records">
                    <tr>
                        <td data-bind="text:$data.Index"></td>
                        <td data-bind="text:$data.name"></td>
                        <td data-bind="text:$data.category_name"></td>
                        <td data-bind="text:$data.sub_category_name"></td>
                        <td>
                            <a data-bind="attr:{href:$data.image}" target="_blank">
                                <img data-bind="attr:{src:$data.image}" style="width:100px;height: 100px;object-fit:contain;">
                            </a>
                        </td>
                        <td>
                            <label class="form-switch">
                                <input type="checkbox" data-bind="checked: $data.status,attr:{name:$data.id()+'status',id:$data.id()},click:$root.stateChange">
                                <i></i>
                            </label>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm icon" data-bind="click:$root.updateRecord"><i class="bx bxs-edit"></i></button>
                                <button class="btn btn-sm icon" data-bind="click:$root.deleteRecord"><i class="bx bxs-trash"></i></button>
                            </div>
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

@section('popup')
<div class="modal fade" id="addRecord" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" data-bind="with:$root.recordModel" id="add_record">
            <input type="hidden" name="id" data-bind="value:$data.id">
            <div class="modal-header">
              <h5 class="modal-title" data-bind="text:$data.id() > 0 ? 'Update' : 'Add'">Modal title</h5>
              <button type="button" class="btn-close" class="close" data-bind="click:$root.closeModal"></button>
            </div>

            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-12 mb-3">
                        <label>Category</label>
                        <div class="form-group">
                            <select class="form-select" name="lu_category_id" data-bind="options:$root.categories, optionsText:'name', optionsValue:'id', value:$data.lu_category_id, optionsCaption:'Select Category',decorateErrorElement:$data.category_id"></select>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Sub Category</label>
                        <div class="form-group">
                            <select class="form-select" name="lu_sub_category_id" data-bind="options:$root.subCategories, optionsText:'name', optionsValue:'id', value:$data.lu_sub_category_id, optionsCaption:'Select Category',decorateErrorElement:$data.lu_sub_category_id"></select>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label>@lang('messages.name_txt')</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" data-bind="value:$data.name" placeholder="@lang('messages.enter_name_placeholder_txt')" autocomplete="off" />
                        </div>
                    </div>

                    <div class="col-12">
                        <label>Image</label>
                        <div class="form-group">
                            <input type="file" class="form-control" name="file" data-bind="value:$data.file" placeholder="@lang('messages.enter_name_placeholder_txt')" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bind="click:$root.closeModal">@lang('messages.cancel_txt')</button>
                <button type="submit" class="btn btn-primary" data-bind="click:$root.saveRecord,text:$data.id() > 0 ? '@lang('messages.update_txt')' : '@lang('messages.add_txt')'"></button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    var getRecords = "{{$records}}", storeRecord="{{$storeRecord}}", destoryRecord="{{$destoryRecord}}", changeStatus="{{$changeStatus}}", categories=@json($categories);
</script>
<script src="{{asset('public/js/pagejs/types/sub-sub-image-type.js?'.time())}}"></script>
@endsection