var Pager = function () {
    var self = this;
    const ascDirection = 'ASC';
    const descDirection = 'DESC';
    self.iTotalDisplayRecords = ko.observable();
    self.iPageSize = ko.observable(2);
    self.currentPage = ko.observable(1);
    self.iTotalRecords = ko.observable();
    self.getDataCallback = function () {
        alert('Please Override getDataCallback');
    };

    self.sort = ko.observable();
    self.sort.extend({ notify: 'always' });
    self.sortDirection = ko.observable(ascDirection);

    self.isSearch = ko.observable(false);
    self.searchBy = ko.observable('');
    self.isActive = ko.observable(1);
    self.searchText = ko.observable().extend({ throttle: 500 }); ;
    self.displayTotalPages = ko.observable(5);

    self.pageSizeOptions = ko.observableArray([5, 10, 15, 20, 25,100,200,'All']);
    self.selectedPageSize = ko.observable(self.iPageSize());
    self.selectedPageSize.subscribe(function (newSize) {
        if(newSize==AllText)
            newSize=self.iTotalRecords();
        self.iPageSize(newSize);
        self.currentPage(1);
        if (self.getDataCallback != undefined){
            self.getDataCallback();
        }
    });
    
    self.currentSort = ko.observable();
    self.sortModel = function () {
        var isDescendingDefault = false;
        var stModel = this;
        stModel.sort = ko.observable();
        stModel.sortDirection = ko.observable(isDescendingDefault ? descDirection: ascDirection);
        stModel.isDesending = ko.observable(isDescendingDefault);
        stModel.sort.extend({ notify: 'always' });
        stModel.sort.subscribe(function (newval) {
            if(self.sort() == newval && stModel.sortDirection() != self.sortDirection())
            {
                stModel.sortDirection(stModel.sortDirection() == ascDirection ? descDirection: ascDirection);
            }
            self.sort(newval);
            self.currentSort(stModel);
            if (stModel.sortDirection() == ascDirection) {
                stModel.sortDirection(descDirection);
                self.sortDirection(descDirection);
                stModel.isDesending(true);
            } else {
                stModel.sortDirection(ascDirection);
                self.sortDirection(ascDirection);
                stModel.isDesending(false);
            }
            self.getDataCallback();
        });
        return stModel;
    };

    self.search = function () {
//      if(data.SearchCategory != undefined || data.SearchCategory != ''){
            self.currentPage(1);
            self.getDataCallback();
            self.isSearch(true);
//      }else{
//          return false;
//      }
    };

    self.clearSearch = function () {
        self.searchBy('');
        self.isActive(1);
        self.searchText('');
        self.isSearch(false);
        self.getDataCallback();
    };

    self.pagesToShow = ko.observableArray();
    self.allPages = ko.dependentObservable(function () {
        var pages = [];
        var pagesToShow = pages;

        for (var i = 1; i <= Math.ceil(self.iTotalRecords() / self.iPageSize()); i++) {
          pages.push({ pageNumber: (i) });
        }
        if (pages.length > self.displayTotalPages()) {
            //if (self.currentPage() > Math.ceil(self.displayTotalPages() / 2)) {
            //    var start = (self.currentPage() - Math.floor(self.displayTotalPages() / 2));
            //    var end = start + self.displayTotalPages();
            //    pagesToShow = pages.slice(start, end);
            //} else {
            //    var start = 0;
            //    var end = start + self.displayTotalPages();
            //    pagesToShow = pages.slice(start, end);
            //}
            var count = Math.ceil(self.currentPage() / self.displayTotalPages());
            var start = (count - 1) * self.displayTotalPages();
            var end = start + self.displayTotalPages();
            pagesToShow = pages.slice(start, end);
        }

        self.pagesToShow(pagesToShow);
        
        return pages;
    });

    self.previousPage = function () {
        if (self.currentPage() > 1) {
            self.moveToPage(self.currentPage() - 1);
        }
    };

    self.nextPage = function () {
        if (self.currentPage() < self.allPages().length) {
            self.moveToPage(self.currentPage() + 1);
        }
    };

    self.gotoPage = function (e) {
        if (e.pageNumber != self.currentPage()) {
            self.moveToPage(e.pageNumber);
        }
    };

    self.lastPage = function () {
        if (self.currentPage() < self.allPages().length) {
            self.moveToPage( self.allPages().length);
        }
    };
    
    self.firstPage = function () {
        if (self.currentPage() >= 1) {
            self.moveToPage(1);
        }
    }

    self.moveToPage = function (index) {
        self.currentPage(index);
        self.getDataCallback();
    };

    
    self.FirstItemIndex = ko.computed(function () {
        return self.iPageSize() * (self.currentPage()-1) + 1;
    });

    self.LastItemIndex = ko.computed(function () {
        return Math.min(self.FirstItemIndex() + self.iPageSize() - 1, self.iTotalRecords());
    });
    
    //    self.PageCountMessage = ko.computed(function () {
    //        var from = (3 * (self.currentPage() - 1)) + 1;
    //        var to = from + self.iTotalDisplayRecords() - 1;
    //        if (self.iTotalDisplayRecords()) {
    //            return 'Showing ' + from + ' to ' + to + ' of ' + self.iTotalRecords() + ' entries';
    //        }
    //    });

    return self;
};