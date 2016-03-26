{"intl":"","src":"require( 'test.model.issue', function(exports, module) {  var WS = require(\"tfw.web-service\");\nvar Crud = require(\"smart-crud\");\n\n\nfunction Issue( attribs ) {\n    Crud.Model.call( this, attribs, [\"title\",\"content\",\"date\",\"status\",\"type\"], [] );\n}\n\n// Inheritance from Widget\nIssue.prototype = Object.create(Crud.Model.prototype);\nIssue.prototype.constructor = Issue;\n\n\n\nmodule.exports.create = function( obj ) {\n    return WS.get( 'test.Issue.create' );\n};\n\nmodule.exports.request = function( criteria ) {\n    if( typeof criteria === 'undefined' ) criteria = {};\n    return WS.get( 'test.Issue.request', criteria );\n};\n\nmodule.exports.update = function( obj ) {\n    return WS.get( 'test.Issue.update' );\n};\n\nmodule.exports.delete = function( id ) {\n    return WS.get( 'test.Issue.delete' );\n};\n });\n","zip":"require(\"test.model.issue\",function(e,t){function r(e){u.Model.call(this,e,[\"title\",\"content\",\"date\",\"status\",\"type\"],[])}var s=require(\"tfw.web-service\"),u=require(\"smart-crud\");r.prototype=Object.create(u.Model.prototype),r.prototype.constructor=r,t.exports.create=function(e){return s.get(\"test.Issue.create\")},t.exports.request=function(e){return\"undefined\"==typeof e&&(e={}),s.get(\"test.Issue.request\",e)},t.exports.update=function(e){return s.get(\"test.Issue.update\")},t.exports[\"delete\"]=function(e){return s.get(\"test.Issue.delete\")}});\n//# sourceMappingURL=test.model.issue.js.map","map":{"version":3,"file":"test.model.issue.js.map","sources":["test.model.issue.js"],"sourcesContent":["require( 'test.model.issue', function(exports, module) {  var WS = require(\"tfw.web-service\");\nvar Crud = require(\"smart-crud\");\n\n\nfunction Issue( attribs ) {\n    Crud.Model.call( this, attribs, [\"title\",\"content\",\"date\",\"status\",\"type\"], [] );\n}\n\n// Inheritance from Widget\nIssue.prototype = Object.create(Crud.Model.prototype);\nIssue.prototype.constructor = Issue;\n\n\n\nmodule.exports.create = function( obj ) {\n    return WS.get( 'test.Issue.create' );\n};\n\nmodule.exports.request = function( criteria ) {\n    if( typeof criteria === 'undefined' ) criteria = {};\n    return WS.get( 'test.Issue.request', criteria );\n};\n\nmodule.exports.update = function( obj ) {\n    return WS.get( 'test.Issue.update' );\n};\n\nmodule.exports.delete = function( id ) {\n    return WS.get( 'test.Issue.delete' );\n};\n });\n"],"names":["require","exports","module","Issue","attribs","Crud","Model","call","this","WS","prototype","Object","create","constructor","obj","get","request","criteria","update","id"],"mappings":"AAAAA,QAAS,mBAAoB,SAASC,EAASC,GAI/C,QAASC,GAAOC,GACZC,EAAKC,MAAMC,KAAMC,KAAMJ,GAAU,QAAQ,UAAU,OAAO,SAAS,YALb,GAAIK,GAAKT,QAAQ,mBACvEK,EAAOL,QAAQ,aAQnBG,GAAMO,UAAYC,OAAOC,OAAOP,EAAKC,MAAMI,WAC3CP,EAAMO,UAAUG,YAAcV,EAI9BD,EAAOD,QAAQW,OAAS,SAAUE,GAC9B,MAAOL,GAAGM,IAAK,sBAGnBb,EAAOD,QAAQe,QAAU,SAAUC,GAE/B,MADwB,mBAAbA,KAA2BA,MAC/BR,EAAGM,IAAK,qBAAsBE,IAGzCf,EAAOD,QAAQiB,OAAS,SAAUJ,GAC9B,MAAOL,GAAGM,IAAK,sBAGnBb,EAAOD,QAAPC,UAAwB,SAAUiB,GAC9B,MAAOV,GAAGM,IAAK"},"dependencies":["mod/test.model.issue","mod/tfw.web-service","mod/smart-crud"]}