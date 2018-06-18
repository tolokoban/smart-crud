/**********************************************************************
 require( 'php-create-services' )
 -----------------------------------------------------------------------
 Create all the PHP services needed to access data.
 **********************************************************************/
var Path = require("path");

var Utils = require("./utils");
var Template = require("./template");


/**
 * @param config
 * * __type__: If present, this is a basic type and no other attribute
 is necessary.
 * * __link__: Present  only if `type`  is not.  Name of the  table to
 which this field is a link,
 * * __list__: Present  only if `link` is.  This field is a  list link
 and the value of this attribute is the name of the target table's
 field.
 * * __strong__: Present  only if  `link` is. Means  that the  link is
 part of this object. If we  delete this objet, we must delete all
 the linked objects too.
 */
module.exports = function( config ) {
    var data = config.data;

    var output = {};
    var tableName, scalarFields, setFields;
    var fieldName, fieldType;

    for( tableName in data ) {
        scalarFields = [];
        setFields = [];
        for( fieldName in data[tableName] ) {
            fieldType = data[tableName][fieldName];
            if( fieldType.list ) {
                setFields.push( fieldName );
            } else {
                scalarFields.push( fieldName );
            }
        }
        createRequestService( config, tableName, scalarFields, setFields );
        createJavascriptGlue( config, tableName, scalarFields, setFields );
    }

    Utils.write(
        Path.join( config.$dirname, "src", "mod", "smart-crud.js" ),
        Template.file( 'smart-crud.js' ).out
    );

    return output;
};

function createRequestService( config, tableName, scalarFields, setFields ) {
    var name = config.prefix + "." + tableName + ".request";
    var filenameWithoutExt = Path.join(
        config.$dirname, "src", "tfw", "svc", name
    );

    var lists = [];
    var fieldName, fieldType;
    for( fieldName in config.data[tableName] ) {
        fieldType = config.data[tableName][fieldName];
        if( fieldType.list ) {
            lists.push( [fieldName, fieldType.link, fieldType.list] );
        }
    }

    var rows = "        $id = $row['id'];\n"
            + "        $ids[] = $id;\n"
            + "        $rows[$id] = Array(";
    var first = true;
    var baseIndex = 0;
    for( fieldName in config.data[tableName] ) {
        fieldType = config.data[tableName][fieldName];
        if( fieldType.list ) continue;
        baseIndex++;
        if( first ) first = false;
        else rows += ",";
        rows += "\n            ";
        if( fieldType.link ) {
            rows += "intVal( $row['" + fieldName + "'] )";
        } else {
            switch( fieldType.type ) {
            case 'INT':
                rows += "intVal( $row['" + fieldName + "'] )";
                break;
            case 'FLOAT':
                rows += "intFloat( $row['" + fieldName + "'] )";
                break;
            default:
                rows += "$row['" + fieldName + "']";
            }
        }
    }
    var indexes = [];
    lists.forEach(function ( itm ) {
        rows += ",\n            Array()";
        indexes.push( baseIndex++ );
    });
    rows += ");";

    var codeForLists = '';
    if( lists.length > 0 ) {
        codeForLists = Template.file( 'request.lists.php', {
            INDEXES: indexes.join( ', ' ),
            LISTS: lists.map( function( itm ) {
                return "'" + itm[0] + "' => Array('" + itm[1] + "', '" + itm[2] + "')";
            }).join( ',\n        ' )
        }).out;
    }

    var content = Template.file( 'request.php', {
        NAME: name,
        TABLE: tableName,
        FIELDS: scalarFields.map( function( item ) { return "'" + item + "'"; } ).join( ", " ),
        ROWS: rows,
        LISTS: codeForLists
    });
    Utils.write( filenameWithoutExt + ".php", content.out );
    Utils.write( filenameWithoutExt + ".security", Template.file( 'request.security.php', {
        NAME: name
    } ).out );
}


function createJavascriptGlue( config, tableName, scalarFields, setFields ) {
    var name = config.prefix + ".model." + tableName.toLowerCase();
    var filenameWithoutExt = Path.join(
        config.$dirname, "src", "mod", name
    );

    var request = '';
    scalarFields.forEach(function ( itm, idx ) {
        request += ',\n                        "' + itm + '": row[' + idx + "]";
    });
    setFields.forEach(function ( itm, idx ) {
        request += ',\n                        "' 
            + itm + '": row[' + (scalarFields.length + idx) + "]";
    });


    var content = Template.file( 'javascript-glue.js', {
        NAME: tableName,
        SCALAR: JSON.stringify( scalarFields ),
        SET: JSON.stringify( setFields ),
        PREFIX: config.prefix,
        REQUEST: request
    });
    Utils.write( filenameWithoutExt + ".js", content.out );
}
