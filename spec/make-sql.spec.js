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
      fail("Difference found between make-sql/" + name + ".got and make-sql/" + name + ".sql!");
    }
  }

  //it('should produce the table `user` even with an empty data', test.bind( null, "simple" ));
  //it('should produce special table for composition', test.bind( null, "composition" ));
});
