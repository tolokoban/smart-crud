require("colors");
var Path = require("path");
var FS = require("fs");

var Utils = require("../src/utils");
var ConfigParser = require("../src/config-parser");
var DefineTables = require("../src/define-tables");
var SqlCreateTables = require("../src/sql-create-tables");
var PhpCreateServices = require("../src/php-create-services");


var config = ConfigParser();

console.log(JSON.stringify( config, null, '  '));


/*
var dirname = config.$dirname;
Utils.mkdir( dirname, 'sql' );
Utils.mkdir( dirname, 'src', 'mod' );
Utils.mkdir( dirname, 'src', 'tfw', 'svc' );

var tables = DefineTables( config );

var query = SqlCreateTables( tables );
Utils.write( Path.join( dirname, 'sql', 'database.sql' ), query );

PhpCreateServices( config );

*/
