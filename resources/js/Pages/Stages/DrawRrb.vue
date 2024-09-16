<template>
  <div class="h-full flex-col justify-between">
    <div class="p-6 rounded-lgbackdrop-blur">
      <table
        class="w-full text-xl border bg-white border-collapse border-black rounded-lg"
      >
        <thead>
          <tr>
            <th class="h-20"></th>
            <th class="w-96"></th>
            <th class="border bg-amber-300 border-black" v-for="i in athletes.length">
              {{ i }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="i in athletes.length">
            <th class="h-20 w-16 border border-black bg-amber-300">{{ i }}</th>
            <td class="border border-black h-20 p-0">
              <div class="w-full h-full" :id="`seat-${i}`">
                <template v-if="athleteInSeat(i)">
                  <athlete :athlete="athleteInSeat(i)" />
                </template>
              </div>
            </td>
            <td
              class="border border-black w-52"
              v-for="j in athletes.length"
              :class="{ disabled: i === j }"
            ></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="overflow-clip flex items-center justify-center">
      <div class="h-20 mt-16 overflow-clip grid grid-cols-1 gap-2 pending-athlete-list">
        <athlete
          v-for="athlete in pendingAthletes"
          :athlete="athlete"
          class="w-96 h-20 rounded-lg p-1"
          :id="`athlete-${athlete.seat}`"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick } from "vue";
import Athlete from "./Athlete.vue";
import AthleteSm from "./AthleteSm.vue";

const ANIM_MS = 1000;
const ANIM_BETWEEN_MS = 1000;

export default {
  name: "DrawRrb",
  props: {
    athletes: {
      type: Array,
      required: true,
    },
  },
  components: {
    Athlete,
    AthleteSm,
  },
  mounted() {
    nextTick(() => {
      const athleteRef = window.document.getElementById(
        `athlete-${this.athletes[0].seat}`
      );
      const athleteInitialPosition = athleteRef.getBoundingClientRect();

      for (let i = 0; i < this.athletes.length; ++i) {
        // dispatch animation
        setTimeout(() => {
          this.animateHandler(i, athleteInitialPosition);
        }, ANIM_BETWEEN_MS * i);

        // dispatch end animation
        setTimeout(() => {
          this.athletes[i].placed = true;
        }, ANIM_BETWEEN_MS * i + ANIM_MS);
      }
    });
  },
  computed: {
    pendingAthletes() {
      return this.athletes.filter((athlete) => !athlete.placed);
    },
    athleteInSeat() {
      return (seat) => {
        const athlete = this.athletes.find((athlete) => athlete.seat === seat);

        if (!athlete) return undefined;

        if (!athlete.placed) return null;

        return athlete;
      };
    },
  },
  methods: {
    animateHandler(cursor, athleteInitialPosition) {
      const athleteRef = window.document.getElementById(
        `athlete-${this.athletes[cursor].seat}`
      );
      const seatRef = window.document.getElementById(
        `seat-${this.athletes[cursor].seat}`
      );

      athleteRef.style.position = "fixed";
      athleteRef.style.transformOrigin = "center";

      // get initial and destination positions
      const athletePosition = athleteInitialPosition;
      const seatPosition = seatRef.getBoundingClientRect();

      athleteRef.style.width = seatPosition.width + "px";

      // WORKAROUND FOR > 32 ATHLETES
      if (this.athletes.length > 32) {
        athleteRef.style.width = seatPosition.width * 2 + "px";
      }

      const deltaX1 = window.innerWidth / 2 - athletePosition.x - seatPosition.width / 2;

      const deltaY1 =
        window.innerHeight / 2 - athletePosition.y - seatPosition.height / 2;

      console.log("src", athletePosition.x, athletePosition.y);

      const deltaX2 = seatPosition.x - athletePosition.x;
      const deltaY2 = seatPosition.y - athletePosition.y;

      // seat
      console.log("dest", seatPosition.x, seatPosition.y);

      console.log("delta", deltaX2, deltaY2);

      // athleteRef.style.setProperty('--translate-x', deltaX)
      // athleteRef.style.setProperty('--translate-y', deltaY)

      // dispatch animation
      athleteRef.animate(
        [
          {
            transform: "translate(0, 0)",
          },
          {
            transform: `translate(${deltaX1}px, ${deltaY1}px) scale(2)`,
          },
          {
            transform: `translate(${deltaX1}px, ${deltaY1}px) scale(2)`,
          },
          {
            transform: `translate(${deltaX1}px, ${deltaY1}px) scale(2)`,
          },
          {
            transform: `translate(${deltaX2}px, ${deltaY2}px) scale(1)`,
          },
        ],
        {
          duration: ANIM_MS,
          easing: "ease-in-out",
          fill: "backwards",
        }
      );
    },
  },
};
</script>

<style scoped>
.disabled {
  @apply bg-gray-300/50;
}
</style>
