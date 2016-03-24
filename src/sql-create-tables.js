module.exports = function( tables ) {
    var output = '';
    var tableName, fields;
    var fieldName, fieldType;
    var firstField;
    
    for( tableName in tables ) {
        fields = tables[tableName];
        output += "DROP TABLE IF EXISTS `" + tableName + "`;\n";
        output += "CREATE TABLE `" + tableName + "` (";
        firstField = true;
        for( fieldName in fields ) {
            fieldType = convertTypeToSQL( fields[fieldName] );
            if( firstField ) firstField = false;
            else output += ',';
            output += '\n  `' + fieldName + "` " + fieldType;
        }

        output += "\n);\n\n";
    }
    
    return output;
};


function convertTypeToSQL( type ) {
    if( Array.isArray( type ) ) {
        // This is an ENUM.
        var output = "ENUM(";
        type.forEach(function ( name, idx ) {
            if( idx > 0 ) output += ", ";
            output += "'" + name + "'";
        });

        output += ")";
        return output;
    }
    switch( type ) {
    case 'INT': return 'INT';
    case 'FLOAT': return 'FLOAT';
    case 'CHAR': return 'VARCHAR(255)';
    case 'TEXT': return 'TEXT';
    case 'DATE': return 'CHAR(8)';
    case 'DATETIME': return 'CHAR(14)';
    }
    throw Error( "I don't know how to convert `" + type + "` into an SQL type!" );
}
