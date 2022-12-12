var ViewModel = function() {
    var self = this;
    self.pager = new Pager();
    self.pager.isSearch(false);
    self.Records = ko.observableArray();
    self.percentages = ko.observableArray(percentages);

    self.recordData = function(){
        var kp = this;
        kp.id = ko.observable(0);
        kp.name = ko.observable().extend({required:{message:'Enter name'}});
        kp.percentage = ko.observable().extend({required:{message:'Select Percentage'}});
    }

    self.SearchCategories = function(){
        var kk = this;
        kk.name= ko.observable();
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
    self.recordModel = ko.observable(new self.recordData());

    self.addRecord = function(){
        self.recordModel(new self.recordData());
        $("#addRecord").modal('show');
    }

    self.closeModal = function(){
        $("#addRecord").modal('hide');
        self.PageLoad();
    }

    self.updateRecord = function(data){
        self.recordModel(data);
        $("#addRecord").modal('show');
    }

    self.saveRecord = function(data){
        var error = ko.validation.group(data, {deep:true,live:true});
        if(error().length ==0){
            delete data.errors;
            
            AjaxCall(storeRecord,ko.toJSON(self.recordModel()), "post", "json", "application/json",true).done(function (response) {
                ShowNotify(response);
                if(response.IsSuccess){
                    $("#addRecord").modal('hide');
                    self.recordModel(new self.recordData());
                    self.PageLoad();
                }
            });
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
                AjaxCall(destoryRecord,ko.toJSON({ id: data.id }), "post", "json", "application/json",true).done(function (response) {
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