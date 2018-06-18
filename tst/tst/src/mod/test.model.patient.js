var Crud = require("smart-crud");


function Patient( attribs ) {
    Crud.Model.call( this, attribs, ["name","age","gender"] );
}

// Inheritance from Widget
Patient.prototype = Object.create(Crud.Model.prototype);
Patient.prototype.constructor = Patient;



module.exports = Patient;
