/**********************************************************************
 require( 'config-parser' )
 -----------------------------------------------------------------------
 Read the JSON config whose name has been provided as command line argument.
 **********************************************************************/
var FS = require("fs");
var Path = require("path");

var Utils = require("./utils");


/**
 * @return the config value, on `null` if an error occured while parsing.
 * The followiong attribute will been added to the config value:
 * * __$filename__: full path of the config file.
 * * __$dirname__: full path of the directorfy where the config file lies.
 */
module.exports = function() {
    var args = process.argv;
    args.shift();
    args.shift();

    var filename = args.shift();
    var dirname = Path.dirname( filename );
    var config = JSON.parse( FS.readFileSync( filename ) );

    if( typeof config.data.User === 'undefined' ) config.data.User = {
        login: "CHAR", password: "CHAR", name: "CHAR",
        comment: "TEXT", roles: "TEXT", enabled: "BOOL",
        creation: "DATETIME"
    };

    expand( config );

    // Enrich the config and return it.
    config.$filename = filename;
    config.$dirname = dirname;
    return config;
};


function expand( config ) {
    var tableName, tableDef;
    var fieldName, fieldType;

    for( tableName in config.data ) {
        tableDef = config.data[tableName];
        for( fieldName in tableDef ) {
            fieldType = parseType( tableDef[fieldName] );
            tableDef[fieldName] = fieldType;
        }
    }
}


function parseType( type ) {
    var output = { raw: type };
    if( !Array.isArray( type ) && type.charAt( 0 ) == '#' ) {
        type = type.substr( 1 );
        output.strong = true;
    }

    if( Utils.isBaseType( type ) ) {
        output.type = type;
        return output;
    }

    if( type.charAt( type.length - 1 ) == '*' ) {
        type = type.substr( 0, type.length - 1 ) + ".*";
    }
    var parts = type.split( '.' );
    output.link = parts[0];
    if( parts.length > 1 ) {
        output.list = parts[1];
    }

    return output;
}
