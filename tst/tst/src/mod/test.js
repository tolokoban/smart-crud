var PageLogin = require("test.page-login");

var pageLogin;


exports.onLoginActive = function() {
    if( typeof pageLogin === 'undefined' ) pageLogin = new PageLogin();
    pageLogin.activate();
};
