/**
 * Definition must be human readable.
 * But it's easier to normalize it for computer parsing.
 */
"use strict";

module.exports = function( definition ) {
  expandStructure( definition );
  return definition;
};


function expandStructure( definition ) {
  var table, fields;
  var name, type, prefix;
  for( table in definition.structure ) {
    fields = definition.structure[table];
    for( name in fields ) {
      type = fields[name];
      if( Array.isArray( type ) || typeof type === 'string' ) {
        type = { type: type };
        fields[name] = type;
      }
      
      type.occur = getOccurence( type.type );
      if( type.occur ) {
        // Remove last char because it was used to define occurence.
        type.type = type.type.substr(0, type.type.length - 1 );
      }
      
      prefix = name.charAt( 0 );
      if( '@!'.indexOf( prefix ) > -1 ) {
        // Composition.
        type.link = prefix === '!' ? 'strong' : 'weak';
        fields[name.substr( 1 )] = type;
        delete fields[name];
      }
    }
  }
}


function getOccurence( type ) {
  var lastChar = type.charAt( type.length - 1 );
  if( "?+*".indexOf( lastChar ) > -1 ) return lastChar;
  return undefined;
}
