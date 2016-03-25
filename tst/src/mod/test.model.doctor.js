var Crud = require("smart-crud");


function Doctor( attribs ) {
    Crud.Model.call( this, attribs, ["name"] );
}

// Inheritance from Widget
Doctor.prototype = Object.create(Crud.Model.prototype);
Doctor.prototype.constructor = Doctor;



module.exports = Doctor;
