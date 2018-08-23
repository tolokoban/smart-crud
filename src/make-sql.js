"use strict";

var Template = require("./template");


/**
 * @param {object} def - Must be an expanded definition.
 */
module.exports = function( def ) {
  var output = '';
  var structure = def.structure;
  if( typeof def.structure === 'undefined' ) structure = {};
  else structure = JSON.parse( JSON.stringify( def.structure ) );

  output += getCodeForTables( def );
  return output;
};


function getCodeForTables( def ) {
  var structure = def.structure;
  var output = '';
  var tableName, fields, fieldName, fieldType;

  try {
    for( tableName in structure ) {
      fields = structure[tableName];
      output += "DROP TABLE IF EXISTS `" + table(tableName) + "`;\n";
      output += "CREATE TABLE `" + table(tableName) + "` (\n";
      output += "  `id` INT(11) NOT NULL AUTO_INCREMENT";
      for( fieldName in fields ) {
        fieldType = fields[fieldName];
        output += ',\n  `' + fieldName + "` " + fieldType.sql;
        if( typeof fieldType.default !== 'undefined' ) {
          output += " DEFAULT '" + fieldType.default + "'";
        }
      }
      var links = def.links
          .filter( filterParentLink.bind( null, tableName ) )
          .map( mapSplitIntoSrcAndDst.bind( null, tableName ) );
      output += links.map( link => `,\n  \`${link.src.att}\` INT(11)` ).join('');
      output += links.map( mapParentLink.bind( null, tableName ) ).join('');
      fields = structure[tableName];
      for( fieldName in fields ) {
        fieldType = fields[ fieldName ];

        if( typeof fieldType.key === 'string' ) {
          switch( fieldType.key ) {
          case 'index':
            output += ",\n  KEY `" + fieldName + "` (`" + fieldName + "`)";
            break;
          case 'unique':
            output += ",\n  UNIQUE KEY `" + fieldName + "` (`" + fieldName + "`)";
            break;
          }
        }
      }
      output += ",\n  PRIMARY KEY (id)";
      output += "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n\n";
    }
    output += getSqlTail();
  }
  catch( ex ) {
    throw "Error in the structure:\n" + JSON.stringify( structure, null, '  ' ) + "\n" + ex;
  }

  return output;
}


/**
 * @param {object} link - `{ name, nodes: [{cls, att, min, max, hard}, {...}] }`.
 */
function filterParentLink( tableName, link ) {
  var src, dst;
  if( link.nodes[0].cls == tableName ) {
    src = link.nodes[0];
    dst = link.nodes[1];
  }
  else if( link.nodes[1].cls == tableName ) {
    src = link.nodes[1];
    dst = link.nodes[0];
  }
  else {
    return false;
  }

  return src.max === 1;
}


function mapSplitIntoSrcAndDst( tableName, link ) {
  var src, dst;
  if( link.nodes[0].cls == tableName )
    return { src: link.nodes[0], dst: link.nodes[1] };
  return { src: link.nodes[1], dst: link.nodes[0] };
}

function mapParentLink( tableName, link ) {
  var output = ",\n  FOREIGN KEY (`" + link.src.att + "`) REFERENCES `" + table(link.dst.cls) + "`(id)";
  if( link.dst.hard ) output += " ON DELETE CASCADE";
  return output;
}


function getSqlTail() {
  return Template.file( "tail.sql" ).out;
}


function table( tablename) {
  return "${PREFIX}" + tablename.charAt(0).toLowerCase() + tablename.substr(1);
}
