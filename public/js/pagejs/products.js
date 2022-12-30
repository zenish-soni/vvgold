var product = function(item){
    let kk = this;
    kk.is_select = ko.observable(true);
    kk.id = ko.observable().extend({required:true});
    kk.name = ko.observable().extend({required:true});
    kk.percentage = ko.observable().extend({
        required:{
            onlyIf: function(){
                return kk.is_select() == true;
            }
        },min:0
    });
    kk.weight = ko.observable().extend({
        required:{
            onlyIf: function(){
                return kk.is_select() == true;
            }
        },min:0
    });

    kk.is_select.subscribe(newValue => {
        if(newValue == false){
            kk.weight('');
        }
    })

    if(item){
        kk.is_select(item.is_select ? item.is_select : false);
        kk.id(item.id);
        kk.name(item.name);
        kk.weight(item.weight);
    }
}

var ViewModel = function() {
    var self = this;
    self.pager = new Pager();
    self.percentages = ko.observableArray(configData.percentages);
    self.pager.isSearch(false);
    self.Records = ko.observableArray();
    self.categories = ko.observableArray(configData.categories);
    self.sizes = ko.observableArray(configData.sizes);
    self.codes = ko.observableArray(configData.codes);
    self.searchTerms = ko.observableArray(configData.searchTerms);
    self.subCategories = ko.observableArray();
    self.subSubCategories = ko.observableArray();
    self.popularArr = ko.observableArray([{id:1,name:'Yes'},{id:2,name:'No'}]);

    self.SearchCategories = function(){
        var kk = this;
        kk.name= ko.observable();
        kk.is_popular= ko.observable();
        kk.lu_category_id= ko.observable();
        kk.lu_sub_category_id= ko.observable();
        kk.sub_sub_category_id= ko.observable();
        kk.lu_size_id= ko.observable();

        kk.lu_category_id.subscribe(function(value){
            self.subCategories([]);

            const categoryDetail = configData.categories.find(category => category.id == value);
            if(categoryDetail){
                self.subCategories(categoryDetail.sub_category);   
            }
        })

        kk.lu_sub_category_id.subscribe(function(value){
            self.subSubCategories([]);

            const subCategoryDetail = configData.subSubCategories.filter(category => category.lu_sub_category_id == value && category.lu_category_id == kk.lu_category_id());
            if(subCategoryDetail){
                self.subSubCategories(subCategoryDetail);   
            }
        });
    }

    self.FilterIsEnabled = function (data){
        self.SearchModel().MainFilterCategory(data);
        self.pager.currentPage(1);
        self.PageLoad();
    }

    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };

    self.SearchModel = ko.observable(new self.SearchCategories());

    self.recordData = function(){
        var kp = this;
        kp.id = ko.observable(0);
        kp.lu_category_id = ko.observable().extend({required:{message:'Select Category'}});
        kp.lu_sub_category_id = ko.observable();
        kp.sub_sub_category_id = ko.observable();
        kp.search_term_id = ko.observableArray();
        kp.lu_code_id = ko.observable().extend({required:{message:'Select Code'}});
        kp.code = ko.observable().extend({required:{message:'Enter Code'}});
        kp.description = ko.observable();
        kp.selectedSizes = ko.observableArray().extend({required:{message:'Enter Code'}});

        kp.attachment = ko.observable().extend({
            required:{
                onlyIf: function() {
                    return kp.id() == 0;
                }
            }
        });

        kp.lu_category_id.subscribe(function(value){
            self.subCategories([]);

            const categoryDetail = configData.categories.find(category => category.id == value);
            if(categoryDetail){
                self.subCategories(categoryDetail.sub_category);   
            }
        })

        kp.lu_code_id.subscribe(function(value){
            kp.code('');
            
            if(value){
                const codeDetail = configData.codes.find(code => code.id == value);
                if(codeDetail){
                    kp.code(codeDetail.name);   
                }    
            }
        })

        kp.lu_sub_category_id.subscribe(function(value){
            self.subSubCategories([]);

            const subCategoryDetail = configData.subSubCategories.filter(category => category.lu_sub_category_id == value && category.lu_category_id == kp.lu_category_id());
            if(subCategoryDetail){
                self.subSubCategories(subCategoryDetail);   
            }
        });
    }

    self.recordModel = ko.observable(new self.recordData());

    self.addRecord = function(){
        self.recordModel(new self.recordData());
        $("#addRecord").modal('show');
    }

    self.resetDefault = function(){
        ko.utils.arrayForEach(configData.sizes,function(item){
            delete item['is_select'];
            delete item['weight'];
        })
    }

    self.addRecord = function(){
        self.recordModel(new self.recordData());

        if(configData.sizes.length > 0){
            self.resetDefault();
            ko.utils.arrayForEach(configData.sizes,function(item){
                self.recordModel().selectedSizes.push(new product(item))
            })
        }

        $("#addRecord").modal('show');
    }

    self.closeModal = function(){
        $("#addRecord").modal('hide');
        self.PageLoad();
    }

    self.updateRecord = function(data){
        self.recordModel(new self.recordData());
        //self.recordModel(data);
        ko.mapping.fromJS(data,{},self.recordModel());
        self.recordModel().attachment(null);
        $("#addRecord").modal('show');
    }

    self.saveRecord = function(data){
        var error = ko.validation.group(data, {deep:true,live:true});
        if(error().length ==0){
            delete data.errors;
            const selectedValue = self.recordModel().selectedSizes().filter(size => size.is_select() == true)
            if(selectedValue.length > 0){
                
                var formData = new FormData($("form#addForm")[0]);
                formData.append('sizes',ko.toJSON(selectedValue));
                AjaxCall(storeRecord,formData, "POST", "", false,true).done(function (response) {
                    ShowNotify(response);
                    if(response.IsSuccess){
                        $("#addRecord").modal('hide');
                        self.recordModel(new self.recordData());
                        self.PageLoad();
                    }
                });

                /*AjaxCall(storeRecord, ko.toJSON(self.recordModel()), "post", "json", "application/json",true).done(function (response) {
                    ShowNotify(response);
                    if(response.IsSuccess){
                        $("#addRecord").modal('hide');
                        self.recordModel(new self.recordData());
                        self.PageLoad();
                    }
                });*/
            }else{
                const response = {IsSuccess:true, Message:"Select atleast one size"}
                ShowNotify(response);
            }
        }else{
            error.showAllMessages(true);
        }
    }

    self.deleteRecord = function(data){
        swalWithBootstrapButtons({
            title: 'Are you sure?',
            text: "Once deleted, you will not be able to recover this record!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                AjaxCall(destroyRecord,ko.toJSON({ id: data.id }), "post", "json", "application/json",true).done(function (response) {
                    if(response.IsSuccess){
                        swalWithBootstrapButtons(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                        self.PageLoad();
                    }else{
                        ShowNotify(response);
                    }
                });
            }else if(result.dismiss === swal.DismissReason.cancel){
                swalWithBootstrapButtons(
                    'Cancelled',
                    'Your record is safe! :)',
                    'error'
                )
            }
        })
    }

    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage(),
        };

        if (self.pager.sort()) {
            param.SortIndex = self.pager.sort();
            param.SortDirection = self.pager.sortDirection();
        }
        else{
            param.SortIndex = '';
            param.SortDirection = '';
        }

        param.SearchParams = ko.toJS(self.SearchModel());
        param.PageSize = self.pager.iPageSize();

        AjaxCall(getRecords, ko.toJSON({ Data: param }), "post", "json", "application/json",true).done(function (response) {
             if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.Items, {}, self.Records);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
             }
             else
             {
                 ShowAlertMessage(response.Message,'error','Oops! Something went wrong');
             }
        });
    }

    self.ApplyFilter = function(data)
    {
        self.pager.isSearch(true);
        self.pager.search();
    };

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);


    //Update Record
    self.editRecordData = function(){
        var kp = this;
        kp.id = ko.observable().extend({required:{message:'Enter Id'}});
        kp.lu_category_id = ko.observable().extend({required:{message:'Select Category'}});
        kp.lu_sub_category_id = ko.observable();
        kp.sub_sub_category_id = ko.observable();
        kp.code = ko.observable().extend({required:{message:'Enter Code'}});
        kp.weight = ko.observable();
        kp.attachment = ko.observable();

        kp.lu_category_id.subscribe(function(value){
            self.subCategories([]);

            const categoryDetail = configData.categories.find(category => category.id == value);
            if(categoryDetail){
                self.subCategories(categoryDetail.sub_category);   
            }
        })

        kp.lu_sub_category_id.subscribe(function(value){
            self.subSubCategories([]);

            const subCategoryDetail = configData.subSubCategories.filter(category => category.lu_sub_category_id == value && category.lu_category_id == kp.lu_category_id());
            if(subCategoryDetail){
                self.subSubCategories(subCategoryDetail);   
            }
        });
    }

    self.editUpdateModal = ko.observable(new self.editRecordData());

    self.updateWeightHandler = (data) => data.is_edit(true);
    self.removeWeight = (data) => data.is_edit(false);

    self.updateWeight = (data) => {
        const weight = data.weight();

        AjaxCall(storeWeightUpdate, ko.toJSON({ id: data.id(),weight:weight }), "post", "json", "application/json",true).done(function (response) {
            if (response.IsSuccess) {
                data.weight(weight);
                data.is_edit(false)
            }
        });
    }

    //Images
    //Product Images
    self.productImages = ko.observableArray();
    self.productName = ko.observable();

    self.productImageData = function(){
        var kk = this;
        kk.name = ko.observable().extend({required:true});
        kk.product_id = ko.observable().extend({required:true});
        kk.image = ko.observable().extend({required:true});
    }

    self.productImageModal = ko.observable(new self.productImageData());

    self.closeSidePanel = function(){
        contentSidebar.close();
    }

    self.productImageEventHandler = function(data){
        if(data.id() > 0){
            self.productImages([]);
            var productID = data.id();
            self.productImageModal(new self.productImageData());
            self.productImageModal().name(data.code());
            self.productImageModal().product_id(productID);

            self.getProductImages(productID);
            contentSidebar.open();
            self.addProductImage();
        }
    }

    self.getProductImages = function(productID){
        AjaxCall(getProductImagesRoute, ko.toJSON({id:productID}), "post", "json", "application/json",true).done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data, {}, self.productImages);

                if(typeof PerfectScrollbar == 'function'){
                    new PerfectScrollbar(".kanban-sidebar .card-content", {
                        wheelPropagation: false
                    });
                }
            }
        });
    }

    //Store Image
    self.readImageURL = function(input) {
        var url = input.value;
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

        if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
            var formData = new FormData($("form#add_record")[0]);
            AjaxCall(storeProductImageRoute,formData, "POST", "", false,true).done(function (response) {
                if(response.IsSuccess){
                    var data = self.productImageModal();
                    var productID = data.product_id();

                    self.productImageModal(new self.productImageData());
                    self.productImageModal().name(data.name());
                    self.productImageModal().product_id(productID);
                    self.getProductImages(productID);

                    self.addProductImage();
                }
            });
        }else{
            $('#getFile').val('');
            var response = {'Message':'Please select only image'};
            ShowNotify(response);
        }
    }

    self.addProductImage = function(){
        $("input#getFile").change(function(){
            self.readImageURL(this);
        });
    }

    self.deleteProductImage = function(data){
        if(data.id() > 0){
            AjaxCall(deleteProductImageRoute, ko.toJSON({id:data.id()}), "post", "json", "application/json",true).done(function (response) {
                if(response.IsSuccess){
                    var data = self.productImageModal();
                    var productID = data.product_id();
                    self.productImageModal(new self.productImageData());
                    self.productImageModal().name(data.name());
                    self.productImageModal().product_id(productID);
                    self.getProductImages(productID);
                    self.addProductImage();
                }
            });
        }
    }

    //Selected Product
    self.selectedProductIDs = ko.observableArray();

    self.productCheckboxCheck = function(value){
        self.selectedProductIDs([]);
        ko.utils.arrayFirst(self.Records(), function(item) {
            if(item.checked()){
                self.selectedProductIDs.push(item);
            }
        });
        return true;
    }

    self.selectProductCheck = ko.computed({
        read: function() {
            self.selectedProductIDs([]);
            var item = ko.utils.arrayFirst(self.Records(), function(item) {
                if(item.checked()){
                    self.selectedProductIDs.push(item);
                }
                return !item.checked();
            });
            return item == null;
        },
        write: function(value) {
            ko.utils.arrayForEach(self.Records(), function (item) {
                item.checked(value);
            });
        }
    });

    //Add Popular
    self.popularEventHandler = function(){
        if(self.selectedProductIDs().length > 0){
            var productIDs = [];
            ko.utils.arrayFirst(self.selectedProductIDs(), function(item) {
                productIDs.push(item.id());
            });

            AjaxCall(storePopularURL, ko.toJSON({ids:productIDs,status:1}), "post", "json", "application/json",true).done(function (response) {
                if (response.IsSuccess) {
                    self.PageLoad()
                }
                else
                {
                    var response = {'Message':'Oops! Something went wrong'};
                    ShowNotify(response);
                }
            });
        }else{
            var response = {'Message':'Please select as list 1 options'};
            ShowNotify(response);
        }
    }

    //Remove Popular
    self.removePopularEventHandler = function(){
        if(self.selectedProductIDs().length > 0){
            var productIDs = [];
            ko.utils.arrayFirst(self.selectedProductIDs(), function(item) {
                productIDs.push(item.id());
            });

            AjaxCall(storePopularURL, ko.toJSON({ids:productIDs,status:2}), "post", "json", "application/json",true).done(function (response) {
                if (response.IsSuccess) {
                    self.PageLoad()
                }
                else
                {
                    var response = {'Message':'Oops! Something went wrong'};
                    ShowNotify(response);
                }
            });
        }else{
            var response = {'Message':'Please select as list 1 options'};
            ShowNotify(response);
        }
    }

    self.exportData = function(){
        $("#formSearch").submit();
    }

    self.exportSingleData = function(){
        $("#formSearch").attr('action',generateSingleCatalogue).submit();
    }

    self.stateChange = function(){
        var checkedValue = $(event.target).prop('checked');
        var getClickEle = $(event.target).attr('id');
        AjaxCall(changeStatus,ko.toJSON({ id: getClickEle, checkedValue: checkedValue}), "post", "json", "application/json",true).done(function (response) {
            ShowNotify(response);
        });
        return true;
    }
};
                        
$(document).ready(function(){
    ko.applyBindings(new ViewModel());
});