var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function {{NAME}}( attribs ) {
    Crud.Model.call( this, attribs, {{SCALAR}}, {{SET}} );
}

// Inheritance from Widget
{{NAME}}.prototype = Object.create(Crud.Model.prototype);
{{NAME}}.prototype.constructor = {{NAME}};



module.exports.create = function( obj ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return new Promise(function( resolve, reject ) {
        WS.get( '{{PREFIX}}.{{NAME}}.request', criteria ).then(
            function( data ) {
                var parsedRows = [];
console.info("[javascript-glue] data.rows=...", data.rows);
                var id, row;
                for( id in data.rows ) {
                    row = data.rows[id];
                    parsedRows.push(new {{NAME}}({ 
                        id: id{{REQUEST}}
                    }));
                };
            }, reject
        );
    });
};

module.exports.update = function( obj ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.update' );
};

module.exports.delete = function( id ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.delete' );
};
