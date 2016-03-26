var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Issue( attribs ) {
    Crud.Model.call( this, attribs, ["title","content","date","status","type"], [] );
}

// Inheritance from Widget
Issue.prototype = Object.create(Crud.Model.prototype);
Issue.prototype.constructor = Issue;



module.exports.create = function( obj ) {
    return WS.get( 'test.Issue.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( 'test.Issue.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Issue.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Issue.delete' );
};
