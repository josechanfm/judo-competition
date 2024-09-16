<template>
  <div class="px-2 h-full" :class="{ seed: athlete.athlete.seed }">
    <div class="flex h-full flex-col justify-center">
      <div v-if="athlete.athlete.name.length + athlete.athlete.name_secondary?.length <= 30">
        <div class="text-3xl font-bold">
          {{ athlete.name }}

          <template v-if="competition.is_language_secondary_enabled">
            {{ athlete.athlete.name_secondary }}
          </template>
        </div>
      </div>
      <div v-else>
        <div class="text-2xl font-bold">
          {{ athlete.athlete.name }}

          <template v-if="competition.is_language_secondary_enabled">
            {{ athlete.athlete.name_secondary }}
          </template>
        </div>
      </div>
      <div class="text-gray-500 text-2xl font-medium">
        {{ athlete.athlete.team.abbreviation }} -
        {{ athlete.athlete.team.name }}

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
