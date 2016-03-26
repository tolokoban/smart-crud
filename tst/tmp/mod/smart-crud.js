{"intl":"","src":"require( 'smart-crud', function(exports, module) {  \nfunction Model( values, scalars, sets ) {\n    Object.defineProperty( this, 'id', {\n        value: values.id,\n        writable: false,\n        enumerable: true\n    });\n\n    var listeners = [];\n\n    \n\n    scalars.forEach(function ( name ) {\n        Object.defineProperty( this, name, {\n            get: function() {\n                return values[name];\n            },\n            set: function(v) {\n                if( values[name] == v ) return;\n                values[name] = v;\n                \n            },\n            enumerable: true\n        });\n    });\n\n\n}\n\n\nexports.Model = Model;\n });\n","zip":"require(\"smart-crud\",function(e,t){function n(e,t,n){Object.defineProperty(this,\"id\",{value:e.id,writable:!1,enumerable:!0});t.forEach(function(t){Object.defineProperty(this,t,{get:function(){return e[t]},set:function(n){e[t]!=n&&(e[t]=n)},enumerable:!0})})}e.Model=n});\n//# sourceMappingURL=smart-crud.js.map","map":{"version":3,"file":"smart-crud.js.map","sources":["smart-crud.js"],"sourcesContent":["require( 'smart-crud', function(exports, module) {  \nfunction Model( values, scalars, sets ) {\n    Object.defineProperty( this, 'id', {\n        value: values.id,\n        writable: false,\n        enumerable: true\n    });\n\n    var listeners = [];\n\n    \n\n    scalars.forEach(function ( name ) {\n        Object.defineProperty( this, name, {\n            get: function() {\n                return values[name];\n            },\n            set: function(v) {\n                if( values[name] == v ) return;\n                values[name] = v;\n                \n            },\n            enumerable: true\n        });\n    });\n\n\n}\n\n\nexports.Model = Model;\n });\n"],"names":["require","exports","module","Model","values","scalars","sets","Object","defineProperty","this","value","id","writable","enumerable","forEach","name","get","set","v"],"mappings":"AAAAA,QAAS,aAAc,SAASC,EAASC,GACzC,QAASC,GAAOC,EAAQC,EAASC,GAC7BC,OAAOC,eAAgBC,KAAM,MACzBC,MAAON,EAAOO,GACdC,UAAU,EACVC,YAAY,GAOhBR,GAAQS,QAAQ,SAAWC,GACvBR,OAAOC,eAAgBC,KAAMM,GACzBC,IAAK,WACD,MAAOZ,GAAOW,IAElBE,IAAK,SAASC,GACNd,EAAOW,IAASG,IACpBd,EAAOW,GAAQG,IAGnBL,YAAY,MAQxBZ,EAAQE,MAAQA"},"dependencies":["mod/smart-crud"]}