var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function User( attribs ) {
    Crud.Model.call( this, attribs, ["login","password","name","comment","roles","creation"], [] );
}

// Inheritance from Widget
User.prototype = Object.create(Crud.Model.prototype);
User.prototype.constructor = User;



module.exports.create = function( obj ) {
    return WS.get( 'test.User.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( 'test.User.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( 'test.User.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.User.delete' );
};
