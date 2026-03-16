<template>
  <div class="max-w-2xl mx-auto p-4 sm:p-6 font-sans">
    <!-- Header -->
    <div class="text-center mb-6">
      <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-900 to-indigo-800 bg-clip-text text-transparent">
        QR Scanner
      </h1>
      <p class="text-gray-600 mt-2 text-lg">Point your camera at a QR code</p>
    </div>

    <!-- Scanner Card -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-4 sm:p-6 border border-white/40">
      <!-- Camera View -->
      <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl overflow-hidden mb-4 relative min-h-[350px]">
        <div v-if="scannerActive" class="relative w-full h-full">
          <!-- Use v-if to completely unmount the component when not active -->
          <QrcodeStream 
            v-if="cameraMounted"
            key="qr-scanner"
            @decode="onDecode" 
            @error="onError" 
            @camera-on="cameraReady = true"
            @camera-off="cameraReady = false"
            class="w-full min-h-[350px] object-cover bg-blue-100"
          />
          
          <!-- Scanning Overlay (only show when camera is actually ready) -->
          <div v-if="cameraReady" class="absolute inset-0 pointer-events-none flex items-center justify-center">
            <div class="w-48 h-48 border-2 border-white/50 rounded-lg relative">
              <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-white"></div>
              <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-white"></div>
              <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-white"></div>
              <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-white"></div>
            </div>
          </div>

          <!-- Camera initializing / Scanning Indicator -->
          <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-4 py-2 rounded-full text-sm backdrop-blur-sm">
            <span v-if="!cameraReady">Initializing camera...</span>
            <span v-else>Scanning for QR code...</span>
          </div>
        </div>

        <!-- Placeholder when camera is off -->
        <div v-else class="bg-gray-100 rounded-2xl min-h-[350px] flex flex-col items-center justify-center text-gray-500 border-2 border-dashed border-gray-300">
          <span class="text-6xl mb-3">📷</span>
          <p class="text-lg">Camera is off</p>
          <p class="text-sm mt-2">Click "Turn On Scanner" to begin</p>
        </div>
      </div>

      <!-- Control Button -->
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <button 
          @click="toggleScanner" 
          :class="[
            'flex-1 py-3 px-6 rounded-xl font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-lg',
            scannerActive 
              ? 'bg-red-500 hover:bg-red-600 text-white shadow-red-200' 
              : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-blue-200'
          ]"
        >
          <span class="text-xl">{{ scannerActive ? '⏸️' : '📷' }}</span>
          <span>{{ scannerActive ? 'Turn Off Scanner' : 'Turn On Scanner' }}</span>
        </button>
        
        <!-- Show Try Again button if there was an error or after successful scan -->
        <button 
          v-if="!scannerActive && (errorMessage || decodedData)"
          @click="resetAndRetry" 
          class="flex-1 py-3 px-6 rounded-xl font-medium bg-amber-100 hover:bg-amber-200 text-amber-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-md"
        >
          <span class="text-xl">🔄</span>
          <span>Scan New Code</span>
        </button>
      </div>

      <!-- Result Panel - Show when QR code is decoded -->
      <div v-if="decodedData" class="mt-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border-l-4 border-green-600 shadow-md animate-fadeIn">
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
            <button 
              @click="clearData" 
              class="w-8 h-8 rounded-full hover:bg-white/60 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors"
              title="Clear"
            >
              ✕
            </button>
          </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-green-100 shadow-inner">
          <!-- Check if result is a URL -->
          <template v-if="isUrl(decodedData)">
            <p class="text-sm text-gray-600 mb-2">🔗 Detected URL:</p>
            <a 
              :href="decodedData" 
              target="_blank" 
              rel="noopener noreferrer"
              class="text-blue-600 hover:text-blue-800 underline break-words font-mono text-sm sm:text-base"
            >
              {{ decodedData }}
            </a>
          </template>
          <!-- Plain text result -->
          <template v-else>
            <pre class="font-mono text-gray-700 whitespace-pre-wrap break-words text-sm sm:text-base">{{ decodedData }}</pre>
          </template>
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
    </div>

    <!-- Footer -->
    <p class="text-center text-gray-500 text-sm mt-6">QR Scanner · Vue 3 + Tailwind</p>
  </div>
</template>

<script>
import { QrcodeStream } from 'qrcode-reader-vue3';

export default {
  name: 'DesktopQRScanner',
  components: {
    QrcodeStream
  },
  data() {
    return {
      scannerActive: false,
      cameraMounted: false,  // Controls actual mounting of the component
      cameraReady: false,    // Tracks if camera stream is actually live
      decodedData: null,
      errorMessage: null,
    }
  },
  methods: {
    toggleScanner() {
      if (this.scannerActive) {
        this.turnOffCamera();
      } else {
        this.turnOnCamera();
      }
    },

    turnOnCamera() {
      this.scannerActive = true;
      this.errorMessage = null;
      this.cameraReady = false;
      
      // Small delay to ensure clean mount
      setTimeout(() => {
        this.cameraMounted = true;
      }, 100);
    },

    turnOffCamera() {
      // First unmount the component
      this.cameraMounted = false;
      this.cameraReady = false;
      
      // Then update the active state
      setTimeout(() => {
        this.scannerActive = false;
      }, 50);
    },

    resetAndRetry() {
      // Clear any existing data
      this.decodedData = null;
      this.errorMessage = null;
      
      // Turn off camera first (ensures cleanup)
      this.turnOffCamera();
      
      // Turn on camera again after a delay
      setTimeout(() => {
        this.turnOnCamera();
      }, 300);
    },

    clearData() {
      this.decodedData = null;
      // Don't turn off camera automatically - let user decide
    },

    dismissError() {
      this.errorMessage = null;
    },

    onDecode(data) {
      // Store the decoded data
      this.decodedData = data;
      
      // Clear any previous error
      this.errorMessage = '';
      
      // Turn off the camera immediately after successful scan
      this.turnOffCamera();
      
      console.log('QR Code decoded:', data);
      
      // Here you can make your API call
      // this.makeApiCall(data);
    },

    onError(error) {
      console.error('Scanner error:', error);
      
      // Handle different error types
      if (error.name === 'NotAllowedError' || error.message.includes('permission')) {
        this.errorMessage = 'Camera access denied. Please grant camera permissions.';
      } else if (error.name === 'NotFoundError' || error.message.includes('No device')) {
        this.errorMessage = 'No camera found on this device.';
      } else if (error.name === 'NotReadableError' || error.message.includes('in use')) {
        this.errorMessage = 'Camera is already in use by another application.';
      } else if (error.message.includes('Could not start')) {
        this.errorMessage = 'Could not start camera. Please try again.';
      } else {
        this.errorMessage = error.message || 'Failed to access camera';
      }
      
      // Turn off scanner on error
      this.turnOffCamera();
    },

    async copyResult() {
      if (this.decodedData) {
        try {
          await navigator.clipboard.writeText(this.decodedData);
          // Show temporary success message
          const originalError = this.errorMessage;
          this.errorMessage = '✓ Copied to clipboard!';
          setTimeout(() => {
            this.errorMessage = originalError;
          }, 2000);
        } catch (err) {
          this.errorMessage = 'Failed to copy to clipboard';
        }
      }
    },

    isUrl(string) {
      try {
        new URL(string);
        return true;
      } catch {
        return false;
      }
    },

    // Optional: Method to make API call with decoded data
    async makeApiCall(data) {
      try {
        // Uncomment and modify this for your API call
        // const response = await axios.get(route('dae.souvenir.pickupCode'), {
        //     params: { code: data }
        // });
        // Handle response here
        console.log('API call with data:', data);
      } catch (error) {
        this.errorMessage = 'Failed to fetch data: ' + error.message;
      }
    }
  },
  beforeUnmount() {
    // Ensure camera is properly released when component is destroyed
    this.cameraMounted = false;
    this.scannerActive = false;
  },
  watch: {
    // Watch for scannerActive changes to ensure cleanup
    scannerActive(newVal) {
      if (!newVal) {
        // Force unmount when scanner becomes inactive
        this.cameraMounted = false;
        this.cameraReady = false;
      }
    }
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
</style>