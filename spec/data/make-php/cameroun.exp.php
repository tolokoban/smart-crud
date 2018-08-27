{
  structure: {
    user: {
      dashboard: string
    }
    organization: { name: string256 }
    carecenter: { name: string256 }
    structure: {
      name: string256
      exams: string
      vaccins: string
      patient: string
      forms: string
      types: string
    }
    patient: {
      key: string256
    }
    patient-field: {
      key: string256
      value: string
    }
    file: {
      name: string256
      hash: string256
      mime: string256
      size: integer
    }
    admission: {
      enter: datetime
      exit: datetime      
    }
    consultation: {
      date: datetime
    }
    data: {
      key: string256
      value: string
    }
    shapshot: {
      key: string256
      value: string
    }    
    attachment: {
      name: string256
      desc: string256
      date: datetime
      mime: string256
    }
    vaccin: {
      key: string256
      date: datetime
      lot: string256
    }
  }
  links: [
    "!organization.carecenters* | carecenter.organization"
    "!organization.structures* | structure.organization"
    "carecenter.structure | structure.carecenters*"
    "user.organizations* | organization.admins*"
    "user.carecenters* | carecenter.admins*"
    "!admission.consultations* | consultation.admission"
    "!consultation.datas* | data.consultation"
    "!patient.admissions* | admission.patient"
    "!patient.attachments* | attachment.patient"
    "!patient.vaccins* | vaccin.patient"
  ]
}
