var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Vote( attribs ) {
    Crud.Model.call( this, attribs, ["user","issue","vote"], [] );
}

// Inheritance from Widget
Vote.prototype = Object.create(Crud.Model.prototype);
Vote.prototype.constructor = Vote;



module.exports.create = function( obj ) {
    return WS.get( 'test.Vote.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Vote.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Vote({ 
                        id: id,
                        "user": row[0],
                        "issue": row[1],
                        "vote": row[2]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Vote.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Vote.delete' );
};
