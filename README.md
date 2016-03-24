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
    $update: TEACHER | PARENT
    $delete: TEACHER
  Parent:
    user: User?
    firstname: STRING
    lastname: STRING
    students: Student*
      $create: TEACHER | user == $user
      $delete: TEACHER
  Teacher:
    user: User?
    firstname: STRING
    lastname: STRING
    students: Student*
    $update: user == $user
```

