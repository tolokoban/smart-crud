# smart-crud
Fast and easy CRUD application maker.

__Do not use it now, because it is far from beeing ready!__

----

## sample1.json

```
{
    "prefix": "test",
    "data": {
        "Tag": {
            "name": "CHAR",
            "issues": "Issue*"
        },
        "Issue": {
            "title": "CHAR",
            "content": "TEXT",
            "author": "User",
            "date": "DATETIME",
            "comments": "#Comment.issue",
            "votes": "#Vote.issue",
            "tags": "Tag*",
            "status": ["OPEN", "FIXED", "CLOSED"],
            "type": ["BUG", "FEATURE"]
        },
        "Comment": {
            "content": "TEXT",
            "author": "User",
            "date": "DATETIME",
            "issue": "Issue"
        },
        "Vote": {
            "user": "User",
            "issue": "Issue",
            "vote": "INT"
        }
    }
}
```

