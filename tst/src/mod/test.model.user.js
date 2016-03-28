var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function User( attribs ) {
    Crud.Model.call( this, attribs, ["login","password","name","comment","roles","enabled","creation"], [] );
}

// Inheritance from Widget
User.prototype = Object.create(Crud.Model.prototype);
User.prototype.constructor = User;



module.exports.create = function( obj ) {
    return WS.get( 'test.User.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( 'test.User.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new User({ 
                        id: id,
                        "login": row[0],
                        "password": row[1],
                        "name": row[2],
                        "comment": row[3],
                        "roles": row[4],
                        "enabled": row[5],
                        "creation": row[6]
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( 'test.User.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.User.delete' );
};
