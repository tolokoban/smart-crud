var Crud = require("smart-crud");


function {{NAME}}( attribs ) {
    Crud.Model.call( this, attribs, {{FIELDS}} );
}

// Inheritance from Widget
{{NAME}}.prototype = Object.create(Crud.Model.prototype);
{{NAME}}.prototype.constructor = {{NAME}};



module.exports = {{NAME}};
