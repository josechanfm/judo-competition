<template>
  <div class="max-w-2xl mx-auto p-4 sm:p-6 font-sans">
    <!-- Header -->
    <div class="text-center mb-6">
      <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-900 to-indigo-800 bg-clip-text text-transparent">
        QR Scanner
      </h1>
      <p class="text-gray-600 mt-2 text-lg">
        Use your webcam to scan QR codes
      </p>
    </div>

    <!-- Scanner Card -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-4 sm:p-6 border border-white/40">
      <!-- Camera View - Always at the top -->
      <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl overflow-hidden mb-6 relative min-h-[350px]">
        <!-- Camera View when active -->
        <div v-if="isScanning" class="relative w-full h-full">
          <qrcode-stream 
            v-if="cameraActive"
            ref="qrScanner"
            @detect="onDetect" 
            @error="onError" 
            @camera-on="handleCameraOn"
            @camera-off="handleCameraOff"
            class="w-full min-h-[350px] object-cover bg-blue-100"
          />
          
          <!-- Scanning Overlay (only shown when camera is ready) -->
          <div v-if="cameraReady" class="absolute inset-0 pointer-events-none flex items-center justify-center">
            <div class="w-48 h-48 border-2 border-white/50 rounded-lg relative">
              <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-white"></div>
              <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-white"></div>
              <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-white"></div>
              <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-white"></div>
            </div>
          </div>

          <!-- Scanning Status -->
          <div v-if="cameraReady" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-4 py-2 rounded-full text-sm backdrop-blur-sm flex items-center gap-2">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span>Camera active - {{ scanAttempts > 0 ? `Scanning (${scanAttempts})` : 'Looking for QR code...' }}</span>
          </div>
        </div>

        <!-- Placeholder when camera is off -->
        <div v-else class="bg-gray-100 rounded-2xl min-h-[350px] flex flex-col items-center justify-center text-gray-500 border-2 border-dashed border-gray-300">
          <span class="text-6xl mb-3">📷</span>
          <p class="text-lg font-medium">Camera is off</p>
          <p class="text-sm mt-2">Click "Start Camera" below to begin scanning</p>
          <p class="text-xs text-gray-400 mt-4">Make sure to allow camera permissions when prompted</p>
        </div>
      </div>

      <!-- Control Buttons - Below the camera view -->
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <button 
          @click="toggleScanning" 
          :class="[
            'flex-1 py-3 px-6 rounded-xl font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-lg',
            isScanning 
              ? 'bg-red-500 hover:bg-red-600 text-white shadow-red-200' 
              : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-blue-200'
          ]"
        >
          <span class="text-xl">{{ isScanning ? '⏸️' : '📷' }}</span>
          <span>{{ isScanning ? 'Stop Camera' : 'Start Camera' }}</span>
        </button>

        <button 
          v-if="!isScanning && hasAttemptedScan && !decodedResult"
          @click="retryScan" 
          class="flex-1 py-3 px-6 rounded-xl font-medium bg-amber-100 hover:bg-amber-200 text-amber-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-md"
        >
          <span class="text-xl">🔄</span>
          <span>Try Again</span>
        </button>

        <button 
          v-if="decodedResult"
          @click="clearResult" 
          class="flex-1 py-3 px-6 rounded-xl font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-md"
        >
          <span class="text-xl">🔄</span>
          <span>Scan New Code</span>
        </button>
      </div>

      <!-- Permission Request / Info -->
      <div v-if="showPermissionPrompt && !isScanning" class="mb-4 bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
        <p class="text-blue-800 mb-2">🔒 Camera access required</p>
        <p class="text-sm text-blue-600">Please allow camera access when prompted by your browser</p>
      </div>

      <!-- Result Panel -->
      <div v-if="decodedResult" class="mt-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border-l-4 border-green-600 shadow-md animate-fadeIn">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <span class="text-2xl">✅</span>
            <h3 class="font-semibold text-gray-800 text-lg">Scan Result</h3>
          </div>
          <div class="flex gap-2">
            <button 
              @click="copyResult" 
              class="w-8 h-8 rounded-full hover:bg-white/60 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
              title="Copy to clipboard"
            >
              📋
            </button>
          </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-green-100 shadow-inner">
          <pre class="font-mono text-gray-700 whitespace-pre-wrap break-words text-sm sm:text-base">{{ decodedResult }}</pre>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mt-4 bg-red-100 text-red-800 rounded-xl px-6 py-4 flex items-center justify-between border border-red-200">
        <div class="flex items-center gap-3">
          <span class="text-xl">⚠️</span>
          <span class="font-medium">{{ errorMessage }}</span>
        </div>
        <button @click="dismissError" class="hover:bg-red-200 rounded-full w-8 h-8 flex items-center justify-center">✕</button>
      </div>

      <!-- Auto-stop warning -->
      <div v-if="showAutoStopWarning" class="mt-3 text-sm text-amber-600 bg-amber-50 rounded-lg p-3 flex items-center gap-2 border border-amber-200">
        <span>⏱️</span>
        <span>No QR code detected after {{ autoStopTimeout }} seconds. Click "Try Again" to restart.</span>
      </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-gray-500 text-sm mt-6">Desktop Webcam QR Scanner · Vue 3 + Tailwind</p>
  </div>
</template>

<script>
import { QrcodeStream } from 'qrcode-reader-vue3'

export default {
  name: 'DesktopQRScanner',
  components: {
    QrcodeStream
  },
  data() {
    return {
      decodedResult: null,
      errorMessage: '',
      isScanning: false,
      cameraActive: false,
      cameraReady: false,
      scanAttempts: 0,
      hasAttemptedScan: false,
      showAutoStopWarning: false,
      showPermissionPrompt: false,
      autoStopTimeout: 30,
      scanTimer: null,
      maxAttempts: 50,
      processedResults: new Set()
    }
  },
  methods: {
    toggleScanning() {
      if (this.isScanning) {
        this.stopScanning()
      } else {
        this.startScanning()
      }
    },

    startScanning() {
      this.showPermissionPrompt = true
      this.isScanning = true
      this.hasAttemptedScan = true
      this.showAutoStopWarning = false
      this.errorMessage = ''
      
      // Small delay to ensure component is ready
      setTimeout(() => {
        this.cameraActive = true
      }, 100)
      
      // Reset scan tracking
      this.scanAttempts = 0
      this.processedResults.clear()
      
      // Start auto-stop timer
      this.startScanTimer()
    },

    stopScanning() {
      this.isScanning = false
      this.cameraActive = false
      this.cameraReady = false
      this.clearScanTimer()
      
      // Force cleanup
      if (this.$refs.qrScanner) {
        this.$refs.qrScanner = null
      }
    },

    retryScan() {
      this.stopScanning()
      // Clear any errors
      this.errorMessage = ''
      this.showAutoStopWarning = false
      // Restart after a moment to ensure cleanup
      setTimeout(() => {
        this.startScanning()
      }, 500)
    },

    startScanTimer() {
      this.clearScanTimer()
      this.scanTimer = setTimeout(() => {
        if (this.isScanning && !this.decodedResult) {
          this.stopScanning()
          this.showAutoStopWarning = true
        }
      }, this.autoStopTimeout * 1000)
    },

    clearScanTimer() {
      if (this.scanTimer) {
        clearTimeout(this.scanTimer)
        this.scanTimer = null
      }
    },

    handleCameraOn() {
      this.cameraReady = true
      this.showPermissionPrompt = false
      console.log('Camera is on and ready')
    },

    handleCameraOff() {
      this.cameraReady = false
      console.log('Camera is off')
    },

    onDetect(detected) {
      if (!this.isScanning || !this.cameraReady) return
      
      this.scanAttempts++
      
      // Auto-stop if too many attempts without result
      if (this.scanAttempts >= this.maxAttempts && !this.decodedResult) {
        this.stopScanning()
        this.showAutoStopWarning = true
        return
      }

      // Extract QR content
      try {
        let result = null
        
        if (detected && detected.length > 0) {
          result = detected[0].rawValue || detected[0].content || JSON.stringify(detected[0])
        } else if (detected && typeof detected === 'string') {
          result = detected
        } else if (detected && detected.content) {
          result = detected.content
        }
        
        if (result) {
          // Clean up the result
          result = result.trim()
          if (result.startsWith('"') && result.endsWith('"')) {
            result = result.slice(1, -1)
          }
          
          // Check for duplicates
          if (!this.processedResults.has(result)) {
            this.processedResults.add(result)
            this.decodedResult = result
            this.stopScanning() // Immediately stop scanning on success
            this.showAutoStopWarning = false
          }
        }
      } catch (e) {
        console.error('Detection error:', e)
        this.errorMessage = 'Error processing QR code'
      }
    },

    onError(error) {
      console.error('Scanner error:', error)
      this.cameraReady = false
      
      if (error.name === 'NotAllowedError' || error.message.includes('permission')) {
        this.errorMessage = 'Camera access denied. Please allow camera permissions and try again.'
        this.showPermissionPrompt = true
      } else if (error.name === 'NotFoundError' || error.message.includes('No device')) {
        this.errorMessage = 'No camera found. Please connect a webcam and try again.'
      } else if (error.name === 'NotReadableError' || error.message.includes('in use')) {
        this.errorMessage = 'Camera is already in use by another application.'
      } else {
        this.errorMessage = error.message || 'Failed to access camera'
      }
      
      this.stopScanning()
    },

    dismissError() {
      this.errorMessage = ''
    },

    clearResult() {
      this.decodedResult = null
      this.scanAttempts = 0
      this.processedResults.clear()
    },

    async copyResult() {
      if (this.decodedResult) {
        try {
          await navigator.clipboard.writeText(this.decodedResult)
          // Show temporary success
          const msg = this.errorMessage
          this.errorMessage = '✓ Copied to clipboard!'
          setTimeout(() => {
            this.errorMessage = msg
          }, 2000)
        } catch (err) {
          this.errorMessage = 'Failed to copy'
        }
      }
    }
  },
  beforeUnmount() {
    this.clearScanTimer()
    this.stopScanning()
  }
}
</script>

<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

/* Ensure the QR scanner fills the container properly */
.qrcode-stream-wrapper,
.qrcode-stream-camera,
.qrcode-stream-overlay,
.qrcode-stream video {
  border-radius: 1rem !important;
  width: 100% !important;
  height: 100% !important;
  min-height: 350px !important;
  max-height: 450px !important;
  object-fit: cover !important;
}

/* Camera container */
.qrcode-stream-wrapper {
  background: #1e3a8a !important;
}

/* Ensure video is visible */
.qrcode-stream video {
  transform: scaleX(1) !important;
}

/* Pulse animation for status indicator */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>