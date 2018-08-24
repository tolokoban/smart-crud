"use strict";

var Util = require("./inc/util");
var MakeSql = require("../src/make-sql");
var ExpandDefinition = require("../src/expand-definition");


describe('Module make-sql', function() {
  function test(name) {
    var xjs = Util.loadJSON( "make-sql/" + name + ".xjs" );
    var definition = ExpandDefinition( xjs );
    var output = MakeSql( definition ).trim();
    var expected = Util.loadText( "make-sql/" + name + ".sql" ).trim();
    Util.saveText( "make-sql/" + name + ".got", output );
    if( output !== expected ) {
      fail("Difference found between make-sql/" + name + ".got and make-sql/" + name + ".sql!\n"
           + findDiff( expected, output ));
    }
  }

  it('should produce the table `user` even with an empty data', test.bind( null, "simple" ));
  it('should produce special table for composition', test.bind( null, "composition" ));
  it('should produce special table for cameroun', test.bind( null, "cameroun" ));
});


function findDiff( exp, got ) {
  var expLines = exp.split( "\n" );
  var gotLines = got.split( "\n" );
  var expLine;
  var gotLine;
  for( var k = 0; k < expLines.length; k++ ) {
    expLine = expLines[k];
    gotLine = gotLines[k];
    if( expLine === gotLine ) continue;
    return findDiffBetweenLines( k + 1, expLine, gotLine );
  }
  return "Expected " + expLines.length + " lines but got " + gotLines.length + "!";
}


function findDiffBetweenLines( lineNumber, expLine, gotLine ) {
  var output = "Difference found at line " + lineNumber + ":\n";
  var indent = '      ';
  for( var k = 0; k < expLine.length; k++ ) {
    if( expLine.charAt( k ) !== gotLine.charAt( k ) ) {
      return output
        + "exp>  " + expLine + "\n"
        + "got>  " + gotLine + "\n"
        + indent + "^\n\n";
    }
    indent += ' ';
  }
  return output
    + "exp>  " + expLine + "\n"
    + "got>  " + gotLine + "\n"
    + indent + "^\n\n";
}
