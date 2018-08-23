"use strict";

var Util = require("./inc/util");
var MakePhp = require("../src/make-php");
var ExpandDefinition = require("../src/expand-definition");


describe('Module make-php', function() {
  function test(name) {
    var xjs = Util.loadJSON( "make-php/" + name + ".xjs" );
    var definition = ExpandDefinition( xjs );
    var output = MakePhp( definition ).trim();
    var expected = Util.loadText( "make-php/" + name + ".exp.php" ).trim();
    Util.saveText( "make-php/" + name + ".got.php", output );
    if( output !== expected ) {
      fail("Difference found between make-php/" + name + ".got.php and make-php/" + name + ".exp.php!\n"
           + findDiff( expected, output ));
    }
  }

  it('should produce PHP code for Group/Student', test.bind( null, "group-student" ));
  it('should produce PHP code for real example Cameroun', test.bind( null, "cameroun" ));
});


function findDiff( exp, got ) {
  var expLines = exp.split( "\n" );
  var gotLines = got.split( "\n" );
  var expLine;
  var gotLine;
  for( var k = 0; k < expLines.length; k++ ) {
    expLine = expLines[k].trim();
    gotLine = gotLines[k].trim();
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
