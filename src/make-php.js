"use strict";

var Template = require("./template");


/**
 * @param {object} def - Must be an expanded definition.
 */
module.exports = function( def ) {
  var output = "<?php\n";
  output += buildUtilFunctions( def );
  for( var tableName in def.structure ) {
    output += buildTable( def, tableName );
  }

  return output + "?>";
};


function buildUtilFunctions( def ) {
  return Template.file( "persistence.php", { NAME: def.name } ).out;
}


function buildTable( def, tableName ) {
  var output = `namespace ${def.name}\\${tableName} {\n`;
  output += buildTableAll( def, tableName );
  output += buildTableGet( def, tableName );
  output += buildTableAdd( def, tableName );
  output += buildTableUpd( def, tableName );
  output += buildTableDel( def, tableName );
  output += buildTableLnk( def, tableName );
  return output + "}\n";
}


function buildTableAll( def, tableName ) {
  return "    function all() {\n"
    + "        global $DB;\n"
    + `        $stm = \\${def.name}\\query('SELECT id FROM' . $DB->table('${tableName}'));\n`
    + `        $ids = [];\n`
    + `        while( null != ($row = $stm->fetch()) ) {\n`
    + `            $ids[] = intVal($row[0]);\n`
    + `        }\n`
    + `        return $ids;\n`
    + `    }\n`;
}

function buildTableGet( def, tableName ) {
  var output = "    function get( $id ) {\n"
      + "        global $DB;\n"
      + `        $row = \\${def.name}\\fetch('SELECT * FROM' . $DB->table('${tableName}') . 'WHERE id=?', $id );\n`
      + "        return ['id' => intVal($row['id'])";
  var table = def.structure[tableName];
  for( var fieldName in table ) {
    output += `,\n                '${fieldName}' => $row['${fieldName}']`;
  }
  return output += "];\n    }\n";
}


function buildTableAdd( def, tableName ) {
  var fields = Object.keys( def.structure[tableName] );
  return "    function add( $fields ) {\n"
    + "        global $DB;\n"
    + `        return \\${def.name}\\exec(\n`
    + "            'INSERT INTO' . $DB->table('"
    + tableName + "') . '("
    + fields.map(f => "`" + f + "`").join(",")
    + ")'\n"
    + "          . 'VALUES("
    + fields.map(f => "?").join(",")
    + ")'"
    + fields.map(f => `,\n            $fields['${f}']`).join('')
    + ");\n    }\n";
}


function buildTableUpd( def, tableName ) {
  var fields = Object.keys( def.structure[tableName] );
  return "    function upd( $id, $values ) {\n"
    + "        global $DB;\n"
    + `        \\${def.name}\\exec(\n`
    + `            'UPDATE' . $DB->table('${tableName}')\n`
    + "          . 'SET "
    + fields.map((f, i) => (i > 0 ? ',' : '') + '`' + f + '`=?').join(",")
    + " '\n"
    + "          . 'WHERE id=?',\n"
    + "            $id"
    + fields.map(f => `,\n            $values['${f}']`).join('')
    + ");\n    }\n";
}


function buildTableDel( def, tableName ) {
  var replacer = { NAME: '\\' + def.name, TABLE: tableName };
  return Template.file( "persistence.del.php", replacer ).out;
}


function buildTableLnk( def, tableName ) {
  var output = '';
  getLinksSingle( def, tableName ).forEach(function (link) {
    output += `    function get${cap(link.src.att)}( $id ) {\n`
      + `        global $DB;\n`
      + `        $row = \\${def.name}\\fetch(\n`
      + `            'SELECT \`${link.src.att}\` FROM' . $DB->table('${tableName}')\n`
      + `          . 'WHERE id=?', $id);\n`
      + `        return intVal($row[0]);\n`
      + `    }\n`;
  });
  getLinksMultiple( def, tableName ).forEach(function (link) {
    output += `    function get${cap(link.src.att)}( $id ) {\n`
      + `        global $DB;\n`
      + `        $stm = \\${def.name}\\query(\n`
      + `            'SELECT id FROM' . $DB->table('${link.dst.cls}')\n`
      + `          . 'WHERE \`${link.dst.att}\`=?', $id);\n`
      + `        $ids = [];\n`
      + `        while( null != ($row = $stm->fetch()) ) {\n`
      + `            $ids[] = intVal($row[0]);\n`
      + `        }\n`
      + `        return $ids;\n`
      + `    }\n`;
  });

  return output;
}


function getLinksSingle( def, tableName ) {
  var links = [];
  def.links.forEach(function (link) {
    var src = link.nodes[0], dst = link.nodes[1];
    if( dst.cls == tableName ) {
      var tmp = dst;
      dst = src;
      src = tmp;
    }
    else if ( src.cls != tableName ) return;
    if( src.max !== 1 ) return;
    links.push({ src: src, dst: dst });
  });

  return links;
}


function getLinksMultiple( def, tableName ) {
  var links = [];
  def.links.forEach(function (link) {
    var src = link.nodes[0], dst = link.nodes[1];
    if( dst.cls == tableName ) {
      var tmp = dst;
      dst = src;
      src = tmp;
    }
    else if ( src.cls != tableName ) return;
    if( typeof( src.max ) !== 'undefined' ) return;
    links.push({ src: src, dst: dst });
  });

  return links;
}


function cap( text ) {
  return text.charAt(0).toUpperCase() + text.substr( 1 );
}
