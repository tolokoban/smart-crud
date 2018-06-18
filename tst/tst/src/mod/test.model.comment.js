var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Comment( attribs ) {
    Crud.Model.call( this, attribs, ["content","author","date","issue"], [] );
}

// Inheritance from Widget
Comment.prototype = Object.create(Crud.Model.prototype);
Comment.prototype.constructor = Comment;



module.exports.create = function( obj ) {
    return WS.get( 'test.Comment.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Comment.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Comment({ 
                        id: id,
                        "content": row[0],
                        "author": row[1],
                        "date": row[2],
                        "issue": row[3]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Comment.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Comment.delete' );
};
