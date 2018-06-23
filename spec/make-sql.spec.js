"use strict";

var Util = require("./inc/util");
var MakeSql = require("../src/make-sql");
var ExpandDefinition = require("../src/expand-definition");


describe('Module make-sql', function() {
  function test(name) {
    var xjs = Util.loadJSON( "make-sql/" + name + ".xjs" );
    var definition = ExpandDefinition( xjs );
    console.log(JSON.stringify( definition, null, '  ' ));
    var output = MakeSql( definition ).trim();
    var expected = Util.loadText( "make-sql/" + name + ".sql" ).trim();
    Util.saveText( "make-sql/" + name + ".got", output );
    expect( output ).toEqual( expected );
  }

  it('should produce the table `user` even with an empty data', function() {
    test( "simple" );
  });
  it('should produce special table for composition', function() {
    debugger;
    test( "composition" );
  });
});
