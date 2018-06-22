"use strict";

module.exports = function( def ) {
  var output = '';
  var tableName, fields;
  var fieldName, fieldType;
  var structure;
  if( typeof def.structure === 'undefined' ) structure = {};
  else structure = JSON.parse( JSON.stringify( def.structure ) );

  for( tableName in structure ) {
    fields = structure[tableName];
    output += "DROP TABLE IF EXISTS `${PREFIX}" + tableName + "`;\n";
    output += "CREATE TABLE `" + tableName + "` (\n";
    output += "  `id` int(11) NOT NULL AUTO_INCREMENT,\n";
    output += "  PRIMARY KEY (`id`)";
    for( fieldName in fields ) {
      var prefix = fieldName.charAt(0);
      if( prefix === '!' || prefix === '@' ) continue;
      fieldType = convertTypeToSQL( fields[fieldName] );
      output += ',\n  `' + fieldName + "` " + fieldType;
    }

    output += "\n) DEFAULT CHARSET=utf8;\n\n";
  }

  return output;
};


var TYPES_MAPPING = {
  string: 'TEXT',
  bool: 'TINYINT(1)',
  boolean: 'TINYINT(1)',
  int: 'INT(11)',
  integer: 'INT(11)',
  float: 'FLOAT',
  date: 'CHAR(8)',
  datetime: 'CHAR(14)'
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

  var cleanType = ("" + type).tim().toLowerCase();
  var mappedType = TYPES_MAPPING[cleanType];
  if( mappedType ) return mappedType;

  if( cleanType.substr(0, 6) === 'string' ) {
    return "VARCHAR(" + cleanType.substr(6) + ")";
  }
  throw Error( "I don't know how to convert `" + type + "` into an SQL type!" );
}
