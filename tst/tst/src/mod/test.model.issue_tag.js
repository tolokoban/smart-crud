var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Issue_Tag( attribs ) {
    Crud.Model.call( this, attribs, ["id","issue"], [] );
}

// Inheritance from Widget
Issue_Tag.prototype = Object.create(Crud.Model.prototype);
Issue_Tag.prototype.constructor = Issue_Tag;



module.exports.create = function( obj ) {
    return WS.get( 'test.Issue_Tag.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Issue_Tag.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Issue_Tag({ 
                        id: id,
                        "id": row[0],
                        "issue": row[1]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Issue_Tag.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Issue_Tag.delete' );
};
