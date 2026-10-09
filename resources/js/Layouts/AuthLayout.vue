<script setup>
import JudokaLogo from "@/Svgs/judoka-logo.svg";

defineProps({
  // 選填標題／說明；未提供時只渲染卡片外框（其餘 Auth 頁面不受影響）
  title: {
    type: String,
    default: "",
  },
  description: {
    type: String,
    default: "",
  },
});
</script>

<template>
  <div
    class="relative flex min-h-screen w-full items-center justify-center overflow-hidden bg-gradient-to-br from-slate-100 via-white to-red-50 px-4 py-10"
  >
    <!-- 背景裝飾光暈 -->
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
      <div
        class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-red-500/10 blur-3xl"
      ></div>
      <div
        class="absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-indigo-500/10 blur-3xl"
      ></div>
    </div>

    <div class="relative w-full max-w-md">
      <div class="mb-8 flex items-center justify-center">
        <JudokaLogo class="h-7 w-auto" />
        <span class="ml-2 text-lg font-semibold tracking-[0.2em] text-gray-800"
          >CMS</span
        >
      </div>

      <div
        class="rounded-2xl border border-white/60 bg-white/90 p-8 shadow-xl shadow-slate-300/40 backdrop-blur-sm"
      >
        <div v-if="title" class="mb-6 text-center">
          <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
          <p v-if="description" class="mt-1.5 text-sm text-gray-500">
            {{ description }}
          </p>
        </div>

        <slot />
      </div>

      <div v-if="$slots.footer" class="mt-6 flex justify-center">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>
