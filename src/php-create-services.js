/**********************************************************************
 require( 'php-create-services' )
 -----------------------------------------------------------------------
 Create all the PHP services needed to access data.
 **********************************************************************/
var Path = require("path");

var Utils = require("./utils");
var Template = require("./template");


module.exports = function( config ) {
    var data = config.data;

    var output = {};
    var tableName, fields;
    var fieldName, fieldType;
    
    for( tableName in data ) {
        fields = [];
        for( fieldName in data[tableName] ) {
            fieldType = data[tableName][fieldName];
            if( Utils.isScalarType( fieldType ) ) {
                fields.push( fieldName );
            }
        }
        createRequestService( config, tableName, fields );

        createJavascriptGlue( config, tableName, fields );
    }

    return output;
};

function createRequestService( config, tableName, fields ) {
    var name = config.prefix + "." + tableName + ".request";
    var filenameWithoutExt = Path.join(
        config.$dirname, "src", "tfw", "svc", name        
    );
    
    var content = Template.file( 'request.php', {
        NAME: name,
        TABLE: tableName,
        FIELDS: fields.map( function( item ) { return "'" + item + "'"; } ).join( ", " )
    });
    Utils.write( filenameWithoutExt + ".php", content.out );
    Utils.write( filenameWithoutExt + ".security", Template.file( 'request.security.php', {
        NAME: name
    } ).out );
}


function createJavascriptGlue( config, tableName, fields ) {
    var name = config.prefix + ".model." + tableName.toLowerCase();
    var filenameWithoutExt = Path.join(
        config.$dirname, "src", "mod", name
    );
    
    var content = Template.file( 'javascript-glue.js', {
        NAME: tableName,
        FIELDS: JSON.stringify( fields )
    });
    Utils.write( filenameWithoutExt + ".js", content.out );
}
