var ViewModel = function() {
    var self = this;
    self.pager = new Pager();
    self.pager.isSearch(false);
    self.Records = ko.observableArray();
    self.users = ko.observableArray(users);

    self.SearchCategories = function(){
        var kk = this;
        kk.name= ko.observable();
        kk.user_id= ko.observable();
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

    self.changeStatus = function(status,data){
        swalWithBootstrapButtons({
            title: 'Are you sure?',
            text: "Once changed, you will not be able to recover this record!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                AjaxCall(changeStatus,ko.toJSON({ id: data.id, status:status }), "post", "json", "application/json",true).done(function (response) {
                    ShowNotify(response);
                    self.PageLoad();
                });
            }
        })
    }

    //Details
    self.details = ko.observableArray();
    self.totalWeight = ko.observable();

    self.showDetailHandler = (data) => {
        self.details(data.details());
        self.totalWeight(data.total_weight());
        $("#viewDetail").modal('show');
    }

    self.closeModal = () => $("#viewDetail").modal('hide');

    //Generate PDF
    self.generatePDF = (data) => {
        AjaxCall(generateInvoice,ko.toJSON({ id: data.id }), "post", "json", "application/json",true).done(function (response) {
            ShowNotify(response);
        });
    }
};
                        
$(document).ready(function(){
    ko.applyBindings(new ViewModel());
});