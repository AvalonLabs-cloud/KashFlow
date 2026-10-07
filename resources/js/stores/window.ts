// stores/window.ts
import { defineStore } from 'pinia'
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useSideBarStore } from './sidebar'

export const useWindowStore = defineStore('window', () => {
  const sidebar = useSideBarStore()

  const width = ref(window.innerWidth)
  const isDesktop = ref(width.value >= 1024)

  const updateWidth = () => (width.value = window.innerWidth)

  // Watch for crossing the desktop breakpoint
  watch(width, (newWidth, oldWidth) => {
    const wasDesktop = oldWidth >= 1024
    const nowDesktop = newWidth >= 1024
    isDesktop.value = nowDesktop

    // Only toggle when crossing from mobile → desktop
    if (!wasDesktop && nowDesktop) {
      sidebar.toggle()
    }
  })

  onMounted(() => {
    window.addEventListener('resize', updateWidth)

    // Initial desktop check
    if (isDesktop.value){
       sidebar.toggle()
    }

  })

  onUnmounted(() => {
    window.removeEventListener('resize', updateWidth)
  })

  // Return something minimal if needed
  return { width, isDesktop }
})
