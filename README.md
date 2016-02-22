# smart-crud
Fast and easy CRUD application maker.

## crud.json

```
{
  "classes": {
    "Student": {
      "$caption": { "fr": "Elève", "en": "Student" },
      "firstname": "STRING",
      "lastname": {
        "type": "STRING",
        "transform": "uppercase"
      },
      "age": {
        "type": "INTEGER",
        "constraint": { "min": 3, "max": 18 }
      },
      "level": "Level",
      "group": "Group"
    },
    "Level": {
      "name": "STRING"
    },
    "Group": {
      "name": "STRING",
      "students": "Student[R]",
      "teachers": "Teacher[]"
    },
    "Teacher": {
      "firstname": "STRING",
      "lastname": "STRING",
      "groups": "Group[]"
      "levels": "Level[R]"
      "students": "Student[R]",
    }
  }
}
```
