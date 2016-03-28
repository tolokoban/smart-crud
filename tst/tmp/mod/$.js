{"intl":"","src":"require( '$', function(exports, module) {  exports.config={\n    name:\"test\",\n    description:\"Test project for smart-crud\",\n    author:\"Tolokoban\",\n    version:\"1\",\n    major:1,\n    minor:undefined,\n    revision:undefined,\n    date:new Date(2016,2,26,17,33,31)\n};\nvar currentLang = null;\nexports.lang = function(lang) {\n    if (lang === undefined) {\n        lang = window.localStorage.getItem(\"Language\");\n        if (!lang) {\n            lang = window.navigator.language;\n            if (!lang) {\n                lang = window.navigator.browserLanguage;\n                if (!lang) {\n                    lang = \"fr\";\n                }\n            }\n        }\n        lang = lang.substr(0, 2).toLowerCase();\n    }\n    currentLang = lang;\n    window.localStorage.setItem(\"Language\", lang);\n    return lang;\n};\nexports.intl = function(words, params) {\n    var dic = words[exports.lang()],\n    k = params[0],\n    txt, newTxt, i, c, lastIdx, pos;\n    if (!dic) {\n        //console.error(\"Missing internationalization for language : \\\"\" + exports.lang() + \"\\\"!\");\n        return k;\n    }\n    txt = dic[k];\n    if (!txt) {\n        //console.error(\"Missing internationalization [\" + exports.lang() + \"]: \\\"\" + k + \"\\\"!\");\n        return k;\n    }\n    if (params.length > 1) {\n        newTxt = \"\";\n        lastIdx = 0;\n        for (i = 0 ; i < txt.length ; i++) {\n            c = txt.charAt(i);\n            if (c === '$') {\n                newTxt += txt.substring(lastIdx, i);\n                i++;\n                pos = txt.charCodeAt(i) - 48;\n                if (pos < 0 || pos >= params.length) {\n                    newTxt += \"$\" + txt.charAt(i);\n                } else {\n                    newTxt += params[pos];\n                }\n                lastIdx = i + 1;\n            } else if (c === '\\\\') {\n                newTxt += txt.substring(lastIdx, i);\n                i++;\n                newTxt += txt.charAt(i);\n                lastIdx = i + 1;\n            }\n        }\n        newTxt += txt.substr(lastIdx);\n        txt = newTxt;\n    }\n    return txt;\n};\n });\n","zip":"require(\"$\",function(r,n){r.config={name:\"test\",description:\"Test project for smart-crud\",author:\"Tolokoban\",version:\"1\",major:1,minor:void 0,revision:void 0,date:new Date(2016,2,26,17,33,31)};var t=null;r.lang=function(r){return void 0===r&&(r=window.localStorage.getItem(\"Language\"),r||(r=window.navigator.language,r||(r=window.navigator.browserLanguage,r||(r=\"fr\"))),r=r.substr(0,2).toLowerCase()),t=r,window.localStorage.setItem(\"Language\",r),r},r.intl=function(n,t){var e,o,a,i,g,u,s=n[r.lang()],l=t[0];if(!s)return l;if(e=s[l],!e)return l;if(t.length>1){for(o=\"\",g=0,a=0;a<e.length;a++)i=e.charAt(a),\"$\"===i?(o+=e.substring(g,a),a++,u=e.charCodeAt(a)-48,o+=0>u||u>=t.length?\"$\"+e.charAt(a):t[u],g=a+1):\"\\\\\"===i&&(o+=e.substring(g,a),a++,o+=e.charAt(a),g=a+1);o+=e.substr(g),e=o}return e}});\n//# sourceMappingURL=$.js.map","map":{"version":3,"file":"$.js.map","sources":["$.js"],"sourcesContent":["require( '$', function(exports, module) {  exports.config={\n    name:\"test\",\n    description:\"Test project for smart-crud\",\n    author:\"Tolokoban\",\n    version:\"1\",\n    major:1,\n    minor:undefined,\n    revision:undefined,\n    date:new Date(2016,2,26,17,33,31)\n};\nvar currentLang = null;\nexports.lang = function(lang) {\n    if (lang === undefined) {\n        lang = window.localStorage.getItem(\"Language\");\n        if (!lang) {\n            lang = window.navigator.language;\n            if (!lang) {\n                lang = window.navigator.browserLanguage;\n                if (!lang) {\n                    lang = \"fr\";\n                }\n            }\n        }\n        lang = lang.substr(0, 2).toLowerCase();\n    }\n    currentLang = lang;\n    window.localStorage.setItem(\"Language\", lang);\n    return lang;\n};\nexports.intl = function(words, params) {\n    var dic = words[exports.lang()],\n    k = params[0],\n    txt, newTxt, i, c, lastIdx, pos;\n    if (!dic) {\n        //console.error(\"Missing internationalization for language : \\\"\" + exports.lang() + \"\\\"!\");\n        return k;\n    }\n    txt = dic[k];\n    if (!txt) {\n        //console.error(\"Missing internationalization [\" + exports.lang() + \"]: \\\"\" + k + \"\\\"!\");\n        return k;\n    }\n    if (params.length > 1) {\n        newTxt = \"\";\n        lastIdx = 0;\n        for (i = 0 ; i < txt.length ; i++) {\n            c = txt.charAt(i);\n            if (c === '$') {\n                newTxt += txt.substring(lastIdx, i);\n                i++;\n                pos = txt.charCodeAt(i) - 48;\n                if (pos < 0 || pos >= params.length) {\n                    newTxt += \"$\" + txt.charAt(i);\n                } else {\n                    newTxt += params[pos];\n                }\n                lastIdx = i + 1;\n            } else if (c === '\\\\') {\n                newTxt += txt.substring(lastIdx, i);\n                i++;\n                newTxt += txt.charAt(i);\n                lastIdx = i + 1;\n            }\n        }\n        newTxt += txt.substr(lastIdx);\n        txt = newTxt;\n    }\n    return txt;\n};\n });\n"],"names":["require","exports","module","config","name","description","author","version","major","minor","undefined","revision","date","Date","currentLang","lang","window","localStorage","getItem","navigator","language","browserLanguage","substr","toLowerCase","setItem","intl","words","params","txt","newTxt","i","c","lastIdx","pos","dic","k","length","charAt","substring","charCodeAt"],"mappings":"AAAAA,QAAS,IAAK,SAASC,EAASC,GAAWD,EAAQE,QAC/CC,KAAK,OACLC,YAAY,8BACZC,OAAO,YACPC,QAAQ,IACRC,MAAM,EACNC,MAAMC,OACNC,SAASD,OACTE,KAAK,GAAIC,MAAK,KAAK,EAAE,GAAG,GAAG,GAAG,IAElC,IAAIC,GAAc,IAClBb,GAAQc,KAAO,SAASA,GAgBpB,MAfaL,UAATK,IACAA,EAAOC,OAAOC,aAAaC,QAAQ,YAC9BH,IACDA,EAAOC,OAAOG,UAAUC,SACnBL,IACDA,EAAOC,OAAOG,UAAUE,gBACnBN,IACDA,EAAO,QAInBA,EAAOA,EAAKO,OAAO,EAAG,GAAGC,eAE7BT,EAAcC,EACdC,OAAOC,aAAaO,QAAQ,WAAYT,GACjCA,GAEXd,EAAQwB,KAAO,SAASC,EAAOC,GAC3B,GAEAC,GAAKC,EAAQC,EAAGC,EAAGC,EAASC,EAFxBC,EAAMR,EAAMzB,EAAQc,QACxBoB,EAAIR,EAAO,EAEX,KAAKO,EAED,MAAOC,EAGX,IADAP,EAAMM,EAAIC,IACLP,EAED,MAAOO,EAEX,IAAIR,EAAOS,OAAS,EAAG,CAGnB,IAFAP,EAAS,GACTG,EAAU,EACLF,EAAI,EAAIA,EAAIF,EAAIQ,OAASN,IAC1BC,EAAIH,EAAIS,OAAOP,GACL,MAANC,GACAF,GAAUD,EAAIU,UAAUN,EAASF,GACjCA,IACAG,EAAML,EAAIW,WAAWT,GAAK,GAEtBD,GADM,EAANI,GAAWA,GAAON,EAAOS,OACf,IAAMR,EAAIS,OAAOP,GAEjBH,EAAOM,GAErBD,EAAUF,EAAI,GACD,OAANC,IACPF,GAAUD,EAAIU,UAAUN,EAASF,GACjCA,IACAD,GAAUD,EAAIS,OAAOP,GACrBE,EAAUF,EAAI,EAGtBD,IAAUD,EAAIN,OAAOU,GACrBJ,EAAMC,EAEV,MAAOD"},"dependencies":["mod/$"]}