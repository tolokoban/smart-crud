"use strict";

var Util = require("./inc/util");
var ExpandDefinition = require("../src/expand-definition");


describe('Module "expand-definition"', function() {
  describe('expansion', function() {
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
    it('should parse links', check.bind( null, "links" ));
  });

  describe('should throw an exception because', function() {
    function check( input, message ) {
      it(message, function() {
        try {
          ExpandDefinition( input );
          fail( JSON.stringify( input, null, '  ' ) );
        }
        catch(ex) {}
      });
    }

    check({}, "because 'structure' is missing.");
    check({ structure: { table$: {} } }, "'table$' is not a valid identifier.");
    check({ structure: { '6table': {} } }, "'6table' is not a valid identifier.");
    check({ structure: { 'Table': {} } }, "'Table' is not a valid identifier.");
  });
});
