<template>
  <ProgramLayout :competition="competition">
    <a-page-header title="Draw">
      <template #extra>
        <a-button type="link" v-if="competition.status > COMPETITION_STATUS.seat_locked">
          lock
        </a-button>
        <a-button
          type="primary"
          class="bg-blue-500"
          @click="lockSeat"
          v-if="
            competition.status === COMPETITION_STATUS.program_arranged && allProgramsDrew
          "
        >
          Lock draw
        </a-button>
        <span></span>
      </template>

      <template #tags>
        <a-tag color="success" v-if="allProgramsDrew"> All programs drew </a-tag>
      </template>
    </a-page-header>

    <template v-if="competition.status >= COMPETITION_STATUS.program_arranged">
      <div class="fixed right-16 bottom-16 z-10">
        <a
          :href="route('manage.competition.athletes.draw-screen', competition.id)"
          v-if="!isRunning"
          target="_blank"
        >
          <a-button
            type="primary"
            shape="round"
            size="large"
            class="!shadow-lg bg-blue-500"
          >
            <template #icon>
              <PoweroffOutlined />
            </template>
            draw screen
          </a-button>
        </a>
        <a-button
          type="primary"
          danger
          shape="round"
          size="large"
          v-else
          class="!shadow-lg"
          @click="closeDisplay"
        >
          <template #icon>
            <PoweroffOutlined />
          </template>
          close draw screen
        </a-button>
      </div>

      <div class="px-6">
        <a-alert
          type="info"
          show-icon
          message="all programs drew"
          description="If there are no issues with the lottery result, please lock the draw."
          v-if="
            allProgramsDrew && competition.status === COMPETITION_STATUS.program_arranged
          "
        />

        <a-alert
          type="success"
          show-icon
          message="The lottery result has been locked."
          description="The lottery result has been locked."
          v-if="competition.status > COMPETITION_STATUS.seat_locked"
        />
      </div>

      <div class="p-6 flex flex-col lg:flex-row gap-4">
        <div class="w-full">
          <a-card
            :active-tab-key="activeGender"
            :tab-list="genderTabList"
            @tabChange="(key) => (activeGender = key)"
          >
            <a class="hidden text-blue-500"></a>
            <a-tabs v-model:active-key="activeCategoryId" tab-position="left">
              <a-tab-pane
                v-for="category in availableCategories"
                :tab="category.name"
                :key="category.id"
              >
                <a-list :data-source="programsByGenderCategory">
                  <template #renderItem="{ item }">
                    <a-list-item
                      class="p-4"
                      :class="{ '!text-blue-500': activeProgramId === item.id }"
                      @click="activeProgramId = item.id"
                      :data-status="item.status"
                    >
                      <div class="flex">
                        <a-tag v-if="item.status > 0" color="success"
                          >Finish</a-tag
                        >
                        <a-tag v-else color="processing">Pending draw</a-tag>
                        {{ item.weight_code }}
                      </div>
                    </a-list-item>
                  </template>
                </a-list>
              </a-tab-pane>
            </a-tabs>
          </a-card>
        </div>

        <div class="shrink-0 w-2/3 flex items-center justify-center">
          <a-empty v-if="!activeProgramId">
            <template #description>
              <h3 class="text-lg text-slate-500 font-bold">Program not yet selected</h3>
              <p class="text-slate-500">Please select a program from the left</p>
            </template>
          </a-empty>
          <div v-else>
            <a-card>
              <template #title>
                {{ activeProgram.name }}
                共{{ athletes.length }}人
              </template>
              <template #extra>
                <div class="flex gap-3">
                  <template v-if="competition.status < COMPETITION_STATUS.seat_locked">
                    <a-button
                      type="link"
                      danger
                      @click="draw"
                      v-if="activeProgram.status > 0"
                    >
                      Redraw
                    </a-button>
                    <a-button type="primary" class="bg-blue-500" @click="draw" v-else>
                      Draw
                    </a-button>
                  </template>
                  <!-- <a
                    :href="
                      route('admin.competition.programs.brackets-pdf', [
                        competition.id,
                        activeProgram.id,
                      ])
                    "
                    target="_blank"
                  >
                    <a-button type="link"> download </a-button>
                  </a> -->
                </div>
              </template>

              <a-list
                :loading="athletes.length === 0"
                :data-source="padAthleteList"
                class="athlete-list"
              >
                <template #renderItem="{ item }">
                  <div class="rounded border px-4 py-2 overflow-clip relative flex">
                    <div
                      v-if="item.seed"
                      class="bg-yellow-500 absolute transform -rotate-45 w-12 h-6 -left-4 -top-4"
                    ></div>
                    <div class="flex items-center text-xl mr-4">
                      {{ item.seat }}
                    </div>
                    <div v-if="item.athlete">
                      <div>{{ item.athlete.name }}</div>
                      <div>{{ item.athlete.team.abbreviation }}</div>
                      <div v-if="competition.is_language_secondary_enabled">
                        {{ item.name_secondary }}
                      </div>
                    </div>
                    <div v-else>Bye</div>
                  </div>
                </template>
              </a-list>
            </a-card>
          </div>
        </div>
      </div>

      <div class="!fixed !bottom-16 w-full left-0 flex" v-if="isRunning">
        <div class="mx-auto shadow-lg !rounded-2xl bg-white dark:bg-neutral-900 p-1 px-6">
          <div class="flex gap-2">
            <a-button
              type="link"
              class="control-button"
              @click="sendAction('showGroupName')"
              :disabled="!activeProgram"
            >
              <template #icon>
                <InfoCircleOutlined />
              </template>

              Group information
            </a-button>
            <a-button
              type="link"
              class="control-button"
              @click="sendAction('showNameList')"
              :disabled="!activeProgram"
            >
              <template #icon>
                <FileTextOutlined />
              </template>

              Show list
            </a-button>
            <a-button
              type="link"
              class="control-button"
              @click="sendAction('draw')"
              :disabled="!activeProgram || nowActive !== 'showNameList'"
            >
              <template #icon>
                <PlayCircleOutlined />
              </template>

              Start drawing
            </a-button>
            <a-button
              type="link"
              class="control-button"
              @click="sendAction('showCover')"
              :disabled="!activeProgram"
            >
              <template #icon>
                <UndoOutlined />
              </template>

              Show cover
            </a-button>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="p-6">
        <a-card>
          <a-empty>
            <template #description>
              <h3 class="font-bold text-lg">no schedule</h3>
              <p>no schedule hint</p>
              <inertia-link
                :href="route('manage.competition.programs.index', competition.id)"
              >
                <a-button type="primary" class="bg-blue-500">
                  no schedule action
                </a-button>
              </inertia-link>
            </template>
          </a-empty>
        </a-card>
      </div>
    </template>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { weightParser } from "@/Utils/weightParser.js";
import {
  PoweroffOutlined,
  PlayCircleOutlined,
  InfoCircleOutlined,
  FileTextOutlined,
  UndoOutlined,
} from "@ant-design/icons-vue";
import { COMPETITION_STATUS } from "@/constants.js";
import { uniqBy } from "lodash";
import { ref } from "vue";

export default {
  name: "DrawControl",
  components: {
    ProgramLayout,
    PoweroffOutlined,
    InfoCircleOutlined,
    FileTextOutlined,
    UndoOutlined,
    PlayCircleOutlined,
  },
  props: {
    competition: {
      type: Object,
      required: true,
    },
    programs: {
      type: Array,
      required: true,
    },
  },
  setup() {
    const bc = new BroadcastChannel("draw");

    const isRunning = ref(false);

    // check if draw is running on setup
    bc.postMessage({ message: "ping" });

    bc.onmessage = (e) => {
      console.debug(e);
      switch (e.data.message) {
        case "pong":
          isRunning.value = true;
          return;
        case "disconnect":
          isRunning.value = false;
          return;
      }
    };

    return {
      COMPETITION_STATUS,
      isRunning,
      bc,
    };
  },
  data() {
    return {
      activeGender: "M",
      activeCategoryId: null,
      activeProgramId: null,
      athletes: [],
      nowActive: "",
      showResult: false,
    };
  },
  computed: {
    programsByGender() {
      return (gender) =>
        this.programs.filter(
          (program) => weightParser(program.weight_code).gender === gender
        );
    },
    availableGenders() {
      return this.programs.reduce((acc, program) => {
        acc.add(weightParser(program.weight_code).gender);
        return acc;
      }, new Set());
    },
    availableCategories() {
      return uniqBy(
        this.programsByGender(this.activeGender).map(
          (program) => program.competition_category
        ),
        "id"
      );
    },
    programsByGenderCategory() {
      return this.programs.filter(
        (program) =>
          weightParser(program.weight_code).gender === this.activeGender &&
          program.competition_category.id === this.activeCategoryId
      );
    },
    activeProgram() {
      this.athletes = [];
      this.loadProgram();
      return this.programs.find((program) => program.id === this.activeProgramId);
    },
    genderTabList() {
      return [...this.availableGenders].map((gender) => ({
        tab: gender,
        key: gender,
      }));
    },
    allProgramsDrew() {
      return this.programs.every((program) => program.status > 0);
    },
    padAthleteList() {
      const athletes = [...this.athletes];

      if (this.activeProgram.status > 0) {
        console.log("activeProgram");
        const empty = {};

        athletes
          .sort((a, b) => a.seat - b.seat)
          .sort((a, b) => {
            if (b.seat - a.seat > 1) {
              empty[a.seat] = b.seat - a.seat;
            }

            return a.seat - b.seat;
          });

        Object.keys(empty).forEach((index) => {
          index = parseInt(index);
          for (let i = 1; i < empty[index]; i++) {
            athletes.splice(index + i - 1, 0, {
              seat: index + i,
              athlete: null,
            });
          }
        });

        const pad = this.activeProgram.chart_size - athletes.length;
        for (let i = 0; i < pad; i++) {
          athletes.push({
            seat: athletes.length + 1,
            athlete: null,
          });
        }
      }

      return athletes;
    },
  },
  methods: {
    loadProgram() {
      if (!this.activeProgramId) return;
      window.axios
        .get(
          route("manage.competition.programs.show", [
            this.competition.id,
            this.activeProgramId,
          ])
        )
        .then(({ data }) => {
          console.log(
            data.program_athletes.sort((a, b) => a.seat - b.seat).map((x) => x.athlete)
          );
          this.program = data.program;
          this.athletes = data.program_athletes.sort((a, b) => a.seat - b.seat);
        });
    },
    draw() {
      // this.activeProgram.status = 1
      // TODO: handle draw
      return window.axios
        .post(
          route("manage.competition.program.draw", [
            this.competition.id,
            this.activeProgramId,
          ])
        )
        .then(({ data }) => {
          this.activeProgram.status = 1;
          this.athletes = data.athletes;
        });
    },
    closeDisplay() {
      this.bc.postMessage({
        message: "close",
      });
    },
    sendAction(action) {
      console.log("sending action", action);
      switch (action) {
        case "showCover":
          this.nowActive = "showCover";
          this.bc.postMessage({
            message: "showCover",
          });
          break;
        case "showNameList":
          this.nowActive = "showNameList";
          this.bc.postMessage({
            message: "showNameList",
            payload: JSON.stringify({
              athletes: this.athletes,
              program: this.activeProgram,
            }),
          });
          break;
        case "showGroupName":
          this.nowActive = "showGroupName";
          this.bc.postMessage({
            message: "showGroupName",
            payload: JSON.stringify({
              program: this.activeProgram,
              athletes: this.athletes,
            }),
          });
          break;
        case "draw":
          this.nowActive = "draw";
          this.handleDraw();
          break;
      }
    },
    async handleDraw() {
      await this.draw();
      this.bc.postMessage({
        message: "draw",
        payload: JSON.stringify({
          program: this.activeProgram,
          athletes: this.athletes,
          bouts: this.activeProgram.bouts,
        }),
      });
    },
    clearDraw() {
      // TODO: clear draw result
    },
    lockSeat() {
      this.$inertia.post(
        route("manage.competition.program.lock-seat", [this.competition.id])
      );
    },
  },
};
</script>

<style scoped lang="less">
:deep(.athlete-list .ant-list-items) {
  @apply grid;
  @apply grid-cols-1;
  @apply lg:grid-cols-4;
  @apply gap-3;
}
.ant-tabs-nav-list {
  @apply w-16;
}

.control-button {
  @apply flex;
  @apply flex-col;
  @apply items-center;
  @apply justify-center;
  @apply hover:bg-gray-100;
  @apply dark:hover:bg-gray-800;
  @apply w-20;
  @apply !h-20;
  @apply rounded-lg;
  height: unset;

  & > .anticon {
    @apply mb-2;

    :deep(svg) {
      @apply w-6;
      @apply h-6;
    }

    :deep(& + span) {
      @apply ml-0;
      @apply font-medium;
      @apply text-xs;
    }
  }
}
</style>
