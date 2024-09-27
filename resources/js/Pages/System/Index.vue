<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
  <Head title="Dashboard" />

  <AdminLayout>
    <a-page-header title="System description"> </a-page-header>
    <div class="mx-6 flex flex-col gap-3">
      <div class="font-bold text-xl">Use Video</div>
      <div class="flex gap-8">
        <iframe
          width="560"
          height="315"
          src="https://www.youtube.com/embed/DmUt4tbng7g?si=iVn0--9fEQVLARnP"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
        <iframe
          width="560"
          height="315"
          src="https://www.youtube.com/embed/zovEPC_yV8s?si=OgP2aO_D0ojfNOkg"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
        <iframe
          width="560"
          height="315"
          src="https://www.youtube.com/embed/AlEvNZ2TdUA?si=ro1PmOpzM4zeDTG1"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
        <iframe
          width="560"
          height="315"
          src="https://www.youtube.com/embed/UHIil3l7gXA?si=a4pkiV6cRtIzasHY"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
        ></iframe>
      </div>
      <div class="font-bold text-xl">System Diagram</div>
      <div class="mb-4"><img src="/diagrams/Compeition_diagram.svg" /></div>
    </div>
  </AdminLayout>
</template>
