var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Issue( attribs ) {
    Crud.Model.call( this, attribs, ["title","content","author","date","status","type"], ["comments","votes","tags"] );
}

// Inheritance from Widget
Issue.prototype = Object.create(Crud.Model.prototype);
Issue.prototype.constructor = Issue;



module.exports.create = function( obj ) {
    return WS.get( 'test.Issue.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.Issue.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new Issue({ 
                        id: id,
                        "title": row[0],
                        "content": row[1],
                        "author": row[2],
                        "date": row[3],
                        "status": row[4],
                        "type": row[5],
                        "comments": row[6],
                        "votes": row[7],
                        "tags": row[8]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Issue.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Issue.delete' );
};
