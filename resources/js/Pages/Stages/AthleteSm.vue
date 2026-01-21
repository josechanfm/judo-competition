<template>
  <div class="px-2 h-full" :class="{ seed: athlete.seed }">
    <div class="flex h-full items-center justify-between">
      <div class="text-2xl font-bold flex-1">
        
          {{ (athlete.athlete.name ?? '') + athlete.athlete.name_secondary}}
      </div>

      <div class="text-gray-500 text-2xl font-medium">
          {{ athlete.athlete.team.name }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Athlete",
  inject: ["language"],
  props: {
    athlete: {
      required: true,
      type: Object,
    },
  },
};
</script>

<style scoped>
.seed {
  @apply bg-yellow-400;
}
</style>
