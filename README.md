# smart-crud
Fast and easy CRUD application maker.

__Do not use it now, because it is far from beeing ready!__

----

### data.xjs

```js
{Data
  tables: {
    issue: { 
      title: string256, 
      desc: string 
      type: [QUESTION BUG IMPROVEMENT]
      status: [NEW REJECTED ASSIGNED TEST RESOLVED]
    }
    comment: { desc: string, date: date }
  }
  links: [
    { issue: author, user!: - }
    { issue: solver, user!: - }
    { comment: issue, issue!: comments }
    { comment: author, user!: - }
  ]
}
```

### data.js

```js
"use strict";

var WS = require("tfw.web-service");
var Data = require("tfw.data");

var TableIssue = Data.defTable({
  title: "string256",
  desc: "string",
  type: ["QUESTION", "BUG", "IMPROVEMENT"],
  status: ["NEW", "REJECTED", "ASSIGNED", "TEST", "RESOLVED"],
  author: "?",
  solver: "?",
  comments: "*"
});


var TableComment = Data.defTable({
  desc: "string",
  date: "date",
  issue: "?",
  author: "?"
});


exports.lstIssue = function( args ) {
  return WS.get("data.issue.Lst", Data.secureLstArgs( args ));
};

exports.getIssue = function( id ) {
  return new Promise(function (resolve, reject) {
    WS.get("data.issue.Get", { id: id }).then(function( value ) {
      if( Array.isArray( value ) ) {
        resolve( value.map(function() { return new TableIssue( value ); }) );
      } else {
        resolve( new TableIssue( value ) );
      }
    }, reject);
  });
};

exports.delIssue = function( id ) {
  return WS.get("data.issue.Del", { id: id });
};

exports.newIssue = function( attribs ) {
  return new Promise(function (resolve, reject) {
    WS.get("data.issue.New", TableIssue.Cast( attribs )).then(function( value ) {
      resolve( new TableIssue( value ) );
    }, reject);
  });
};


exports.lstComment = function( args ) {
  return WS.get("data.comment.Lst", Data.secureLstArgs( args ));
};

exports.getComment = function( id ) {
  return new Promise(function (resolve, reject) {
    WS.get("data.comment.Get", { id: id }).then(function( value ) {
      if( Array.isArray( value ) ) {
        resolve( value.map(function() { return new TableComment( value ); }) );
      } else {
        resolve( new TableComment( value ) );
      }
    }, reject);
  });
};

exports.delComment = function( id ) {
  return WS.get("data.comment.Del", { id: id });
};

exports.newComment = function( attribs ) {
  return new Promise(function (resolve, reject) {
    WS.get("data.comment.New", TableComment.Cast( attribs )).then(function( value ) {
      resolve( new TableComment( value ) );
    }, reject);
  });
};
```
