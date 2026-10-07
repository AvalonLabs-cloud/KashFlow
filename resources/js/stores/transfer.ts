// stores/transferStore.ts
import { defineStore } from 'pinia'

type Transfer = {
  id: string
  accountNumber: string
  bankName: string
  bankCode: string
  accountName: string
  createdAt: string
}

type TransferForm = {
  accountNumber: string
  bankName: string
  bankCode: string
  accountName:string
}

type State = {
  transfers: Record<string, Transfer>
}

export const useTransferStore = defineStore('transfer', {
  state: (): State => ({
    transfers: {},
  }),

  actions: {
    addTransfer(form: TransferForm): string {
      const id = Date.now().toString()

      this.transfers[id] = {
        id,
        accountNumber: form.accountNumber,
        bankName: form.bankName,
        bankCode: form.bankCode,
        accountName: form.accountName,
        createdAt: new Date().toISOString(),
      }

      this.saveToStorage()

      return id
    },

    getTransfer(id: string | null): Transfer | null {
      return  id != null ?  this.transfers[id] : null
    },

    removeTransfer(id: string): void {
      delete this.transfers[id]
      this.saveToStorage()
    },

    clearAll(): void {
      this.transfers = {}
      this.saveToStorage()
    },

    // 🔥 Persistence
    saveToStorage(): void {
      localStorage.setItem('transfers', JSON.stringify(this.transfers))
    },

    loadFromStorage(): void {
      const data = localStorage.getItem('transfers')

      if (data) {
        this.transfers = JSON.parse(data)
      }
    },
  },

  persist: true,
})
