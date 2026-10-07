<script lang="ts" setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  pinLength: {
    type: Number,
    default: 4
  },
  
  title: {
    type: String,
    default: 'Enter PIN'
  },
  subtitle: {
    type: String,
    default: 'Authorize this transaction'
  },
  isProcessing: {
    type: Boolean,
    default: false
  },
  errorMessage: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'complete', 'forgot-pin', 'close']);

const currentPin = ref('');

// Computed property to display dots
const dots = computed(() => {
  return Array.from({ length: props.pinLength }, (_, i) => i < currentPin.value.length);
});

// Logic for keypad input
const handleKeyPress = (num:number) => {
  if (props.isProcessing){
    return
}

  if (currentPin.value.length < props.pinLength) {
    currentPin.value += num.toString();
  }
};

const handleBackspace = () => {
  if (props.isProcessing){
     return
    }

  currentPin.value = currentPin.value.slice(0, -1);
};

// Reset PIN when modal closes or opens
watch(() => props.modelValue, (isOpen) => {
  if (!isOpen) {
    currentPin.value = '';
  }
});

// Watch for PIN completion
watch(currentPin, (newVal) => {
  if (newVal.length === props.pinLength) {
    emit('complete', newVal);
  }
});

const closeModal = () => {
  emit('update:modelValue', false);
  emit('close');
};
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="modelValue" class="fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[60] flex flex-col justify-end" @click.self="closeModal">

        <Transition name="slide-up">
          <div  v-if="modelValue" class="bg-surface-container-lowest rounded-t-[1.5rem] shadow-[0_-8px_32px_rgba(6,54,31,0.12)] w-full max-w-md mx-auto relative bottom-sheet-gradient overflow-hidden">

            <div class="flex justify-center pt-3">
              <div class="w-10 h-1 bg-outline-variant/30 rounded-full"></div>
            </div>

            <div class="px-6 pt-4 pb-6 flex justify-between items-start">
              <div>
                <h3 class="text-xl font-extrabold font-headline text-on-surface">{{ title }}</h3>
                <p class="text-sm text-on-surface-variant">{{ subtitle }}</p>
              </div>
              <button @click="closeModal" class="p-2 hover:bg-surface-container rounded-full transition-colors focus:outline-none">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
              </button>
            </div>

            <div class="px-6 py-8 flex flex-col items-center">
              <div class="flex gap-6 mb-4">
                <div
                  v-for="(filled, index) in dots"
                  :key="index"
                  class="pin-dot"
                  :class="{ 'filled': filled }"
                ></div>
              </div>

              <div class="h-8 flex items-center justify-center">
                <Transition name="fade" mode="out-in">
                  <p v-if="errorMessage && !isProcessing" class="text-error text-sm font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">error</span> {{ errorMessage }}
                  </p>
                  <div v-else-if="isProcessing" class="flex flex-col items-center gap-2">
                    <div class="w-5 h-5 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-[10px] text-primary font-bold uppercase tracking-widest">Verifying</p>
                  </div>
                </Transition>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-px bg-surface-container/50 border-t border-surface-container">
              <button
                v-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]"
                :key="n"
                @click="handleKeyPress(n)"
                class="keypad-btn"
              >
                {{ n }}
              </button>

              <button @click="emit('forgot-pin')" class="keypad-btn text-primary  uppercase tracking-wider font-sm">
                <small>
                    Forgot?
                </small>

              </button>

              <button @click="handleKeyPress(0)" class="keypad-btn">
                0
              </button>

              <button @click="handleBackspace" class="keypad-btn text-on-surface-variant">
                <span class="material-symbols-outlined">
                    <small>
                        backspace
                    </small>
                    </span>
              </button>
            </div>

            <div class="h-6 bg-surface-container-lowest"></div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Scoped Custom CSS from your provided styles */
@reference "tailwindcss";

.bottom-sheet-gradient {
  background: linear-gradient(180deg, rgba(220, 255, 229, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
}

.pin-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background-color: transparent;
  border: 2px solid #89b898;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.pin-dot.filled {
  background-color: #006a28;
  border-color: #006a28;
  transform: scale(1.2);
}

/* .keypad-btn {
  @apply py-6 bg-surface-container-lowest hover:bg-surface-container-low
         transition-colors font-headline text-sm font-bold text-on-surface
         active:scale-95 focus:outline-none select-none;
} */

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
}

/* Material Symbols Config */
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
