var timerID = 2000;

function empty(val){
    if(val == "" || val == "undefined" || val == null || val == 0 || val == "0" || val == "NULL"){
        return true;
    }
    return false;
}

var LoginModel = function() {
    var self = this;

    //Login
    self.loginData = function(){
        var kp = this;
        kp.email = ko.observable().extend({required:true});
        kp.password = ko.observable().extend({required:true});
        kp.remember = ko.observable();
    }
    self.loginRecord = ko.observable(new self.loginData());

    self.checkLogin = function(data){
        self.sendData('login');
    }

    //Forgot password data
    self.forgotPasswordData = function(){
        var kp = this;
        kp.email = ko.observable().extend({required:true, email:true});
    }
    self.forgotPasswordRecord = ko.observable(new self.forgotPasswordData);

    self.validateForgotPasswordData = function(data){
        self.sendData('forgot-password');
    }

    //Reset password data
    self.resetPasswordData = function(){
        var kp = this;
        kp.user_id = ko.observable().extend({required:true});
        kp.password = ko.observable().extend({required:true}).extend({minLength:6});
    }
    self.resetPasswordRecord = ko.observable(new self.resetPasswordData);

    self.validateResetPasswordData = function(data){
        self.sendData('reset-password');
    }

    //Send data
    self.sendData = function(pageName){
        var modalName;

        switch (pageName){
            case 'login':
                modalName = self.loginRecord();
                url = checkLoginURL;
                break;
        }
        
        if(modalName != undefined && modalName != null && modalName != "0"){
            $("#loginAlert, .alert").hide();
            $(".alert").removeClass('alert-warning alert-info alert-success');

            var error = ko.validation.group(modalName, {deep:true,live:true});
            if(error().length ==0){
                $(".btn").attr('disabled','disabled');
                delete modalName.errors;

                AjaxCall(url, ko.toJSON(modalName), "post", "json", "application/json",true).done(function (response) {
                    if (response.IsSuccess) {
                        $(".alert").addClass('alert-success');
                        setTimeout(function(){window.location.href = response.redirectURL},timerID);
                    }else{
                        $(".btn").removeAttr('disabled','disabled');
                        $(".alert").addClass('alert-warning');
                    }
                    $(".alert").find('.alert-msg').html(response.Message);
                    $("#loginAlert").show();
                });
            }else{
                error.showAllMessages(true);
            }
        }else{
            alert('Error');
        }
    }
};
                        
$(document).ready(function(){
    ko.applyBindings(new LoginModel());
});