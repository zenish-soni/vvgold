var docDefaultPageSize = 100;
var AllText = 'All';
const swalWithBootstrapButtons = swal.mixin({
    confirmButtonClass: 'btn round btn-color-1',
    cancelButtonClass: 'btn round btn-color-2',
    buttonsStyling: false,
});

function CheckErrors(currentForm) {

    if (!jQuery(currentForm).valid()) {
        //$('.input-validation-error.tooltip-danger').tooltip('hide');
        //$('.select-validatethis.tooltip-danger').tooltip('hide');
        //$('.jcf-class-validatethis.tooltip-danger').tooltip('hide');
        //$('.tooltip-danger:first').focus();

        //$('html, body').animate({
        //    scrollTop: $($('.tooltip-danger:first')).offset().top - 70
        //}, 1000);
        return false;
    }
    return true;
}

$(document).ready(function () {
    $('input:first').focus();
});

var myApp;
myApp = myApp || (function () {
    return {
        showPleaseWait: function () {
            $(".page-loader-wrapper").show();
        },
        hidePleaseWait: function () {
            $(".page-loader-wrapper").hide();
        }
    };
})();

var contentSidebar;
contentSidebar = contentSidebar || (function () {
    return {
        open: function () {
            /*$("body").append('<div class="slide-panel-opacity"></div>');
            $("body").find(".slide-panel-opacity").addClass('show');
            $("body").addClass('slide-open');
            $(".slide-panel-content").addClass('open');*/

            //Kanban Sidebar
            $("body").append('<div class="kanban-overlay show"></div>');
            $(".kanban-overlay, .kanban-sidebar").addClass('show');
            $("body").addClass('kanban-open');
        },
        close: function () {
            /*$("body").find('.slide-panel-opacity').remove();
            $("body").removeClass('slide-open');
            $(".slide-panel-content").removeClass('open');*/

            //Kanban Sidebar
            $("body").find('.kanban-overlay').remove();
            $(".kanban-overlay, .kanban-sidebar").removeClass('show');
            $("body").removeClass('kanban-open');
        }
    };
})();

var hideLoading = true;

function AjaxCall(url, postData, httpmethod, calldatatype, contentType, showLoading, hideLoadingParam, isAsync) {
	//if (url && !url.match(/^http([s]?):\/\/.*/))
		/*url = baseUrl+url;
	else
		url = url;*/

    url = url;
    
    if (hideLoadingParam != undefined && !hideLoadingParam)
        hideLoading = hideLoadingParam;
    if (contentType == undefined)
        contentType = "application/x-www-form-urlencoded;charset=UTF-8";

    if (showLoading == undefined)
        showLoading = false;

    if (showLoading == false || showLoading.toString().toLowerCase() == "false")
        showLoading = false;
    else
        showLoading = true;

    showLoading = true;
    if (isAsync == undefined)
        isAsync = true;

    return jQuery.ajax({
        type: httpmethod,
        url: url,
        headers:
        {
            //'X-CSRF-Token': page_token
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: postData,
        global: showLoading,
        dataType: calldatatype,
        contentType: contentType,
        async: isAsync,
        processData:false, 
        beforeSend: function() { 
            if (showLoading) myApp.showPleaseWait();
        },
        error: function(xhr, textStatus, errorThrown) {
            if (!userAborted(xhr)) {
                if (xhr.status == 403) {
                    var response = $.parseJSON(xhr.responseText);
                    if (response != null && response.Type == "NotAuthorized" && response.Link != undefined){
                        window.location = response.Link; 
                    }
                }else if (xhr.status == 419 || xhr.status == 401) {
                    window.location.href = window.location.href;    
                }else {
                  //  alert("An error has occured");
                }
            }
        }
    });

}

function UnBlockUI() {
    myApp.hidePleaseWait();
    //KeyPressNumericValidation();
};

$(document).ajaxStop(function (jqXHR, settings) {
    if (hideLoading) {
        UnBlockUI();
    }
});

function userAborted(xhr) {
    return !xhr.getAllResponseHeaders();
}

ko.bindingHandlers.FileUpload = {
    init: function(element, valueAccessor, allBindingsAccessor) {

        var url = valueAccessor();
        var Send = allBindingsAccessor().Send;
        var Done = allBindingsAccessor().Done;

        $(element).fileupload({
            dataType: 'json',
            url: url,

            send: Send,
            done: Done
        });
    }
};

ko.extenders.maxLength = function(target, maxLength) {
    //create a writeable computed observable to intercept writes to our observable
    var result = ko.computed({
        read: target,  //always return the original observables value
        write: function(newValue) {
            var current = target(),
                valueToWrite = newValue ? newValue.substring(0, Math.min(newValue.length, maxLength)) : null;

            //only write if it changed
            if (valueToWrite !== current) {
                target(valueToWrite);
            } else {
                //if the rounded value is the same, but a different value was written, force a notification for the current field
                if (newValue !== current) {
                    target.notifySubscribers(valueToWrite);
                }
            }
        }
    });

    //initialize with current value to make sure it is rounded appropriately
    result(target());

    //return the new computed observable
    return result;
};

ko.bindingHandlers.DateRange = {
    init: function(element, valueAccessor, allBindingsAccessor) {
        //var endDate = $(element).attr('end-date') ? $(element).attr('end-date') : "";
        //var startDate = $(element).attr('start-date') ? $(element).attr('start-date') : "";

        var startDate = "";
        var endDate = "";

        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true, language: window.culture, endDate: endDate, startDate: startDate };
        $(element).datepicker(options);

        //when a user changes the date, update the view model
        $(element).on("changeDate", function(event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(event.date);
                if($(element).hasClass('start-date'))
                    $('.end-date').datepicker('setStartDate', event.date);
                else if ($(element).hasClass('end-date'))
                    $('.start-date').datepicker('setEndDate', event.date);
            }
            $(element).validate();
        });
    },
    update: function(element, valueAccessor) {
        var widget = $(element).data("datepicker");

        //when the view model is updated, update the widget
        if (widget) {
            if (widget.dates.length == 0) {
                if (ko.utils.unwrapObservable(valueAccessor())) {
                    widget.date = moment(ko.utils.unwrapObservable(valueAccessor())).toDate(); //.locale("en-AU").format('l');
                    console.log(widget.date);
                    widget.setDate(widget.date);
                    //widget.setUTCDate(widget.date);
                }
            }
            var value = ko.utils.unwrapObservable(valueAccessor());
            if(value != undefined && value != '')
                widget.setDate(moment(value).toDate());
            
        }
    }
}
ko.bindingHandlers.dateRangepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true, language:window.culture, format: "mm/dd/yyyy", endDate: '+0d' };
        $(element).datepicker(options);

        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(event.date);
            }
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datepicker");
        //when the view model is updated, update the widget
        if (widget) {
            if (ko.utils.unwrapObservable(valueAccessor())) {
                widget.date = moment(ko.utils.unwrapObservable(valueAccessor())).format('l');
                widget.setDate(widget.date);
            }
            widget.setDate();
        }
    }
};

ko.bindingHandlers.dateTimepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions ||{ format: 'mm/dd/yyyy hh:ii' };
        $(element).datetimepicker(options);
       // alert('h');
        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(moment(event.date).utc().format('YYYY/MM/DD H:mm'));
            }
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datetimepicker");
        //when the view model is updated, update the widget
        if (widget) {
            if (ko.utils.unwrapObservable(valueAccessor())) {
                widget.date = new Date(moment(ko.utils.unwrapObservable(valueAccessor())).format('YYYY/MM/DD H:mm'));
                widget.setDate(widget.date);
            }
        }
    }
};

ko.bindingHandlers.datePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions ||{ 
            format: 'DD/MM/YYYY',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            }
        };
        $(element).datetimepicker(options);
       // alert('h');
        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(moment(event.date).utc().format('DD/MM/YYYY'));
            }
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datetimepicker");
        //when the view model is updated, update the widget
        if (widget) {
            if (ko.utils.unwrapObservable(valueAccessor())) {
                widget.date = new Date(moment(ko.utils.unwrapObservable(valueAccessor())).format('DD/MM/YYYY'));
                widget.setDate(widget.date);
            }
        }
    }
};


function ShowNotify(response) {
    $("#toast-container").remove();
    var color = '';
    toastr.options = {
      maxOpened: 1,
      autoDismiss: true,
      timeOut: 5000,
      positionClass: 'toast-top-right'
    };

    if(response.IsSuccess == true){
        color = 'success'
    }else{
        color = 'error'
    }
    toastr[color](response.Message)
    /*
    $.notifyClose();
    var timeOut = 5000, placement={from: 'top',align: 'right'}, color= '', message = 'Message';
    if(response.IsSuccess == true){
        color = 'success'
    }else{
        color = 'danger'
    }

    if(response.Message){
        message = response.Message
    }

    $.notify({
        icon: "nc-icon nc-app",
        message: message

    }, {
        type: color,
        timer: timeOut,
        placement: placement
    });*/
}


ko.bindingHandlers.select2 = {
    after: ["options", "value"],
    init: function (el, valueAccessor, allBindingsAccessor, viewModel) {
        $(el).select2(ko.unwrap(valueAccessor()));
        ko.utils.domNodeDisposal.addDisposeCallback(el, function () {
            $(el).select2('destroy');
        });
    },
    update: function (el, valueAccessor, allBindingsAccessor, viewModel) {
        var allBindings = allBindingsAccessor();
        var select2 = $(el).data("select2");
        if ("value" in allBindings) {
            var newValue = "" + ko.unwrap(allBindings.value);
            if ((allBindings.select2.multiple || el.multiple) && newValue.constructor !== Array) {
                select2.val([newValue.split(",")]);
            }
            else {
                select2.val([newValue]);
            }
        }
    }
};

ko.bindingHandlers.select2Multiple = {
    update: function (element, valueAccessor) {
        $(element).val(valueAccessor()()).select2({
            allowClear: true,
            triggerChange:true
        }).on("change", function (e) {
            // mostly used event, fired to the original element when the value changes

            //debugger;
            valueAccessor()($(this).val());
            $(element).validate();
        });
    }
};


/*$('.input-group-addon').children('.fa.fa-calendar').end().attr("style", "cursor:pointer");
$('.input-group-addon').children('.fa.fa-calendar').end().live('click', function () {
    $(this).closest('.input-group').children('input').datepicker('show');
});
*/
function ParseJsonDate(jsondate) {
    return (eval((jsondate).replace(/\/Date\((\d+)\)\//gi, "new Date($1)")));
}

function ShowDialogMessage(header, type, message) {

/*    BootstrapDialog.show({
        type: type,
        title: header,
        message: message,
        closable: false,
        closeByBackdrop: false,
        closeByKeyboard:false,
        buttons: [{
            label: 'Ok',
            action: function (dialogItself) {
                dialogItself.close();
            }
        }]
    });*/

    //setTimeout(function () {
    //    $("[class='close']").click();
    //}, 2000);

    //BootstrapDialog.TYPE_DEFAULT,
    //BootstrapDialog.TYPE_INFO,
    //BootstrapDialog.TYPE_PRIMARY,
    //BootstrapDialog.TYPE_SUCCESS,
    //BootstrapDialog.TYPE_WARNING,
    //BootstrapDialog.TYPE_DANGER
}

ko.bindingHandlers.required = {
    init: function (element, value) {
        $(element).rules("add", {
            required: true,
            messages: {
                required: value()
            }
        });
        $(element).change(function () {
            $(element).valid();

        });
        $.validator.unobtrusive.parse(element);


    }
    /*End For Custom Validation*/
};

ko.bindingHandlers.floatPercent = {
    update: function (element, valueAccessor, allBindingsAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor()),
            precision = ko.utils.unwrapObservable(allBindingsAccessor().precision) || ko.bindingHandlers.numericText.defaultPrecision,
            formattedValue = value.toFixed(precision);

        var sign = allBindingsAccessor().sign;

        ko.bindingHandlers.text.update(element, function () { return sign != undefined ? formattedValue + ' ' + sign : formattedValue; });
    },
    defaultPrecision: 1
};

ko.validation.rules['url'] = {
  validator: function(val, required) {
    if (!val) {
      return !required
    }
    val = val.replace(/^\s+|\s+$/, ''); //Strip whitespace
    //Regex by Diego Perini from: http://mathiasbynens.be/demo/url-regex
    return val.match(/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.‌​\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[‌​6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1‌​,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00‌​a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u‌​00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i);
  },
  message: 'This field has to be a valid URL'
};
ko.validation.registerExtenders();


function GetURLParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}
var initDecorate=true;
ko.bindingHandlers.decorateErrorElement = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        initDecorate=true;
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        if (valueAccessor != undefined && valueAccessor() != undefined && valueAccessor().isValid) {
        
            var valueIsValid = valueAccessor().isValid();
            var valueIsmodified = valueAccessor().isModified();
            var errorType = allBindingsAccessor().ErrorType;
            
            if(allBindingsAccessor().multipleSelect!=undefined)
            {
                if(initDecorate)
                {
                    initDecorate=false;
                    return;
                }
                element=$(element).next('.ms-parent').children(".ms-choice");
            }

            var showToolTip = function (element) {
                
                element
                .attr("data-original-title", valueAccessor().error())
                .addClass("tooltip-danger").addClass('ko-validation').siblings('span.validationMessage').show();
                element.closest('.input-col').addClass('has-error');
                element.closest('.form-group').addClass('error');
                element.closest('.form-sub-group').addClass('error');
                element.closest('.input-group').addClass('error');
                
            };

            var hideToolTip = function (element) {
                element
                    .removeClass("tooltip-danger").removeClass('ko-validation')
                    .siblings('span.validationMessage').hide();
                
                element.closest('.input-col').removeClass('has-error');
                element.closest('.form-group').removeClass('error');
                element.closest('.form-sub-group').removeClass('error');
                element.closest('.input-group').removeClass('error');
            };

            $('.tooltip-danger').on('focus', function () {
                    $(this).closest('.input-group').removeClass('error');
                    $(this).removeClass('tooltip-danger').removeClass('ko-validation');
            });
            
            if (valueIsmodified) {
                if (!valueIsValid) {
                    showToolTip($(element));
                } else {
                    hideToolTip($(element));
                }
            }
        }
    }
};

$(document).on("input", ".number_only", function() {
    this.value = this.value.replace(/\D/g,'');
});
$(document).on("input", ".text_only", function() {
    this.value = this.value.replace(/[^a-zA-Z]+/, '');
});
$(document).on("input", ".text_space_only", function() {
    this.value = this.value.replace(/[^a-zA-Z ]+/, '');
});