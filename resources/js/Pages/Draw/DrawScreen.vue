<template>
  <div
    class="w-screen h-screen bg-slate-300 fixed"
    :style="`background-image: url('${draw.background}'); background-size: cover`"
  >
    <audio ref="audioPlayer" src="/assets/draw-music.mp3" loop></audio>
    <div class="flex justify-end p-2 mx-2">
      <button @click="playAudio" class="text-right" v-if="!playingAudio">
        <svg
          fill="#000000"
          width="20px"
          height="20px"
          viewBox="0 0 16 16"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M10 2c3.314 0 6 2.693 6 6 0 3.314-2.693 6-6 6v-2c2.205 0 4-1.79 4-4 0-2.205-1.79-4-4-4V2zm0 4c1.105 0 2 .888 2 2 0 1.105-.888 2-2 2V6zM0 3h2l6-3v16l-6-3H0V3zm2 2v6l4 2V3L2 5z"
            fill-rule="evenodd"
          />
        </svg>
      </button>
      <button @click="stopAudio" class="text-right" v-else>
        <svg
          width="20px"
          height="20px"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M10 5C10 3.34315 8.65686 2 7 2H5C3.34315 2 2 3.34315 2 5V19C2 20.6569 3.34315 22 5 22H7C8.65686 22 10 20.6569 10 19V5ZM8 5C8 4.44772 7.55229 4 7 4H5C4.44772 4 4 4.44772 4 5V19C4 19.5523 4.44772 20 5 20H7C7.55229 20 8 19.5523 8 19V5Z"
            fill="#0F0F0F"
          />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M22 5C22 3.34315 20.6569 2 19 2H17C15.3431 2 14 3.34315 14 5V19C14 20.6569 15.3431 22 17 22H19C20.6569 22 22 20.6569 22 19V5ZM20 5C20 4.44772 19.5523 4 19 4H17C16.4477 4 16 4.44772 16 5V19C16 19.5523 16.4477 20 17 20H19C19.5523 20 20 19.5523 20 19V5Z"
            fill="#0F0F0F"
          />
        </svg>
      </button>
    </div>
    <transition>
      <template v-if="currentStage === Stage.EMPTY">
        <div
          class="w-full h-full"
          :style="`background-image: url('${draw.cover}'); background-size: cover;`"
        ></div>
      </template>
    </transition>

    <transition>
      <template v-if="currentStage === Stage.DRAW">
        <div class="p-16 flex flex-col">
          <div class="flex bg-white/75 rounded-lg items-center p-4 backdrop-blur">
            <div v-if="competition.logo_url != ''">
              <img :src="competition.logo_url" class="h-24 w-auto" />
            </div>
            <div
              class="flex-1 text-neutral-800 flex flex-col"
              :class="{ 'ml-12': competition.logo_url !== '' }"
            >
              <h1 class="text-4xl font-extrabold mb-2">{{ competition.name }}</h1>
              <h1
                class="text-4xl font-extrabold mb-0"
                v-if="competition.is_language_secondary_enabled"
              >
                {{ competition.name_secondary }}
              </h1>
            </div>
            <div class="text-right">
              <h2 class="text-4xl font-bold mb-2">
                {{ stagePayload.program.competition_category.name }}
                {{ stagePayload.program.name }}
              </h2>
              <h3 class="text-4xl font-medium mb-0">
                共 {{ stagePayload.athletes.length }} 人
              </h3>
            </div>
          </div>
          <div class="w-full justify-center flex-1 relative overflow-hidden mt-16">
            <player
              :athletes="stagePayload.athletes"
              :key="lastAction"
              v-if="stagePayload.program.contest_system !== 'rrb'"
            />
            <draw-rrb v-else :athletes="stagePayload.athletes" :key="lastAction" />
          </div>
        </div>
      </template>
    </transition>

    <transition>
      <template v-if="currentStage === Stage.GROUP_NAME">
        <div class="flex items-center p-16">
          <div>
            <h2 class="text-7xl font-bold leading-relaxed m-0">
              {{ stagePayload.program.competition_category.name }}
              {{ stagePayload.program.name }}
            </h2>
            <h2 class="text-4xl font-medium leading-relaxed m-0">
              {{ stagePayload.program.weight_code }}
            </h2>
            <h3 class="text-3xl text-gray-800 font-medium leading-relaxed m-0">
              共 <span class="font-bold">{{ stagePayload.athletes.length }}</span> 人
            </h3>
          </div>
        </div>
      </template>
    </transition>

    <transition>
      <template v-if="currentStage === Stage.NAME_LIST">
        <div class="flex p-16 flex-col justify-start">
          <div class="flex bg-white/75 rounded-lg items-center p-4 backdrop-blur">
            <div v-if="competition.logo_url != ''">
              <img :src="competition.logo_url" class="h-24 w-auto" />
            </div>
            <div
              class="flex-1 text-neutral-800 flex flex-col"
              :class="{ 'ml-12': competition.logo_url !== '' }"
            >
              <h1 class="text-4xl font-extrabold mb-2">{{ competition.name }}</h1>
              <h1
                class="text-4xl font-extrabold mb-0"
                v-if="competition.is_language_secondary_enabled"
              >
                {{ competition.name_secondary }}
              </h1>
            </div>

            <div class="text-right">
              <h2 class="text-4xl font-bold mb-2">
                {{ stagePayload.program.competition_category.name
                }}{{ stagePayload.program.weight_code }}
              </h2>
              <h3 class="text-4xl font-medium mb-0">
                共 {{ stagePayload.athletes.length }} 人
              </h3>
            </div>
          </div>

          <div class="flex-1">
            <div class="mt-16 grid grid-cols-2 w-full gap-6">
              <div
                v-for="athlete in nameListSlice"
                :key="athlete.id"
                class="rounded-lg bg-white/75 overflow-clip p-4 relative backdrop-blur"
              >
                <div class="text-4xl font-bold mb-2">
                  <div
                    class="absolute bg-yellow-500 rotate-45 h-16 w-16 -right-8 -top-8 transform"
                    v-if="athlete.seed"
                  ></div>
                  {{ athlete.name_zh }}
                  <span>
                    <template v-if="competition.is_language_secondary_enabled">
                      {{ athlete.name_pt }}
                    </template>
                  </span>
                </div>
                <div class="text-4xl font-medium">
                  <span>{{ athlete.team.abbreviation }}</span>
                  - {{ athlete.team.name_zh }}
                  <template v-if="competition.is_language_secondary_enabled">
                    - {{ athlete.team.name_pt }}
                  </template>
                </div>
              </div>
            </div>
          </div>

          <div class="flex gap-4 text-3xl font-bold items-center mt-16">
            <span class="inline-block bg-yellow-500 rounded-full w-8 h-8"></span>
            種子選手 / Atleta Semente / Seeded Athlete
          </div>
        </div>
      </template>
    </transition>
  </div>
</template>

<script>
import { ref, computed, watch } from "vue";
import Player from "../Stages/DrawPlayerBracket.vue";
import DrawRrb from "../Stages/DrawRrb.vue";
import { chunk, shuffle, flatten } from "lodash";

const Stage = {
  EMPTY: "empty",
  GROUP_NAME: "groupName",
  NAME_LIST: "nameList",
  DRAW: "draw",
};
export default {
  components: {
    Player,
    DrawRrb,
  },
  provide() {
    return {
      language: this.language,
      competition: this.competition,
    };
  },
  mounted() {
    this.interval = setInterval(() => {
      this.language = (this.language + 1) % 2;
    }, 5000);
  },
  deactivated() {
    clearInterval(this.interval);
  },
  data() {
    return {
      interval: null,
      language: 0,
      audioSource: "/assets/draw-music.mp3",
      audioElement: null,
      playingAudio: false,
    };
  },
  setup() {
    const bc = new BroadcastChannel("draw");
    const currentStage = ref(Stage.EMPTY);
    const stagePayload = ref(null);
    const currentInterval = ref(null);
    const lastAction = ref(new Date());
    const counter = ref(0);
    const NAME_PER_PAGE = 8;
    const PAGE_DURATION = 3000;
    const nameListSlice = computed(() => {
      if (currentStage.value === Stage.NAME_LIST) {
        const offset = counter.value * NAME_PER_PAGE;
        return stagePayload.value.athletes.slice(offset, offset + NAME_PER_PAGE);
      }
    });

    const updateNameListCount = () => {
      // reset to first page if outbound
      if ((1 + counter.value) * NAME_PER_PAGE >= stagePayload.value.athletes.length) {
        counter.value = 0;
        return;
      }
      ++counter.value;
    };

    // sort athlete
    const handleSortAthletes = (athletes, size) => {
      // TODO: sort athletes
      const IJFSeededPosition = {
        8: [1, 5, 7, 3, 4, 8, 6, 2],
        16: [1, 9, 13, 5, 7, 15, 11, 3, 4, 12, 16, 8, 6, 14, 10, 2],
        32: [
          1,
          17,
          25,
          9,
          13,
          21,
          29,
          5,
          7,
          19,
          27,
          11,
          3,
          15,
          23,
          31,
          4,
          20,
          28,
          12,
          16,
          24,
          32,
          8,
          6,
          18,
          26,
          10,
          14,
          22,
          30,
          2,
        ],
        64: [
          1,
          33,
          49,
          17,
          25,
          41,
          57,
          9,
          13,
          37,
          53,
          21,
          29,
          45,
          61,
          5,
          7,
          35,
          51,
          19,
          27,
          43,
          59,
          11,
          3,
          31,
          47,
          15,
          23,
          39,
          55,
          63,
          4,
          32,
          48,
          20,
          28,
          44,
          60,
          12,
          16,
          36,
          52,
          24,
          32,
          48,
          64,
          8,
          6,
          34,
          50,
          18,
          26,
          42,
          58,
          10,
          14,
          38,
          54,
          22,
          30,
          46,
          62,
          2,
        ],
      };

      // 拿出所有種子選手（優先抽籤）
      const seededAthletes = athletes
        .filter((athlete) => athlete.seed)
        .sort((a, b) => {
          return (
            IJFSeededPosition[size].indexOf(b.seed) -
            IJFSeededPosition[size].indexOf(a.seed)
          );
        });

      console.debug("seeded athlete", seededAthletes);

      const nonSeedAthletes = athletes.filter((athlete) => !athlete.seed);

      console.debug("other athlete", nonSeedAthletes);

      return [...seededAthletes, ...nonSeedAthletes];
    };
    // after initialized, handle changes
    watch(currentStage, (newValue, oldValue) => {
      // clear any timing events
      counter.value = 0;
      clearInterval(currentInterval.value);
      switch (newValue) {
        case Stage.NAME_LIST:
          currentInterval.value = setInterval(updateNameListCount, PAGE_DURATION);
      }
    });
    bc.postMessage({ message: "pong" });
    // initialize data for next move
    bc.onmessage = (e) => {
      console.debug(e);
      console.log(JSON.parse(e.data.payload));
      switch (e.data.message) {
        case "ping":
          bc.postMessage({ message: "pong" });
          return;
        case "close":
          window.close();
          return;
        case "showGroupName":
          currentStage.value = Stage.GROUP_NAME;
          stagePayload.value = JSON.parse(e.data.payload);
          return;
        case "showNameList":
          counter.value = 0;
          stagePayload.value = JSON.parse(e.data.payload);
          currentStage.value = Stage.NAME_LIST;
          return;
        case "draw":
          stagePayload.value = JSON.parse(e.data.payload);
          // follow ijf sequence
          if (stagePayload.value.program.contest_system !== "rrb") {
            stagePayload.value.athletes = handleSortAthletes(
              stagePayload.value.athletes,
              stagePayload.value.program.chart_size
            );
          }
          console.log("stagePayload.value.athletes", stagePayload.value.athletes);
          currentStage.value = Stage.DRAW;
          return;
        case "showCover":
          stagePayload.value = null;
          currentStage.value = Stage.EMPTY;
          return;
      }

      lastAction.value = new Date();
    };
    window.addEventListener("beforeunload", () => {
      bc.postMessage({ message: "disconnect" });
    });
    return {
      bc,
      lastAction,
      Stage,
      currentStage,
      stagePayload,
      nameListSlice,
    };
  },
  props: {
    competition: {
      type: Object,
      required: true,
    },
    draw: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    this.audioElement = this.$refs.audioPlayer;
  },
  methods: {
    playAudio() {
      this.playingAudio = true;
      this.audioElement.play();
    },
    stopAudio() {
      this.playingAudio = false;
      this.audioElement.pause();
      this.audioElement.currentTime = 0;
    },
  },
};
</script>

<style scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
