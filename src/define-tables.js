var Utils = require("./utils");


module.exports = function( config ) {
    var tables = {};
    var table;
    var tableName, tableDef;
    var fieldName, fieldType;

    for( tableName in config.data ) {
        if( tableName.charAt(0) == '$' ) continue;
        table = {};
        tables[tableName] = table;
        tableDef = config.data[tableName];
        for( fieldName in tableDef ) {
            fieldType = tableDef[fieldName];
            if( Utils.isBaseType( fieldType ) ) {
                table[fieldName] = fieldType;
            } else {
                fieldType = removeSpecialAttributes( fieldType );
                if( typeof config.data[fieldType] === 'undefined' ) {
                    throw Error( "Invalid type `" + fieldType + "` "
                                 + "of field `" + fieldName + "` "
                                 + "in table `" + tableName + "`!" );
                }
                addLinkTable( tables, tableName, fieldType );
            }
        }
    }

    return tables;
};


function addLinkTable( tables, table1, table2 ) {
    var name = Utils.getLinkName( table1, table2 );
    if( typeof tables[name] === 'undefined' ) {
        tables[name] = {};
        tables[name][table1.toLowerCase()] = "INT";
        tables[name][table2.toLowerCase()] = "INT";
    }
    return tables[name];
}

function removeSpecialAttributes( type ) {
    if( type.length < 2 ) return type;
    var lastChar = type.charAt( type.length - 1 );
    if( lastChar == '*' ) return type.substr( 0, type.length - 1 );
    return type;
}


