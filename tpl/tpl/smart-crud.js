
function Model( values, scalars, sets ) {
    Object.defineProperty( this, 'id', {
        value: values.id,
        writable: false,
        enumerable: true
    });

    var listeners = [];

    scalars.forEach(function ( name ) {
        Object.defineProperty( this, name, {
            get: function() {
                return values[name];
            },
            set: function(v) {
                if( values[name] == v ) return;
                values[name] = v;
                
            },
            enumerable: true
        });
    }, this);
}


exports.Model = Model;
