<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import JudokaLogo from "@/Svgs/judoka-logo.svg";
</script>

<template>
  <div class="h-screen w-screen flex justify-center items-center bg-gray-100">
    <div class="w-full max-w-sm p-4">
      <div class="text-center mb-12">
        <h1 class="text-xl flex justify-center items-center">
          <judoka-logo class="inline-block h-6" />
          <span class="ml-1 m-4">CMS</span>
        </h1>
      </div>
      <slot />
    </div>
  </div>
</template>
