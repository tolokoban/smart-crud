"use strict";

var Fs = require("fs");
var Path = require("path");
var PermissiveJson = require("toloframework-permissive-json");


exports.loadText = loadText;
exports.loadJSON = loadJSON;
exports.saveText = saveText;
exports.saveJSON = saveJSON;




function loadText( filename ) {
  var fullPath = Path.join( __dirname, "..", "data", filename );
  if( !Fs.existsSync( fullPath ) ) {
    throw "This file does not exist: " + fullPath + "!";
  }
  return Fs.readFileSync( fullPath, { encoding: "utf8" } );
}


function loadJSON( filename ) {
  return PermissiveJson.parse( loadText( filename ) );
}


function saveText( filename, content ) {
  var fullPath = Path.join( __dirname, "..", "data", filename );
  Fs.writeFileSync( fullPath, content, { encoding: "utf8" } );
}


function saveJSON( filename, obj ) {
  saveText( filename, JSON.stringify( obj, null, '  ' ) );
}



