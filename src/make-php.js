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
  output += buildTableGet( def, tableName );
  output += buildTableAdd( def, tableName );
  output += buildTableUpd( def, tableName );
  output += buildTableDel( def, tableName );
  return output + "}\n";
}


function buildTableGet( def, tableName ) {
  var output = "    function get( $id ) {\n"
      + "        global $DB;\n"
      + `        $stm = \\${def.name}\\fetch('SELECT * FROM' . $DB->table('${tableName}') . 'WHERE id=?', $id );\n`
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
  console.info("[make-php] replacer=", replacer);
  return Template.file( "persistence.del.php", replacer ).out;
}
