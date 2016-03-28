var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Tag( attribs ) {
    Crud.Model.call( this, attribs, ["name"], ["issues"] );
}

// Inheritance from Widget
Tag.prototype = Object.create(Crud.Model.prototype);
Tag.prototype.constructor = Tag;



module.exports.create = function( obj ) {
    return WS.get( 'test.Tag.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Tag.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Tag({ 
                        id: id,
                        "name": row[0],
                        "issues": row[1]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Tag.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Tag.delete' );
};
