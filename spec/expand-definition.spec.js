"use strict";

var Util = require("./inc/util");
var ExpandDefinition = require("../src/expand-definition");


describe('Module "definition-parser"', function() {
  function check( filename ) {
    var input = Util.loadJSON( "expand-definition/" + filename + ".inp.xjs" );
    var expected = Util.loadJSON( "expand-definition/" + filename + ".exp.xjs" );
    var output = ExpandDefinition( input );
    Util.saveJSON( "expand-definition/" + filename + ".got.xjs", output );

    expect( output ).toEqual( expected );
  }

  it('should expand empty definition', check.bind( null, "empty" ));
  it('should extend user with a new field', check.bind( null, "structure" ));
  it('should camelcase names', check.bind( null, "camel" ));
});
