import { defineStore } from 'pinia'
export const useSideBarStore = defineStore('sidebar', {
    state: () => ({
        isOpen: false as SideBarState['isOpen'],
    }),
    getters:{
        isSidebarOpen: (state) => state.isOpen
    },
   actions :{
    toggle(){
       this.isOpen  = !this.isOpen
    }
   }
})

interface SideBarState{
    isOpen: boolean
}
