"use strict";

module.exports = function( def ) {
  var output = '';
  var tableName, fields;
  var fieldName, fieldType;
  var prefix;
  var structure;
  if( typeof def.structure === 'undefined' ) structure = {};
  else structure = JSON.parse( JSON.stringify( def.structure ) );

  addTableUser( structure );
  output += getCodeForTables( structure );
  output += getCodeForKeys( structure );
  var links = def.links;
  if( Array.isArray( links ) )
    output += getCodeForLinks( links, structure );

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

/**
 * Add attribute `sql` to the type definition.
 * This is the SQL type.
 */
function expandType( typedef ) {
  var type = typedef.type;
  if( Array.isArray( type ) ) {
    // This is an ENUM.
    var output = "ENUM(";
    type.forEach(function ( name, idx ) {
      if( idx > 0 ) output += ", ";
      output += "'" + name + "'";
    });
    output += ")";
    typedef.sql = output;
    return typedef;
  }

  var cleanType = ("" + type).trim().toLowerCase();
  var mappedType = TYPES_MAPPING[cleanType];
  if( mappedType ) {
    typedef.sql = mappedType;
    return typedef;
  }

  if( cleanType.substr(0, 6) === 'string' ) {
    typedef.sql = "VARCHAR(" + cleanType.substr(6) + ")";
    return typedef;
  }
  throw Error( "I don't know how to convert `" + type + "` into an SQL type!" );
}


function addTableUser( structure ) {
  if( typeof structure.user === 'undefined' ) structure.user = {};
  delete structure.id;
  var user = structure.user;
  user.login = { type: "string256", key: "unique" };
  user.password = { type: "string256" };
  user.name = { type: "string256", key: "unique" };
  user.roles = { type: "string512", default: "[]" };
  user.enabled = { type: "boolean" };
  user.creation = { type: "datetime" };
  user.data = { type: "string" };
}


function getCodeForTables( structure ) {
  var output = '';
  var tableName, fields, fieldName, fieldType;

  try {
    for( tableName in structure ) {
      fields = structure[tableName];
      output += "DROP TABLE IF EXISTS `${PREFIX}" + tableName + "`;\n";
      output += "CREATE TABLE `" + tableName + "` (\n";
      output += "  `id` INT(11) NOT NULL AUTO_INCREMENT";
      for( fieldName in fields ) {
        if( fields[fieldName].link ) continue;

        try {
          fieldType = expandType( fields[fieldName] );
          output += ',\n  `' + fieldName + "` " + fieldType.sql;
          if( typeof fieldType.default !== 'undefined' ) {
            output += " DEFAULT '" + fieldType.default + "'";
          }
        }
        catch( ex ) {
          throw "Unable to parse " + fieldName + ": " + JSON.stringify( fields[fieldName] ) + "\n" + ex;
        }
      }
      output += "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n\n";
    }
  }
  catch( ex ) {
    throw "Error in the structure:\n" + JSON.stringify( structure, null, '  ' ) + "\n" + ex;
  }

  return output;
}


function getCodeForKeys( structure ) {
  var output = '';
  var tableName, fields, fieldName, fieldType;

  try {
    for( tableName in structure ) {
      output += "ALTER TABLE `${PREFIX}" + tableName + "`\n";
      output += "  ADD PRIMARY KEY (`id`)";
      fields = structure[tableName];
      for( fieldName in fields ) {
        fieldType = fields[ fieldName ];
        if( fieldType.link ) continue;

        if( typeof fieldType.key === 'string' ) {
          switch( fieldType.key.toLowerCase() ) {
          case 'index':
            output += ",\n  ADD KEY `" + fieldName + "` (`" + fieldName + "`)";
            break;
          case 'unique':
            output += ",\n  ADD UNIQUE KEY `" + fieldName + "` (`" + fieldName + "`)";
            break;
          }
        }
      }
      output += ";\n\n";
    }
  }
  catch( ex ) {
    throw "Error in the structure:\n" + JSON.stringify( structure, null, '  ' ) + "\n" + ex;
  }

  return output;
}


function getCodeForLinks( links, structure ) {
  var output = '';

  links.forEach(function (link) {
    output += getCodeForLink( link, structure );
  });
  return output;
}

function getCodeForLink( linkDef, structure ) {
  try {
    var output = '';

    var link = parseLink( link, structure );
  }
  catch( ex ) {
    throw "Unable to create code for link definition: " + JSON.stringify( linkDef ) + "!\n" + ex;
  }
}


function toto() {
  var tableName, fields, fieldName, fieldType;

  try {
    for( tableName in structure ) {
      fields = structure[tableName];
      for( fieldName in fields ) {
        fieldType = fields[ fieldName ];
        if( !fieldType.link ) continue;

        if( typeof structure[fieldType.type] === 'undefined' ) {
          throw "The field `" + fieldName + "` of class `" + tableName + "` must have another class as type!\n"
            + "But you set `" + fieldType.type + "`.";
        }

        output += "DROP TABLE IF EXISTS `${PREFIX}" + tableName + "`;\n";
        output += "CREATE TABLE `" + tableName + "_" + fieldName + "_" + fieldType.type + "` (\n";
        output += "  `" + tableName + "_id` INT(11) NOT NULL,\n";
        output += "  `" + fieldType.type + "_id` INT(11) NOT NULL)\n";
        output += "ALTER TABLE `" + tableName + "_" + fieldName + "_" + fieldType.type
          + "` ADD PRIMARY KEY(`" + tableName + "_id`, `" + fieldType.type + "_id`);\n\n";
      }
    }
    return output;
  }
  catch( ex ) {
    throw "Error in the structure:\n" + JSON.stringify( structure, null, '  ' ) + "\n" + ex;
  }
}
