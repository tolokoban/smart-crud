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
  output += buildTableName( def, tableName );
  output += buildTableAll( def, tableName );
  output += buildTableGet( def, tableName );
  output += buildTableAdd( def, tableName );
  output += buildTableUpd( def, tableName );
  output += buildTableDel( def, tableName );
  output += buildTableLnk( def, tableName );
  return output + "}\n";
}


function buildTableName( def, tableName ) {
  return "    function name() {\n"
    + "        global $DB;\n"
    + `        return ${table(tableName)};\n`
    + `    }\n`;
}

function buildTableAll( def, tableName ) {
  return "    function all() {\n"
    + `        $stm = \\${def.name}\\query('SELECT id FROM' . \\${def.name}\\${tableName}\\name());\n`
    + `        $ids = [];\n`
    + `        while( null != ($row = $stm->fetch()) ) {\n`
    + `            $ids[] = intVal($row[0]);\n`
    + `        }\n`
    + `        return $ids;\n`
    + `    }\n`;
}

function buildTableGet( def, tableName ) {
  var output = "    function get( $id ) {\n"
      + `        $row = \\${def.name}\\fetch('SELECT * FROM' . \\${def.name}\\${tableName}\\name() . 'WHERE id=?', $id );\n`
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
    + `        return \\${def.name}\\exec(\n`
    + `            'INSERT INTO' . \\${def.name}\\${tableName}\\name() . '(`
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
  var allowedFields = fields.map( f => "'" + f + "'" ).join(",");
  return `    function upd( $id, $values ) {
        try {
            $args = [null];
            $sets = [];
            $fields = [${allowedFields}];
            foreach( $values as $key => $val ) {
                if( !in_array( $key, $fields ) )
                    throw new \\Exception("[\\\\${def.name}\\\\${tableName}\\\\upd()] Unknown field: $key!");
                $sets[] = "\`$key\`=?";
                $args[] = $val;
            }
            $args[0] = 'UPDATE' . \\${def.name}\\${tableName}\\name() . 'SET '
                     . implode(',', $sets) . ' WHERE id=?';
            $args[] = $id;
            \call_user_func_array( "\\Data\\query", $args );
        }
        catch( \\Exception $e ) {
            error_log("Exception in \\\\${def.name}\\\\${tableName}\\\\upd( $id, values )!");
            error_log("   error:  " . $e->getMessage());
            error_log("   values: " . json_encode( $values ));
            throw $e;
        }
    }
`;
}


function buildTableDel( def, tableName ) {
  var replacer = { NAME: '\\' + def.name, TABLE: `\\${def.name}\\${tableName}\\name()` };
  return Template.file( "persistence.del.php", replacer ).out;
}


function buildTableLnk( def, tableName ) {
  var output = '';
  var links = getLinksOfThisTable( def, tableName );
  getLinksSingle( links ).forEach(function (link) {
    output += `    function get${cap(link.src.att)}( $id ) {\n`
      + `        $row = \\${def.name}\\fetch(\n`
      + `            'SELECT \`${link.src.att}\` FROM' . \\${def.name}\\${tableName}\\name()\n`
      + `          . 'WHERE id=?', $id);\n`
      + `        return intVal($row[0]);\n`
      + `    }\n`;
  });
  getLinksMultiple( links ).forEach(function (link) {
    output += `    function get${cap(link.src.att)}( $id ) {\n`
      + `        $stm = \\${def.name}\\query(\n`
      + `            'SELECT id FROM' . \\${def.name}\\${link.dst.cls}\\name()\n`
      + `          . 'WHERE \`${link.dst.att}\`=?', $id);\n`
      + `        $ids = [];\n`
      + `        while( null != ($row = $stm->fetch()) ) {\n`
      + `            $ids[] = intVal($row[0]);\n`
      + `        }\n`
      + `        return $ids;\n`
      + `    }\n`;
    output += `    function link${cap(link.src.att)}( $id${link.src.cls}, $id${link.dst.cls} ) {\n`
      + `        \\${def.name}\\query(\n`
      + `            'UPDATE' . \\${def.name}\\${link.dst.cls}\\name()\n`
      + `          . 'SET \`${link.dst.att}\`=? '\n`
      + `          . 'WHERE id=?', $id${link.src.cls}, $id${link.dst.cls});\n`
      + `    }\n`;
  });
  getLinksManyToMany( links ).forEach(function (link) {
    output += `    function get${cap(link.src.att)}( $id ) {\n`
      + `        global $DB;\n`
      + `        $stm = \\${def.name}\\query(\n`
      + `            'SELECT \`${link.dst.cls}\` FROM' . $DB->table('${link.name}')\n`
      + `          . 'WHERE \`${link.src.cls}\`=?', $id);\n`
      + `        $ids = [];\n`
      + `        while( null != ($row = $stm->fetch()) ) {\n`
      + `            $ids[] = intVal($row[0]);\n`
      + `        }\n`
      + `        return $ids;\n`
      + `    }\n`;
    output += `    function link${cap(link.src.att)}( $id, $id${link.dst.cls} ) {\n`
      + `        global $DB;\n`
      + `        \\${def.name}\\query(\n`
      + `            'INSERT INTO' . $DB->table('${link.name}')\n`
      + `          . '(\`${link.src.cls}\`, \`${link.dst.cls}\`)'\n`
      + `          . 'VALUES(?,?)', $id, $id${link.dst.cls});\n`
      + `    }\n`;
    output += `    function unlink${cap(link.src.att)}( $id, $id${link.dst.cls}=null ) {\n`
      + `        global $DB;\n`
      + `        if( $id${link.dst.cls} == null ) {\n`
      + `          \\${def.name}\\query(\n`
      + `              'DELETE FROM' . $DB->table('${link.name}')\n`
      + `            . 'WHERE \`${link.src.cls}\`=?', $id);\n`
      + `        }\n`
      + `        else {\n`
      + `          \\${def.name}\\query(\n`
      + `              'DELETE FROM' . $DB->table('${link.name}')\n`
      + `            . 'WHERE \`${link.src.cls}\`=? AND \`${link.dst.cls}\`=?', $id, $id${link.dst.cls});\n`
      + `        }\n`
      + `    }\n`;
  });
  return output;
}


/**
 * Return a list of links for this table (`tableName`).
 * We ensure that in resulting links we always have
 *   nodes[0].cls === tableName.
 */
function getLinksOfThisTable( def, tableName ) {
  return def.links.map( link => {
    if( link.nodes[0].cls === tableName ) return link;
    if( link.nodes[1].cls === tableName ) return {
      name: link.name,
      nodes: [link.nodes[1], link.nodes[0]]
    };
    return null;
  }).filter( link => link != null );
}

function getLinksSingle( tableLinks ) {
  var links = [];
  tableLinks.forEach(function (link) {
    var src = link.nodes[0], dst = link.nodes[1];
    if( src.max !== 1 ) return;
    links.push({ src: src, dst: dst });
  });

  return links;
}


function getLinksMultiple( tableLinks ) {
  var links = [];
  tableLinks.forEach(function (link) {
    var src = link.nodes[0], dst = link.nodes[1];
    if( typeof( src.max ) !== 'undefined' ) return;
    if( dst.max !== 1 ) return;
    links.push({ src: src, dst: dst });
  });

  return links;
}


function getLinksManyToMany( tableLinks ) {
  var links = [];
  tableLinks.forEach(function (link) {
    var src = link.nodes[0], dst = link.nodes[1];
    if( typeof( src.max ) !== 'undefined' ) return;
    if( typeof( dst.max ) !== 'undefined' ) return;
    links.push({ src: src, dst: dst, name: link.name });
  });

  return links;
}


function cap( text ) {
  return text.charAt(0).toUpperCase() + text.substr( 1 );
}

function table( tablename) {
  return "$DB->table('" + tablename.charAt(0).toLowerCase() + tablename.substr(1) + "')";
}
