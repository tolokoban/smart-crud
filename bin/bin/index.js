require("colors");
var Path = require("path");
var FS = require("fs");

var Utils = require("../src/utils");
var Template = require("../src/template");
var ConfigParser = require("../src/config-parser");
var DefineTables = require("../src/define-tables");
var SqlCreateTables = require("../src/sql-create-tables");
var PhpCreateServices = require("../src/php-create-services");


var config = ConfigParser();


var dirname = config.$dirname;
Utils.mkdir( dirname, 'sql' );
Utils.mkdir( dirname, 'src', 'mod' );
if( ! FS.existsSync( Path.join( dirname, 'src', 'tfw' ) ) ) {
    Template.files( 'tfw', Path.join( dirname, 'src', 'tfw' ), { PREFIX: config.prefix } );
    Utils.write( Path.join( dirname, 'src', 'tfw', 'svc', '.htaccess' ), 'deny from all\n' );
}
Utils.mkdir( dirname, 'src', 'tfw', 'svc' );

var tables = DefineTables( config );
var query = SqlCreateTables( tables, config );
Utils.write( Path.join( dirname, 'sql', 'database.sql' ), query );

PhpCreateServices( config );

var files = {
    "index.html": "index.html",
    "main.js": "mod/" + config.prefix + ".js",
    "main.css": "mod/" + config.prefix + ".css",
    "page-login.htm": "page-login.htm",
    "page-login-lost.htm": "page-login-lost.htm",
    "main.page-login.js": "mod/" + config.prefix + ".page-login.js"
};

var src, dst;
for( src in files ) {
    dst = files[src];
    Utils.write(
        Path.join( dirname, 'src', dst ),
        Template.file( src, {
            NAME: config.prefix,
            TITLE:  config.title
        } ).out
    );
}
