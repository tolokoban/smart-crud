# smart-crud
Fast and easy CRUD application maker.

__Do not use it now, because it is far from beeing ready!__

----

## sample1.json

```js
{Data
  tables: {
    organization: { name: string256 }
    carecenter: { name: string256 }
    patient: { key: string64 }
    patient-form: { value: string }
  }
  
  access: {
    organization: { CRUD: ADMIN }
  }

  links: [
    { carecenter: organization, organization: carecenters* }
    { patient: organization, organization: patients* }
    { patient-form: organization, organization: patient-forms* }
  ]
}```

