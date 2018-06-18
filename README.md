# smart-crud
Fast and easy CRUD application maker.

__Do not use it now, because it is far from beeing ready!__

----

## sample1.json

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
  
  access: {
    organization: { CRUD: ADMIN }
  }

  links: [
    { issue: author, user: - }
    { issue: solver, user: - }
    { comment: issue, issue: comments }
    { comment: author, user: - }
  ]
}
```

