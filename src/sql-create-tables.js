module.exports = function( tables ) {
    var output = '';
    var tableName, fields;
    var fieldName, fieldType;
    
    for( tableName in tables ) {
        fields = tables[tableName];
        output += "DROP TABLE IF EXISTS `" + tableName + "`;\n";
        output += "CREATE TABLE `" + tableName + "` (\n";
        output += "  `id` int(11) NOT NULL AUTO_INCREMENT";
        for( fieldName in fields ) {
            fieldType = convertTypeToSQL( fields[fieldName] );
            output += ',\n  `' + fieldName + "` " + fieldType;
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
    case 'BOOL': return 'tinyint(1)';
    case 'FLOAT': return 'FLOAT';
    case 'CHAR': return 'VARCHAR(255)';
    case 'TEXT': return 'TEXT';
    case 'DATE': return 'CHAR(8)';
    case 'DATETIME': return 'CHAR(14)';
    }
    throw Error( "I don't know how to convert `" + type + "` into an SQL type!" );
}
