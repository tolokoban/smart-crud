var FS = require("fs");
var Path = require("path");



exports.mkdir = function() {
    var key, arg, items = [].slice.call( arguments );
    var path = Path.resolve( Path.normalize( items.join("/") ) ),
        item, i,
        curPath = "";
    items = path.replace( /\\/g, '/' ).split( "/" );
    for (i = 0 ; i < items.length ; i++) {
        item = items[i];
        curPath += item + "/";
        if( FS.existsSync( curPath ) ) {
            var stat = FS.statSync( curPath );
            if( !stat.isDirectory() ) {
                break;
            }
        } else {
            try {
                FS.mkdirSync(curPath);
            }
            catch (ex) {
                throw Error( "Unable to create directory \"" + curPath + "\"!\n" + ex );
            }
        }
    }
    return path;
};


exports.write = function( path, content ) {
    path = Path.resolve( Path.normalize( path ) );
    console.log( Path.basename( path ).bold + "  " 
                 + (content.length + " bytes").cyan
                 + "  " + Path.dirname( path ).gray );
    FS.writeFile( path, content );
};


exports.getLinkName = function( table1, table2 ) {
    if( table1 < table2 ) return table1 + "_" + table2;
    else return table2 + "_" + table1;
};

exports.isBaseType = function( type ) {
    if( Array.isArray( type ) ) return true;
    switch( type ) {
    case 'CHAR': return true;
    case 'TEXT': return true;
    case 'FILE': return true;
    case 'INT': return true;
    case 'FLOAT': return true;
    case 'DATE': return true;
    case 'DATETIME': return true;
    }
    return false;
};



exports.isScalarType = function( type ) {
    if( Array.isArray( type ) ) return true;
    if( type.type ) return true;
    return false;
};
