var Issue = require("test.model.issue");

Issue.request().then(function( data ) {
    console.info("[test] data=...", data);
}, function( err ) {
    console.error(err);
});
