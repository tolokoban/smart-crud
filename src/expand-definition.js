"use strict";

/**
 * The definition aim's to be human readable.
 * This function return a more usable and safe version for computers.
 */
module.exports = function( def ) {
  try {
    var output = {
      // To use for temporary variables.
      $: {
        linksNames: {}
      }
    };
    translateName( output, def );
    translateStructure( output, def );
    translateLinks( output, def );

    // Remove temporary variables.
    delete output.$;
    return output;
  }
  catch( ex ) {
    throw ex + "\n\nHere is the complete definition:\n" + JS( def, null, '  ' );
  }
};


/**
 *
 */
function translateLinks( output, def ) {
  if( typeof def.links === 'undefined' ) def.links = [];
  var links = def.links;
  if( !Array.isArray( links ) )
    throw "`links` must be an array, but we got: " + JS( links ) + "!";
  output.links = [];
  links.forEach(function (linkDef) {
    output.links.push( parseLink( linkDef, output.structure, output.$ ) );
  });
}


function parseLink( linkDef, structure, tempVars ) {
  try {
    if( typeof linkDef !== 'string' )
      throw "A link definition must be a string!\n"
      + "But we got: " + JS( linkDef );
    var pieces = linkDef.split("|");
    if( pieces.length != 2 )
      throw "There can be one and only one '|' in a link definition!";

    var nodes = [
      parseLinkPart( pieces[0].trim(), structure ),
      parseLinkPart( pieces[1].trim(), structure )
    ];
    var name = makeLinkName( nodes[0].cls, nodes[1].cls, tempVars );
    return { name: name, nodes: nodes };
  }
  catch( ex ) {
    throw ex + "\nWe were expecting something like: \"!group.students*|student.group\".";
  }
}


function makeLinkName( name1, name2, tempVars ) {
  var name = name1 < name2 ? name1 + "_" + name2 : name2 + "_" + name1;
  var index = tempVars.linksNames[name];
  if( typeof index === 'undefined' ) {
    tempVars.linksNames[name] = 2;
    return name;
  }

  tempVars.linksNames[name]++;
  return name + "_" + index;
}


/**
 * @return
 * {
 *   cls: "group",
 *   att: "students",
 *   min: 1,
 *   max: 1,
 *   hard: true
 * }
 */
function parseLinkPart( item, structure ) {
  try {
    var parts = item.split(".");
    if( parts.length !== 2 )
      throw "A single dot must be put between the class name and the attribute name.";
    var link = {};
    var className = parts[0];
    var attribName = parts[1];

    lookForHardLink( className, link );
    lookForOccurences( attribName, link );
    checkIfClassExist( link.cls, structure );

    return link;
  }
  catch( ex ) {
    throw "Unable to parse the folowing node of a link definition: " + JSON.stringify( item ) + "!\n" + ex;
  }
}


/**
 * If `className` has a leading `!`, link.hard = true, link.cls = camelCap( className.substr(1) )
 * else link.cls = camelCap( className )
 */
function lookForHardLink( className, link ) {
  if( className.charAt(0) == '!' ) {
    link.hard = true;
    link.cls = camelCap( className.substr(1) );
  }
  else {
    link.cls = camelCap( className );
  }
}


var OCCURENCES = {
  '?': { min: 0, max: 1 },
  '+': { min: 1 },
  '*': { min: 0 },
  'default': { min: 1, max: 1 }
}
function lookForOccurences( attName, link ) {
  var lastCharIndex = attName.length - 1;
  var lastChar = attName.charAt( lastCharIndex );

  var occurences = OCCURENCES[lastChar];
  if( typeof occurences === 'undefined' ) {
    occurences = OCCURENCES.default;
    link.occ = '1';
  }
  else {
    // Remove the trailing char because it was only used to specify occurences.
    attName = attName.substr( 0, lastCharIndex );
    link.occ = lastChar;
  }
  link.att = camel( attName );
  link.min = occurences.min;
  if( typeof occurences.max !== 'undefined' ) link.max = occurences.max;
}


/**
 *
 */
function checkIfClassExist( className, structure ) {
  if( typeof structure[className] === 'undefined' )
    throw "This class does not exist: " + JS( className ) + "!\n"
    + "Available classes are: " + JS( structure.keys() ) + ".";
}

/**
 * A name can be provided which will be used as prefix for the generated code.
 * If not provided, it will be set to "data".
 */
function translateName( output, def ) {
  if( typeof def.name === 'undefined' ) def.name = 'data';
  if( typeof def.name !== 'string' )
    throw "The attribute `name` must be a string, but we got: " + JS( def.name ) + "!";
  output.name = camelCap( def.name );
}


/**
 *
 */
function translateStructure( output, def ) {
  if( typeof def.structure === 'undefined' )
    throw "Missing mandatory attribute `structure`!";
  if( typeof def.structure !== 'object' || Array.isArray( def.structure ) )
    throw "Attribute `structure` must be an object!";

  var structure = prepareStructureUser( clone( def.structure ) );
  output.structure = {};
  var key, val;
  for( key in structure ) {
    val = structure[key];
    translateStructureClass( output, key, val );
  }
}

/**
 * @param {object} attribs - `{ name: "string256", personal-code: { type: "string64", key: "unique" } }`
 */
function translateStructureClass( output, className, attribs ) {
  try {
    var camelClassName = camelCap(className);
    output.structure[camelClassName] = {};
    var attName, attValue;
    for( attName in attribs ) {
      try {
        attValue = clone( attribs[attName] );
        if( typeof attValue === 'string' ) {
          attValue = { type: attValue };
        }
        if( typeof attValue !== 'object' || Array.isArray( attValue ) )
          throw "Attrib's value must be a string or an object, but we got: " + JS( attValue ) + "!";

        checkIfThereIsAnInvalidAttribute( attValue );
        attValue.sql = getSqlType( attValue.type );
        output.structure[camelClassName][camel(attName)] = attValue;
      }
      catch( ex ) {
        throw "Error while parsing attribute " + JS( attName ) + "!\n" + ex;
      }
    }
  }
  catch( ex ) {
    throw "Unable to parse structure's classname: " + JS( className ) + "!\n" + ex;
  }
}


var VALID_KEYS_FOR_CLASS_ATTRIBUTES = ["type", "key", "default"];
var VALID_FIELD_KEYS = ["unique", "index"];

function checkIfThereIsAnInvalidAttribute( value ) {
  var key, val;
  for( key in value ) {
    if( VALID_KEYS_FOR_CLASS_ATTRIBUTES.indexOf( key ) === -1 )
      throw JS( key ) + " is not a valid attribute!\n"
      + "Valid attributes are: " + JS(VALID_KEYS_FOR_CLASS_ATTRIBUTES) + ".\n"
      + JS( value );
    val = value[key];
    if( key === 'default' && typeof val !== 'string' )
      throw "Attribute " + JS( key ) + " must be a string!\n"
      + " We got: " + JS( val );
  }

  var keyType = value.key;
  if( typeof keyType !== 'undefined' ) {
    if( VALID_FIELD_KEYS.indexOf( keyType ) === -1 )
      throw "The \"key\" attribute must have a value in " + JS( VALID_FIELD_KEYS ) + "!\n"
      + "We got: " + JS( keyType ) + ".";
  }
}

var USER_DEFAULT = {
  login: { type: "string256", key: "unique" },
  password: { type: "string256" },
  name: { type: "string256", key: "unique" },
  roles: { type: "string512", default: "[]" },
  enabled: { type: "boolean" },
  creation: { type: "datetime" },
  data: { type: "string" }
};

function prepareStructureUser( structure ) {
  if( typeof structure.user === 'undefined' ) structure.user = {};
  var user = structure.user;
  var key, val;
  for( key in USER_DEFAULT ) {
    if( typeof user[key] !== 'undefined' )
      throw "You must not override attribute " + JS( key ) + " of classe \"user\"!";
    val = clone( USER_DEFAULT[key] );
    user[key] = val;
  }
  return structure;
}

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
 * This is the SQL type.
 */
function getSqlType( type ) {
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

  var cleanType = ("" + type).trim().toLowerCase();
  var mappedType = TYPES_MAPPING[cleanType];
  if( mappedType ) return mappedType;

  if( cleanType.substr(0, 6) === 'string' ) {
    return "VARCHAR(" + cleanType.substr(6) + ")";
  }
  throw "Don't know how to convert " + JS( type ) + " into an SQL type!\n"
    + "Allowed types are " + JS( TYPES_MAPPING.keys() ) + "\n"
    + "and \"string\" with a number behind. For example \"string64\", \"string256\", ...";
}


function capitalize( text ) {
  if( typeof text !== 'string' ) return text;
  if( text.length === 0 ) return text;
  return text.charAt(0).toUpperCase() + text.substr( 1 );
}


function camel( name ) {
  var c;
  for( var k = 0; k < name.length; k++ ) {
    c = name.charAt( k );
    if( c >= "a" && c <= "z" ) continue;
    if( k > 0 && c >= "0" && c <= "9" ) continue;
    if( c == '-' || c == '_' ) continue;
    throw JS( name ) + " is not a valid identifier because of the unexpected char "
      + JS( c ) + "!";
  }

  return name.split('.').map(function( word, wordIdx ) {
    return word.split('-').map(function( piece, pieceIdx ) {
      if( wordIdx + pieceIdx === 0 ) return piece.toLowerCase();
      return capitalize( piece );
    }).join("");
  }).join("");
}


function camelCap( name ) {
  return capitalize( camel( name ) );
}


function clone( original ) {
  return JSON.parse( JS( original ) );
}


function JS( obj ) {
  return JSON.stringify( obj );
}


function JSPretty( obj ) {
  return JSON.stringify( obj, null, '  ' );
}


function isSpecial( obj, expectedName ) {
  var type = typeof obj;
  if( type === 'string' || type !== 'object' || Array.isArray(obj) ) return false;
  if( !obj ) return false;
  var name = obj[0];

  if( typeof name !== 'string' ) return false;
  if( typeof expectedName === 'string' ) {
    return name.toLowerCase() === expectedName.toLowerCase();
  }
  return true;
}
