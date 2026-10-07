<script lang="ts" setup>
import { ref, onMounted, nextTick } from 'vue'
import { useForm } from "@inertiajs/vue3";

import Header from '../../components/Header.vue';

const complaintText = ref(
  "Regarding unauthorized settlement fees applied to transaction ref #TX-892110 on October 14th. Despite prior verification that wire adjustments were waived, an incidental deduction of $45.00 occurred. Please initiate an expedited refund and review our account waiver agreement."
)
const savedComplaintDraft = ref(complaintText.value)
const currentState = ref('form') // 'form', 'loading', 'success', 'error'
const textareaRef = ref(null)

const form = useForm({
message:null
});


const handleFormSubmit = () => {
  savedComplaintDraft.value = complaintText.value
  currentState.value = 'loading'
  form.post('/submitComplaint')
  
  // Simulate network request
  setTimeout(() => {
    currentState.value = 'success'
  }, 1400)
}

const restoreEditorPreservingContent = async () => {
  complaintText.value = savedComplaintDraft.value
  currentState.value = 'form'
  await nextTick()
  textareaRef.value?.focus()
}

const resetAll = async () => {
  complaintText.value = ""
  savedComplaintDraft.value = ""
  currentState.value = 'form'
  await nextTick()
  textareaRef.value?.focus()
}
</script>

<template>
  <div class="app-wrapper">
    <!-- Minimal Brand Header / Nav -->
    <Header :client_name="'avalon'" />
    <!-- Main Content Container -->
    <main class="main-content">
      <Transition name="view-state" mode="out-in">
        
        <!-- ========================================================================= -->
        <!-- STATE 1: COMPLAINT FORM & LOADING -->
        <!-- ========================================================================= -->
        <div v-if="currentState === 'form' || currentState === 'loading'" class="view-container">
          <!-- Header -->
          <div class="form-header">
            <h1 class="page-title">Submit a Complaint</h1>
            <p class="page-subtitle">
              Describe your complaint below. Your message will be forwarded to the appropriate authority for review.
            </p>
          </div>

          <!-- Email Composition Container -->
          <form @submit.prevent="handleFormSubmit" class="complaint-form">
            
            <!-- Subtle Non-editable "To:" Header Field -->
            <div class="to-field">
              <span class="to-label">To:</span>
              <div class="to-badge">
                <span class="to-badge-dot"></span>
                Complaints Department
              </div>
              <span class="to-note">Direct Regulatory Intake</span>
            </div>

            <!-- Large Spacious Email-style Textarea -->
            <div class="textarea-container">
              <textarea
                ref="textareaRef"
                v-model="form.message"
                rows="10"
                required
                placeholder="Write your complaint here..."
                class="complaint-textarea"
                aria-label="Complaint message body"
                :disabled="currentState === 'loading'"
              ></textarea>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">

              <!-- Submit Button -->
              <button
                type="submit"
                class="submit-btn group"
                :disabled="currentState === 'loading'"
              >
                <!-- Regular State -->
                <template v-if="currentState !== 'loading'">
                  <span>Send Complaint</span>
                  <svg class="submit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                </template>

                <!-- Loading State -->
                <template v-else>
                  <span>Sending...</span>
                  <svg class="spinner-icon" fill="none" viewBox="0 0 24 24">
                    <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </template>
              </button>
            </div>
          </form>
        </div>

        <!-- ========================================================================= -->
        <!-- STATE 2: SUCCESSFUL SUBMISSION -->
        <!-- ========================================================================= -->
        <div v-else-if="currentState === 'success'" class="feedback-container">
          <div class="feedback-content">
            <div class="icon-circle success-circle">
              <svg class="feedback-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <h2 class="feedback-title">Complaint Submitted</h2>
            <p class="feedback-desc">Your complaint has been successfully submitted to the appropriate authority.</p>
            <button type="button" @click="resetAll" class="action-btn primary-btn">
              Done
            </button>
          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- STATE 3: FAILED SUBMISSION -->
        <!-- ========================================================================= -->
        <div v-else-if="currentState === 'error'" class="feedback-container">
          <div class="feedback-content">
            <div class="icon-circle error-circle">
              <svg class="feedback-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <h2 class="feedback-title">Submission Failed</h2>
            <p class="feedback-desc">We couldn't submit your complaint. Please try again.</p>
            <button type="button" @click="restoreEditorPreservingContent" class="action-btn secondary-btn">
              Try Again
            </button>
          </div>
        </div>

      </Transition>
    </main>

  </div>
</template>

<style scoped>
/* ==========================================================================
   Base & Typography Reset
   ========================================================================== */
.app-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background-color: #FFFFFF;
  color: #0F172A;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.app-wrapper ::selection {
  background-color: #DCFCE7;
  color: #14532D;
}

button {
  font-family: inherit;
  border: none;
  margin: 0;
  padding: 0;
  cursor: pointer;
  background: transparent;
}

/* ==========================================================================
   Header & Layout
   ========================================================================== */
.app-header {
  width: 100%;
  border-bottom: 1px solid #F1F5F9;
  padding: 1rem 1.5rem;
}

@media (min-width: 768px) {
  .app-header {
    padding: 1rem 2rem;
  }
}

.header-container {
  max-width: 650px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-group {
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.brand-logo {
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 0.5rem;
  background-color: #0F172A;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFFFFF;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.brand-logo-icon {
  width: 1rem;
  height: 1rem;
  color: #34d399;
}

.brand-name {
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: #0F172A;
}

/* ==========================================================================
   State Simulator Controls
   ========================================================================== */
.state-simulator {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  background-color: #F8FAFC;
  padding: 0.25rem;
  border-radius: 0.5rem;
  border: 1px solid rgba(226, 232, 240, 0.7);
  font-size: 0.75rem;
  font-weight: 500;
  color: #475569;
}

.simulator-label {
  padding: 0.125rem 0.5rem;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94A3B8;
  font-family: 'JetBrains Mono', monospace;
}

.simulator-btn {
  padding: 0.25rem 0.625rem;
  border-radius: 0.25rem;
  color: #475569;
  transition: all 0.15s ease-in-out;
}

.simulator-btn:hover {
  color: #0F172A;
}

.simulator-btn.active {
  background-color: #FFFFFF;
  color: #0F172A;
  font-weight: 500;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(226, 232, 240, 0.6);
}

/* ==========================================================================
   Main Content Area
   ========================================================================== */
.main-content {
  width: 100%;
  max-width: 650px;
  margin: 0 auto;
  padding: 2rem 1.25rem;
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

@media (min-width: 768px) {
  .main-content {
    padding-top: 3.5rem;
    padding-bottom: 3.5rem;
  }
}

.view-container {
  width: 100%;
}

.form-header {
  margin-bottom: 1.75rem;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0F172A;
  letter-spacing: -0.025em;
  line-height: 1.375;
  margin-bottom: 0.5rem;
}

@media (min-width: 768px) {
  .page-title {
    font-size: 28px;
  }
}

.page-subtitle {
  font-size: 0.875rem;
  color: #64748B;
  line-height: 1.625;
}

@media (min-width: 768px) {
  .page-subtitle {
    font-size: 15px;
  }
}

/* ==========================================================================
   Complaint Form
   ========================================================================== */
.complaint-form {
  background-color: #FFFFFF;
  border-radius: 1rem;
  border: 1px solid #E2E8F0;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
  overflow: hidden;
  transition: all 0.2s;
}

.complaint-form:focus-within {
  border-color: #16A34A;
  box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
}

.to-field {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1.25rem;
  border-bottom: 1px solid #F1F5F9;
  background-color: rgba(248, 250, 252, 0.4);
  user-select: none;
}

.to-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94A3B8;
  font-family: 'JetBrains Mono', monospace;
}

.to-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: rgba(241, 245, 249, 0.8);
  padding: 0.25rem 0.625rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #1E293B;
  border: 1px solid rgba(226, 232, 240, 0.5);
}

.to-badge-dot {
  width: 0.375rem;
  height: 0.375rem;
  border-radius: 9999px;
  background-color: #16A34A;
}

.to-note {
  font-size: 11px;
  color: #94A3B8;
  margin-left: auto;
  font-family: 'JetBrains Mono', monospace;
  display: none;
}

@media (min-width: 640px) {
  .to-note {
    display: inline-block;
  }
}

.textarea-container {
  padding: 1.25rem;
  background-color: #FFFFFF;
}

@media (min-width: 768px) {
  .textarea-container {
    padding: 1.5rem;
  }
}

.complaint-textarea {
  width: 100%;
  background: transparent;
  color: #0F172A;
  font-size: 1rem;
  line-height: 1.625;
  resize: vertical;
  min-height: 220px;
  max-height: 500px;
  border: none;
  font-family: inherit;
}

.complaint-textarea::placeholder {
  color: #94A3B8;
}

.complaint-textarea:focus {
  outline: none;
  box-shadow: none;
}

.complaint-textarea:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: rgba(248, 250, 252, 0.5);
}

/* Custom Scrollbar for Textarea */
.complaint-textarea::-webkit-scrollbar {
  width: 6px;
}
.complaint-textarea::-webkit-scrollbar-track {
  background: transparent;
}
.complaint-textarea::-webkit-scrollbar-thumb {
  background: #E2E8F0;
  border-radius: 9999px;
}
.complaint-textarea::-webkit-scrollbar-thumb:hover {
  background: #CBD5E1;
}

/* ==========================================================================
   Action Bar & Buttons
   ========================================================================== */
.action-bar {
  padding: 1rem 1.25rem;
  background-color: rgba(248, 250, 252, 0.5);
  border-top: 1px solid #F1F5F9;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

@media (min-width: 640px) {
  .action-bar {
    flex-direction: row;
  }
}

.encryption-note {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #94A3B8;
  order: 2;
}

@media (min-width: 640px) {
  .encryption-note {
    order: 1;
  }
}

.encryption-icon {
  width: 0.875rem;
  height: 0.875rem;
  color: #94A3B8;
}

.submit-btn {
  width: 100%;
  order: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  background-color: #15803D;
  color: #FFFFFF;
  font-weight: 600;
  font-size: 0.875rem;
  letter-spacing: 0.025em;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.15s ease;
}

@media (min-width: 640px) {
  .submit-btn {
    width: auto;
    order: 2;
  }
}

.submit-btn:hover:not(:disabled) {
  background-color: #166534;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
}

.submit-btn:active:not(:disabled) {
  background-color: #14532D;
}

.submit-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 4px #16A34A;
}

.submit-btn:disabled {
  opacity: 0.8;
  cursor: not-allowed;
}

.submit-icon {
  width: 1rem;
  height: 1rem;
  transition: transform 0.15s ease;
}

.submit-btn:hover .submit-icon {
  transform: translateX(2px);
}

.spinner-icon {
  width: 1rem;
  height: 1rem;
  color: #FFFFFF;
  animation: spin 1s linear infinite;
}

.spinner-track {
  opacity: 0.25;
}

.spinner-head {
  opacity: 0.75;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ==========================================================================
   Success & Error States
   ========================================================================== */
.feedback-container {
  width: 100%;
  text-align: center;
  padding: 2.5rem 0;
}

@media (min-width: 768px) {
  .feedback-container {
    padding: 3.5rem 0;
  }
}

.feedback-content {
  max-width: 480px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.icon-circle {
  width: 4rem;
  height: 4rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
}

.success-circle {
  background-color: #ecfdf5;
  border: 1px solid #d1fae5;
  color: #15803D;
}

.error-circle {
  background-color: #fff1f2;
  border: 1px solid #ffe4e6;
  color: #e11d48;
}

.feedback-icon {
  width: 2rem;
  height: 2rem;
}

.feedback-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0F172A;
  letter-spacing: -0.025em;
  margin-bottom: 0.625rem;
}

@media (min-width: 768px) {
  .feedback-title {
    font-size: 26px;
  }
}

.feedback-desc {
  color: #64748B;
  font-size: 0.875rem;
  line-height: 1.625;
  margin-bottom: 2rem;
}

@media (min-width: 768px) {
  .feedback-desc {
    font-size: 1rem;
  }
}

.action-btn {
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 600;
  font-size: 0.875rem;
  letter-spacing: 0.025em;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.15s ease;
}

@media (min-width: 640px) {
  .action-btn {
    width: 12rem;
  }
}

.primary-btn {
  background-color: #15803D;
  color: #FFFFFF;
}

.primary-btn:hover {
  background-color: #166534;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
}

.primary-btn:active {
  background-color: #14532D;
}

.primary-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 4px #16A34A;
}

.secondary-btn {
  background-color: #0F172A;
  color: #FFFFFF;
}

.secondary-btn:hover {
  background-color: #1E293B;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
}

.secondary-btn:active {
  background-color: #020617;
}

.secondary-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 4px #0F172A;
}

/* ==========================================================================
   Footer
   ========================================================================== */
.app-footer {
  width: 100%;
  border-top: 1px solid #F1F5F9;
  padding: 1rem 1.5rem;
  text-align: center;
  font-size: 0.75rem;
  color: #94A3B8;
}

.footer-container {
  max-width: 650px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

@media (min-width: 640px) {
  .footer-container {
    flex-direction: row;
  }
}

/* ==========================================================================
   Vue Transitions
   ========================================================================== */
.view-state-enter-active,
.view-state-leave-active {
  transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
}

.view-state-enter-from,
.view-state-leave-to {
  opacity: 0;
  transform: scale(0.99);
}
</style>