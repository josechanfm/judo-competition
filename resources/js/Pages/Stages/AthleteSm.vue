<template>
  <div class="px-2 h-full" :class="{ seed: athlete.seed }">
    <div class="flex h-full items-center justify-between">
      <div class="text-2xl font-bold flex-1">
        <div v-if="language === 0">
          {{ athlete.name_zh }}
        </div>
        <div v-else>
          {{ athlete.name_pt }}
        </div>
      </div>

      <div class="text-gray-500 text-2xl font-medium">
        {{ athlete.team.abbreviation }} -
        <template v-if="language === 0">
          {{ athlete.team.name_zh }}
        </template>
        <template v-else>
          {{ athlete.team.name_pt }}
        </template>
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
