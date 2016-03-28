var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Tag_Issue( attribs ) {
    Crud.Model.call( this, attribs, ["id","tag"], [] );
}

// Inheritance from Widget
Tag_Issue.prototype = Object.create(Crud.Model.prototype);
Tag_Issue.prototype.constructor = Tag_Issue;



module.exports.create = function( obj ) {
    return WS.get( 'test.Tag_Issue.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Tag_Issue.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Tag_Issue({ 
                        id: id,
                        "id": row[0],
                        "tag": row[1]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Tag_Issue.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Tag_Issue.delete' );
};
