var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Comment( attribs ) {
    Crud.Model.call( this, attribs, ["content","date"], [] );
}

// Inheritance from Widget
Comment.prototype = Object.create(Crud.Model.prototype);
Comment.prototype.constructor = Comment;



module.exports.create = function( obj ) {
    return WS.get( 'test.Comment.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( 'test.Comment.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Comment.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Comment.delete' );
};
