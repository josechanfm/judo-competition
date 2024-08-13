<template>
  <ProgramLayout>
    <a-page-header title="抽籤">
      <template #extra>
        <a-button type="link" v-if="competition.status > COMPETITION_STATUS.seat_locked">
          lock
        </a-button>
        <a-button
          type="primary"
          @click="lockSeat"
          v-if="
            competition.status === COMPETITION_STATUS.program_arranged && allProgramsDrew
          "
        >
          鎖定抽籤
        </a-button>
        <span></span>
      </template>

      <template #tags>
        <a-tag color="success" v-if="allProgramsDrew"> 所有組別完成抽籤 </a-tag>
      </template>
    </a-page-header>

    <template v-if="competition.status >= COMPETITION_STATUS.program_arranged">
      <div class="fixed right-16 bottom-16 z-10">
        <a
          :href="route('manage.competition.athletes.draw-screen', competition.id)"
          v-if="!isRunning"
          target="_blank"
        >
          <a-button type="primary" shape="round" size="large" class="!shadow-lg">
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
          message="所有組別完成抽籤"
          description="如果抽籤結果沒有問題請鎖定抽籤"
          v-if="
            allProgramsDrew && competition.status === COMPETITION_STATUS.program_arranged
          "
        />

        <a-alert
          type="success"
          show-icon
          message="抽籤順序已經鎖定"
          description="抽籤順序已經鎖定"
          v-if="competition.status > COMPETITION_STATUS.seat_locked"
        />
      </div>

      <div class="p-6 flex flex-col lg:flex-row gap-4">
        <div class="w-full lg:w-96">
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
                      <a-tag v-if="item.status > 0" color="success">已抽籤</a-tag>
                      <a-tag v-else color="processing">待抽籤</a-tag>
                      {{ item.weight_code }}
                    </a-list-item>
                  </template>
                </a-list>
              </a-tab-pane>
            </a-tabs>
          </a-card>
        </div>

        <div class="flex-1">
          <a-empty v-if="!activeProgramId">
            <template #description>
              <h3 class="text-lg text-slate-500 font-bold">尚未選擇組別</h3>
              <p class="text-slate-500">請從左邊選擇一個組別</p>
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
                      重新抽籤
                    </a-button>
                    <a-button type="primary" @click="draw" v-else> 抽籤 </a-button>
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
                      <div>{{ item.athlete.name_zh }}</div>
                      <div>{{ item.athlete.team.name_zh }}</div>
                      <div v-if="competition.is_language_secondary_enabled">
                        {{ item.name_pt }}
                      </div>
                    </div>
                    <div v-else>輪空</div>
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

              組別資訊
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

              顯示名單
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

              開始抽籤
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

              顯示封面
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
                <a-button type="primary"> no schedule action </a-button>
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
          this.program = data.program;
          this.athletes = data.program.program_athletes.sort((a, b) => a.seat - b.seat);
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
        route("manage.competition.programs.lock-seat", [this.competition.id])
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
