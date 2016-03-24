# smart-crud
Fast and easy CRUD application maker.

## crud.yaml

```
classes:
  Student:
    firstname: STRING
    lastname: STRING
    age: INTEGER
    parent: Parent*
    teacher: Teacher
    $create: TEACHER
    $request: *
    $update: TEACHER | PARENT
    $delete: TEACHER
  Parent:
    user: User?
    firstname: STRING
    lastname: STRING
    students: Student*
      
  Teacher:
    user: User?
    firstname: STRING
    lastname: STRING
    students: Student*
```

