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
        <form class="row justify-content-start" method="post" id="formSearch" data-bind="with:$root.SearchModel" action="{{route('invoices.generate_catalogue')}}">
            {{csrf_field()}}
            <div class="col-md-2">
                <label>Category</label>
                <fieldset class="form-group">
                    <select class="form-control" name="lu_category_id" data-bind="options:$root.categories, optionsText:'name', optionsValue:'id', value:$data.lu_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Category'}"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Sub Category</label>
                <fieldset class="form-group">
                    <select class="form-control" name="lu_sub_category_id" data-bind="options:$root.subCategories, optionsText:'name', optionsValue:'id', value:$data.lu_sub_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Sub Category'}"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Sub Sub Category</label>
                <fieldset class="form-group">
                    <select class="form-control" name="sub_sub_category_id" data-bind="options:$root.subSubCategories, optionsText:'name', optionsValue:'id', value:$data.sub_sub_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Sub Sub Category'}"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Size</label>
                <fieldset class="form-group">
                    <select class="form-control" name="lu_size_id" data-bind="options:$root.sizes, optionsText:'name', optionsValue:'id', value:$data.lu_size_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Size'}"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Search Term</label>
                <fieldset class="form-group">
                    <select class="form-control" name="search_term_id" data-bind="options:$root.searchTerms, optionsText:'name', optionsValue:'id', value:$data.search_term_id, optionsCaption:'Select Term', select2:{ placeholder: 'Select Term'}"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Is Popular?</label>
                <fieldset class="form-group">
                    <select class="form-control" name="is_popular" data-bind="options:$root.popularArr, optionsText:'name', optionsValue:'id', value:$data.is_popular, optionsCaption:'Select Option'"></select>
                </fieldset>
            </div>

            <div class="col-md-2">
                <label>Search</label>
                <fieldset class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="@lang('messages.list_search_placeholder_txt')" data-bind="value:$data.name" />
                </fieldset>
            </div>

            <div class="col-md-12 mt-4">
                <button class="btn btn-primary glow" data-bind="click:$root.ApplyFilter">@lang('messages.search_txt')</button>
                <button class="btn btn-warning glow" data-bind="click:$root.ClearSearch">@lang('messages.clear_txt')</button>
                <button class="btn btn-danger glow" data-bind="click:$root.exportData">Generate</button>
                
                @if(!empty($configData['cataloguePath']))
                <a class="btn btn-success glow" href="{{$configData['cataloguePath']}}" target="_blank">View Catalogue</a>
                @endif

                <button class="btn btn-danger glow" data-bind="click:$root.exportSingleData">Single Generate</button>
                @if(!empty($configData['singleCataloguePath']))
                <a class="btn btn-success glow" href="{{$configData['singleCataloguePath']}}" target="_blank">View Image</a>
                @endif

                <div class="btn-group me-3" data-bind="visible:$root.selectedProductIDs().length > 0">
                    <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="javascript:;" data-bind="click:$root.popularEventHandler">Add Popular</a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="javascript:;" data-bind="click:$root.removePopularEventHandler">Remove Popular</a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>

        <div class="row justify-content-end">
            <div class="col-md-3 text-end custom-header" data-bind="with:$root.pager">
                <label>@lang('messages.show_txt') 
                    <select class="show-record"  data-bind="options:$data.pageSizeOptions(),value:$data.selectedPageSize"></select>
                    @lang('messages.entries_txt') 
                </label>
            </div>
        </div>

        <div class="table-responsive text-nowrap mt-3">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>
                            <div class="checkbox">
                                <input type="checkbox" id="checkboxMain" class="checkbox-input" data-bind="checked:$root.selectProductCheck" />
                                <label for="checkboxMain"></label>
                            </div>
                        </th>
                        <th>#</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Sub Sub Category</th>
                        <th>Weight</th>
                        <th>Image</th>
                        <th>Size</th>
                        <th>Is Popular?</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody  class="table-border-bottom-0" data-bind="visible:$root.Records().length > 0, foreach:$root.Records">
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" class="checkbox-input" data-bind="checked:$data.checked, attr:{'id':'checkbox'+ $data.id()},click: $root.productCheckboxCheck.bind($data)" />
                                <label data-bind="attr:{'for':'checkbox'+ $data.id()}"></label>
                            </div>
                        </td>
                        <td data-bind="text:$data.Index"></td>
                        <td data-bind="text:$data.code"></td>
                        <td data-bind="text:$data.category_name"></td>
                        <td data-bind="text:$data.sub_category_name"></td>
                        <td data-bind="text:$data.sub_sub_category_name"></td>
                        <td>
                            <div class="d-flex" style="cursor: pointer;">
                                <div data-bind="visible:$data.is_edit() == false ,text:$data.weight, event:{dblclick:$root.updateWeightHandler}"></div>
                                <div data-bind="visible:$data.is_edit">
                                    <input type="text" class="form-control" data-bind="value:$data.weight" />
                                    <button class="btn btn-primary" data-bind="click:$root.updateWeight"><i class="bx bx-check"></i></button>
                                    <button class="btn btn-secondary" data-bind="click:$root.removeWeight"><i class="bx bx-x"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a data-bind="attr:{href:$data.image}" target="_blank">
                                <img class="img-fluid" data-bind="attr:{src:$data.thumb_image}" style="width:100px;height:100px;object-fit:contain;" />
                            </a>
                        </td>
                        <td data-bind="text:$data.size_name"></td>
                        <td data-bind="text:$data.is_popular"></td>
                        <td>
                            <label class="form-switch">
                                <input type="checkbox" data-bind="checked: $data.status,attr:{name:$data.id()+'status',id:$data.id()},click:$root.stateChange">
                                <i></i>
                            </label>
                        </td>
                        <td>
                            <div class="btn-group">
                                <!-- <button class="btn btn-sm icon" data-bind="click:$root.updateRecord"><i class="bx bxs-edit"></i></button> -->
                                <button class="btn btn-sm icon" data-bind="click:$root.productImageEventHandler">
                                    <i class='bx bx-images'></i>
                                </button>
                                <button class="btn btn-sm icon" data-bind="click:$root.deleteRecord"><i class="bx bxs-trash"></i></button>
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
<div class="modal fade" id="addRecord" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form class="modal-content" data-bind="with:$root.recordModel" id="addForm">

            <input type="hidden" name="id" data-bind="value:$data.id" />
            <div class="modal-header">
              <h5 class="modal-title" data-bind="text:$data.id() > 0 ? 'Update' : 'Add'">Modal title</h5>
              <button type="button" class="btn-close" class="close" data-bind="click:$root.closeModal"></button>
            </div>

            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-12 col-lg-6 mb-3" id="categoryId">
                        <label>Category</label>
                        <div class="form-group">
                            <select class="form-control" name="lu_category_id" data-bind="options:$root.categories, optionsText:'name', optionsValue:'id', value:$data.lu_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Category', dropdownParent: $('#categoryId')}"></select>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-3" id="subCategoryId">
                        <label>Sub Category</label>
                        <div class="form-group">
                            <select class="form-control" name="lu_sub_category_id" data-bind="options:$root.subCategories, optionsText:'name', optionsValue:'id', value:$data.lu_sub_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Category', dropdownParent: $('#subCategoryId')}"></select>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-3" id="subSubCategoryId">
                        <label>Sub Sub Category</label>
                        <div class="form-group">
                            <select class="form-control" name="sub_sub_category_id" data-bind="options:$root.subSubCategories, optionsText:'name', optionsValue:'id', value:$data.sub_sub_category_id, optionsCaption:'Select Category', select2:{ placeholder: 'Select Category', dropdownParent: $('#subSubCategoryId')}"></select>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-3" id="searchTermId">
                        <label>Search Terms</label>
                        <div class="form-group">
                            <select class="form-control" name="search_term_id[]" multiple="multiple" data-bind="options:$root.searchTerms, optionsText:'name', optionsValue:'id', selectedOptions:$data.search_term_id, optionsCaption:'Select Term', select2:{ placeholder: 'Select Term', dropdownParent: $('#searchTermId')}"></select>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-3" id="codeId">
                        <label>Code</label>
                        <div class="form-group">
                            <select class="form-control" data-bind="options:$root.codes, optionsText:'name', optionsValue:'id', value:$data.lu_code_id, optionsCaption:'Select Code', select2:{ placeholder: 'Select Code', dropdownParent: $('#codeId')}"></select>
                        </div>
                    </div>

                    <input type="hidden" name="code" class="form-control" data-bind="value:$data.code" autocomplete="off" />

                    <div class="col-12 col-lg-6 mb-3">
                        <label>Image</label>
                        <div class="form-group">
                            <input type="file" class="form-control" name="attachment" accept="image/*" data-bind="value: $data.attachment" />
                        </div>
                    </div>

                    <!-- ko if:$data.selectedSizes -->
                    <!-- ko foreach:$data.selectedSizes -->
                    <div class="input-group">
                        <div class="input-group-text">
                            <input class="form-check-input" type="checkbox" data-bind="checkedValue: $data, checked: $data.is_select,attr:{'id':'qua_'+$data.id()}">
                            <label class="form-check-label ps-2" data-bind="text:$data.name,attr:{'for':'qua_'+$data.id()}"></label>
                        </div>
                        <input type="text" class="form-control" data-bind="value:$data.weight, attr:{'disabled':!($data.is_select())}" min="0" placeholder="Enter Weight" />
                        <select class="form-select" data-bind="options:$root.percentages, value:$data.percentage, optionsCaption:'Select Percentage', attr:{'disabled':!($data.is_select())}"></select>
                    </div>
                    <!-- /ko -->
                    <!-- /ko -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" data-bind="click:$root.saveRecord,text:$data.id() > 0 ? '@lang('messages.update_txt')' : '@lang('messages.add_txt')'"></button>
                <button type="button" class="btn btn-secondary" data-bind="click:$root.closeModal">@lang('messages.cancel_txt')</button>
            </div>
        </form>
    </div>
</div>

<div class="kanban-sidebar">
    <div class="card shadow-none quill-wrapper">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom px-2 py-1">
            <h5 class="card-title fw-bold text-dark">Images</h5>
            <button type="button" class="close close-icon" data-bind="click:$root.closeSidePanel">
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                    </svg>
                </span>
            </button>
        </div>
        
        <div class="kanban-item">
            <div class="card-content">
                <div class="card-body">
                    <div class="row" data-bind="with:$root.productImageModal">
                        <div class="col-12 text-center">
                            <span class="ml-1 font-weight-bold" data-bind="text:$data.name"></span>
                        </div>
                        <div class="col-12 col-md-9 mx-auto mt-1">
                            <form class="form-group add-new-file text-center" id="add_record">
                                <label for="getFile" class="btn btn-primary"><i class="bx bx-plus"></i>Add File</label>
                                <input type="file" class="d-none" multiple="multiple" id="getFile" autocomplete="off" accept="image/*" name="image[]" />
                                <input type="hidden" name="product_id" autocomplete="off" data-bind="value:$data.product_id" />
                            </form>
                        </div>
                    </div>

                    <div class="row product-images-sidebar mt-3" data-bind="visible:$root.productImages().length > 0,foreach:$root.productImages()">
                        <div class="col-md-4 col-6 pl-25 pr-0 pb-25">
                            <div class="thumbnail">
                                <div class="caption">
                                    <a class="badge badge-light-success" data-bind="attr:{href:$data.image,target:'_blank'}"><i class='bx bx-show'></i></a>
                                    <a class="badge badge-danger" data-bind="click:$root.deleteProductImage"><i class='bx bx-trash'></i></a>
                               </div>
                                <img class="img-fluid product-img" data-bind="attr:{src:$data.thumb_image,alt:$data.image}" >
                            </div>
                        </div>
                    </div>

                    <div class="row" data-bind="visible:$root.productImages().length == 0">
                        <div class="col-md-6 mx-auto mt-2">
                            <img src="{{asset('public/img/no-image-found.png')}}" class="img-fluid" />
                            <h6 class="mt-2">No Image Found</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="reset" class="btn btn-danger" data-bind="click:$root.closeSidePanel">
                    <i class="bx bx-x"></i>
                    <span>Close</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('public/vendor/select2/js/select2.min.js')}}"></script>
<script type="text/javascript">
    var getRecords = "{{$records}}", storeRecord="{{$storeRecord}}", storeWeightUpdate="{{$storeWeightUpdate}}", destroyRecord="{{$destroyRecord}}", configData=@json($configData), getProductImagesRoute="{{$getProductImagesRoute}}", storeProductImageRoute="{{$storeProductImageRoute}}", deleteProductImageRoute="{{$deleteProductImageRoute}}", storePopularURL ="{{route('products.store_popular')}}", generateSingleCatalogue="{{route('invoices.generate_single_catalogue')}}", changeStatus="{{$changeStatus}}";
</script>
<script src="{{asset('public/js/pagejs/products.js?'.time())}}"></script>
@endsection