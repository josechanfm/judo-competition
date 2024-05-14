<template>
  <div class="flex flex-col h-full">
    <chart :athletes="athletes" v-slot="sp" ref="chart" class="origin-top flex-1">
      <div>
        <template v-if="athleteInSeat(sp.seat)">
          <athlete :athlete="athleteInSeat(sp.seat)" v-if="this.athletes.length <= 16" />
          <athlete-sm :athlete="athleteInSeat(sp.seat)" v-else />
        </template>
        <template v-else>
          <div class="w-full bg-white"></div>
        </template>
      </div>
    </chart>

    <div class="overflow-clip flex items-center justify-center">
      <div class="h-24 overflow-clip grid grid-cols-1 gap-2 pending-athlete-list">
        <athlete
          v-for="athlete in pendingAthletes"
          :athlete="athlete"
          class="w-[600px] h-24 rounded-lg p-1"
          :id="`athlete-${athlete.seat}`"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "./Chart.vue";
import { ref, onMounted, nextTick } from "vue";
import Athlete from "./Athlete.vue";
import AthleteSm from "./AthleteSm.vue";

const ANIM_MS = 1000;
const ANIM_BETWEEN_MS = 1000;

export default {
  name: "Player",
  components: {
    Athlete,
    AthleteSm,
    Chart,
  },
  props: {
    athletes: {
      required: true,
      type: Array,
    },
  },
  setup(props) {
    // begin transition: @keyframe scale and move to screen center
    // end transition @keyframe scale and move to the assigned seat
    // get refs of the seat element for its coordination
    // destroy the athlete item once placed
    // advance
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
    scale() {
      if (this.athletes.length <= 8) return 2;
      if (this.athletes.length >= 32) return 0.5;
      if (this.athletes.length >= 16) return 0.5;
      return 1;
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

<style>
/**
--translateX: 0;
--translateY: 0;
 */
@keyframes assign-player {
  0% {
    transform-origin: 50% 50%;
    position: fixed;
  }

  50% {
    transform: translateX(50%) translateY(-40vh) scale(1.25);
  }

  100% {
    transform: translateX(var(--translateX)) translateY(var(--translateY)) scale(1);
  }
}

.pending-athlete-list > * {
  @apply bg-white;
}
</style>
