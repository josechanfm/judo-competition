<template>
  <div class="px-2 h-full" :class="{ seed: athlete.seed }">
    <div class="flex h-full flex-col justify-center">
      <div v-if="athlete.name_zh.length + athlete.name_pt.length <= 30">
        <div class="text-3xl font-bold">
          {{ athlete.name_zh }}

          <template v-if="competition.is_language_secondary_enabled">
            {{ athlete.name_pt }}
          </template>
        </div>
      </div>
      <div v-else>
        <div class="text-2xl font-bold">
          {{ athlete.name_zh }}

          <template v-if="competition.is_language_secondary_enabled">
            {{ athlete.name_pt }}
          </template>
        </div>
      </div>
      <div class="text-gray-500 text-2xl font-medium">
        {{ athlete.team.abbreviation }} -
        {{ athlete.team.name }}

        <!-- <template v-if="competition.is_language_secondary_enabled">
          {{ athlete.name_pt }}
        </template> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Athlete",
  props: {
    athlete: {
      required: true,
      type: Object,
    },
  },
  inject: ["competition"],
};
</script>

<style scoped>
.seed {
  @apply bg-yellow-400;
}
</style>
