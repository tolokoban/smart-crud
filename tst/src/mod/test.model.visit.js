var Crud = require("smart-crud");


function Visit( attribs ) {
    Crud.Model.call( this, attribs, ["patient","doctor","date","status"] );
}

// Inheritance from Widget
Visit.prototype = Object.create(Crud.Model.prototype);
Visit.prototype.constructor = Visit;



module.exports = Visit;
