<template>
    <inertia-head :title="$t('competitions.manage')" />
  
    <GuestLayout>
      <template #header>
        <div class="mx-4 py-4">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $t("competitions.manage") }}
          </h2>
        </div>
      </template>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="flex flex-col items-center">
              <a-qrcode :value="route('competition.show',competition.id)+competition.uuid??''" :size="256"/>
              <div class="text-3xl">
                {{ competition.token }}
              </div>
            </div>
            <a :href="route('competition.show',competition.id)" target="_blank">Link</a>
        </div>
    </GuestLayout>
  </template>
  
  <script>
  import GuestLayout from "@/Layouts/GuestLayout.vue";
  export default {
    components: {
      GuestLayout,
    },
    props: ["competition"],
    data() {
      return {
      };
    },
    mounted(){
    window.app=this
    },
    computed: {
    },
    created() {
    },
    methods: {
    },
  };
  </script>
  