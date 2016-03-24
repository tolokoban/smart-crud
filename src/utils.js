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
