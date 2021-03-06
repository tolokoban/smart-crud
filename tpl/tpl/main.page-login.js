/**********************************************************************
 require( '{{NAME}}.page-login' )
 -----------------------------------------------------------------------

 **********************************************************************/
var $ = require("dom");
var WS = require("tfw.web-service");

// Tracking connection changes.
WS.changeEvent.add(function() {
    if( !WS.user ) {
        // Nobody is connected. We must go to the login page.
        window.location.hash = "/{{NAME}}/login";
    }
});


function PageLogin() {
    var that = this;

    var att, id;
    var attributes = {
        username: "login.username",
        password: "login.password",
        btnOK: "login.btnOK"
    };
    for( att in attributes ) {
        id = attributes[att];
        Object.defineProperty( this, att, {
            value: document.getElementById( id ),
            writable: false,
            enumerable: true,
            configurable: false
        });
    }

    $.on( this.btnOK, function() {
        WS.login( that.username.valu , that.password.value ).then(
            function( user ) {
                console.info("[main.page-login] user=...", user);
                window.location.hash = "/{{NAME}}/menu";
            },
            function( err ) {
                console.info("[main.page-login] err=...", err);
                alert( err );
            }
        );
    });
}


/**
 * @return void
 */
PageLogin.prototype.activate = function() {
    this.username.value = "";
    this.password.value = "";
};


module.exports = PageLogin;
