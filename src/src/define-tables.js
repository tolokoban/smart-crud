var Utils = require("./utils");


/**
 * Tables are  created from `config.data`.  Each key is a  table name,
 * and the value is a map of the table's fields. In this map, keys are
 * the field types and values are objets with these attributes:
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
    var tables = {};
    var table;
    var tableName, tableDef;
    var fieldName, fieldType;
    // Sometimes, We must add new tables for links.
    var newTables = {};

    for( tableName in config.data ) {
        table = {};
        tables[tableName] = table;
        tableDef = config.data[tableName];
        for( fieldName in tableDef ) {
            fieldType = tableDef[fieldName];
            if( fieldType.type ) {
                table[fieldName] = fieldType.type;
            }
            else if( fieldType.link ) {
                if( !fieldType.list ) {
                    table[fieldName] = "INT";
                }
                else if( fieldType.list == '*' ) {
                    if( typeof config.data[fieldType.link] === 'undefined' ) {
                        throw Error( "Invalid type `" + fieldType.link + "` "
                                     + "of field `" + fieldName + "` "
                                     + "in table `" + tableName + "`!" );
                    }
                    fieldType.list = tableName.toLowerCase();
                    addLinkTable( tables, tableName, fieldType.link, newTables );
                    fieldType.link = Utils.getLinkName( tableName, fieldType.link );
                }
            }
        }
    }

    if( typeof config.extraTables === 'undefined' ) config.extraTables = {};

    // Adding link tables if any.
    for( tableName in newTables ) {
        config.data[tableName] = newTables[tableName];
        config.extraTables[tableName] = 1;
    }

    return tables;
};


function addLinkTable( tables, table1, table2, newTables ) {
    var name = table1 + "_" + table2;
    if( typeof tables[name] === 'undefined' ) {
        tables[name] = {};
        tables[name][table1.toLowerCase()] = "INT";
        newTables[name] = {
            id: { type: "INT" }
        };
        newTables[name][table1.toLowerCase()] = { type: "INT" };
    }
    return tables[name];
}

function removeSpecialAttributes( type ) {
    if( type.length < 2 ) return type;
    var lastChar = type.charAt( type.length - 1 );
    if( lastChar == '*' ) return type.substr( 0, type.length - 1 );
    return type;
}
