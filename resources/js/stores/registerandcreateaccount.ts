import { defineStore } from 'pinia'

type RegistrationState = {
  firstname: string
  lastname: string
  email: string
  password: string
}

export const useRegistrationStore = defineStore('registration', {
  state: () => ({
    firstName: '' as string,
    lastName: '' as string,
    email: '' as string,
    phone : '' as string,
    password: '' as string,
    bvn: '' as string,
  }),

  getters: {
    fullName: (state) => `${state.firstName} ${state.lastName}`
  },

  actions: {
    setPersonalInfo(data: { firstName: string; lastName: string }) {
      this.firstName = data.firstName
      this.lastName = data.lastName
    },

    setContactInfo(data: { email: string; phone: string }) {
      this.email = data.email
      this.phone = data.phone
    },

    setPassword(password: string) {
      this.password = password
    },

    setFirstStageCredentials(firstStageData : RegistrationState){
      this.firstName = firstStageData.firstname
      this.lastName = firstStageData.lastname
      this.email = firstStageData.email
      this.password = firstStageData.password

      if (!this.firstName || !this.lastName || !this.email || !this.password) {
           return false
      } else {
            return true
      }
    },




    reset() {
      this.firstName = ''
      this.lastName = ''
      this.email = ''
      this.phone = ''
      this.password = ''
    }
  }
})
