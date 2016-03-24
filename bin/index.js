require("colors");
var Path = require("path");
var FS = require("fs");

var Utils = require("../src/utils");
var DefineTables = require("../src/define-tables");
var SqlCreateTables = require("../src/sql-create-tables");


var args = process.argv;
args.shift();
args.shift();

var crudConfigFilename = args.shift();
var dirname = Path.dirname( crudConfigFilename );
var config = JSON.parse( FS.readFileSync( crudConfigFilename ) );

Utils.mkdir( dirname, 'sql' );
Utils.mkdir( dirname, 'src', 'mod' );
Utils.mkdir( dirname, 'src', 'tfw', 'svc' );

var tables = DefineTables( config );

console.log( JSON.stringify( tables, null, '  ' ) );
console.log();

var query = SqlCreateTables( tables );
FS.writeFile( Path.join( dirname, 'sql', 'database.sql' ), query );

console.log(query);


