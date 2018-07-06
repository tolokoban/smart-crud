"use strict";

/**
 * @param {object} def - Must be an expanded definition.
 */
module.exports = function( def ) {
  var output = '';
  var structure = def.structure;
  if( typeof def.structure === 'undefined' ) structure = {};
  else structure = JSON.parse( JSON.stringify( def.structure ) );

  output += getCodeForTables( structure );
  output += getCodeForKeys( structure );
  var links = def.links;
  if( Array.isArray( links ) )
    output += getCodeForLinks( links, structure );

  return output;
};


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
        fieldType = fields[fieldName];
        output += ',\n  `' + fieldName + "` " + fieldType.sql;
        if( typeof fieldType.default !== 'undefined' ) {
          output += " DEFAULT '" + fieldType.default + "'";
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

        if( typeof fieldType.key === 'string' ) {
          switch( fieldType.key ) {
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
    var tableName = linkDef.name;
    var att1 = linkDef.nodes[0].att;
    var att2 = linkDef.nodes[1].att;
    
    output += "DROP TABLE IF EXISTS `${PREFIX}" + tableName + "`;\n";
    output += "CREATE TABLE `" + tableName + "` (\n";
    output += "  `" + att1 + "` INT(11) NOT NULL,\n";
    output += "  `" + att2 + "` INT(11) NOT NULL)\n";
    output += "ALTER TABLE `" + tableName + "` ADD PRIMARY KEY(`" + att1 + "`, `" + att2 + "`);\n\n";

    return output;
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

      }
    }
    return output;
  }
  catch( ex ) {
    throw "Error in the structure:\n" + JSON.stringify( structure, null, '  ' ) + "\n" + ex;
  }
}
