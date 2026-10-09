<template>
  <ProgramLayout :competition="competition">
    <a-page-header :title="$t('draw')">
      <template #extra>
        <template
          v-if="
            competition.status >= COMPETITION_STATUS.program_arranged &&
            competition.status < COMPETITION_STATUS.seat_locked
          "
        >
          <span class="mr-2 text-sm text-slate-500">{{ $t("draw_control.method") }}</span>
          <a-select
            v-model:value="drawMethod"
            :options="drawMethodOptions"
            class="w-44 mr-1"
            @change="onDrawMethodChange"
          />
          <a-tooltip :title="drawMethodHint" class="mr-2">
            <InfoCircleOutlined class="text-slate-400 cursor-help" />
          </a-tooltip>
          <a-button
            danger
            class="mr-2"
            :loading="resetAllLoading"
            :disabled="drawnProgramCount === 0"
            @click="confirmResetAll"
          >
            {{ $t("draw_control.reset_all") }}
            <template v-if="drawnProgramCount > 0">
              ({{ drawnProgramCount }})
            </template>
          </a-button>
        </template>
        <a-button type="link" v-if="competition.status > COMPETITION_STATUS.seat_locked">
          {{ $t("lock_seat") }}
        </a-button>
        <a-button
          class="mr-2"
          :loading="drawAllLoading"
          :disabled="pendingProgramCount === 0"
          @click="confirmDrawAll"
          v-if="competition.status === COMPETITION_STATUS.program_arranged"
        >
          {{ $t("draw_control.draw_all") }}
          <template v-if="pendingProgramCount > 0">
            ({{ pendingProgramCount }})
          </template>
        </a-button>
        <a-button
          type="primary"
          class="bg-blue-500"
          @click="lockSeat"
          v-if="
            competition.status === COMPETITION_STATUS.program_arranged && allProgramsDrew
          "
        >
          {{ $t("lock_seat") }}
        </a-button>
        <span></span>
      </template>

      <template #tags>
        <a-tag color="success" v-if="allProgramsDrew">
          {{ $t("all_programs_drew") }}
        </a-tag>
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
            {{ $t("draw_screen") }}
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
          {{ $t("close_draw_screen") }}
        </a-button>
      </div>

      <div class="px-6">
        <a-alert
          type="info"
          show-icon
          :message="$t('all_drew')"
          :description="$t('all_drew_description')"
          v-if="
            allProgramsDrew && competition.status === COMPETITION_STATUS.program_arranged
          "
        />

        <a-alert
          type="success"
          show-icon
          :message="$t('draw_control.locked')"
          :description="$t('draw_control.locked')"
          v-if="competition.status > COMPETITION_STATUS.seat_locked"
        />
      </div>

      <div class="p-6 flex flex-col xl:flex-row gap-4">
        <div class="w-full xl:flex-1">
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
                        <a-tag v-if="item.status > 0" color="success">{{ $t("finished") }}</a-tag>
                        <a-tag v-else color="processing">{{ $t("draw_control.pending_draw") }}</a-tag>
                        {{ item.weight_code }}
                      </div>
                    </a-list-item>
                  </template>
                </a-list>
              </a-tab-pane>
            </a-tabs>
          </a-card>
        </div>

        <div class="w-full xl:w-2/3 xl:shrink-0 flex items-center justify-center">
          <a-empty v-if="!activeProgramId">
            <template #description>
              <h3 class="text-lg text-slate-500 font-bold">
                {{ $t("draw_control.no_program_selected") }}
              </h3>
              <p class="text-slate-500">
                {{ $t("draw_control.select_program_hint") }}
              </p>
            </template>
          </a-empty>
          <div v-else>
            <a-card>
              <template #title>
                {{ activeProgram.name }}
                {{ $t("draw_control.athletes_count", { count: athletes.length }) }}
              </template>
              <template #extra>
                <div class="flex gap-3">
                  <template v-if="activeProgram.status > 0">
                  <a :href="route('manage.competition.program.generateOnlineTable', [
                      competition.id,
                      activeProgramId,
                    ])" 
                      target="_blank">
                    <a-button type="link" @click="download">{{ $t("draw_control.online_table") }}</a-button>
                  </a>
                  </template>
                  <template v-if="competition.status < COMPETITION_STATUS.seat_locked">
                    <div class="flex gap-3" v-if="activeProgram.status > 0">
                      <a-button type="link" danger @click="reset">
                        {{ $t("draw_control.reset") }}
                      </a-button>
                      <a-button type="link" danger @click="draw">
                        {{ $t("draw_control.redraw") }}
                      </a-button>
                    </div>
                    <a-button type="primary" class="bg-blue-500" @click="draw" v-else>
                      {{ $t("draw") }}
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
                      <div v-if="item.athlete.name">{{ item.athlete.name }}</div>
                      <div v-if="item.athlete.name_secondary">
                        {{ item.athlete.name_secondary }}
                      </div>
                      <div v-if="item.athlete.team">
                        {{ item.athlete.team.abbreviation || item.athlete.team.name }}
                      </div>
                    </div>
                    <div v-else>{{ $t("draw_control.bye") }}</div>
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

              {{ $t("draw_control.group_info") }}
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

              {{ $t("draw_control.show_list") }}
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

              {{ $t("draw_control.start_draw") }}
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

              {{ $t("draw_control.show_cover") }}
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
              <h3 class="font-bold text-lg">{{ $t("no_schedule") }}</h3>
              <p>{{ $t("no_schedule_hint") }}</p>
              <inertia-link
                :href="route('manage.competition.programs.index', competition.id)"
              >
                <a-button type="primary" class="bg-blue-500">
                  {{ $t("no_schedule_action") }}
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
  ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { COMPETITION_STATUS, PROGRAM_STATUS } from "@/constants.js";
import { Modal } from "ant-design-vue";
import { uniqBy } from "lodash";
import { createVNode, ref } from "vue";

// 抽籤方式：random = 一般抽籤、team_separated = 同隊分開（存在 localStorage）
const DRAW_METHODS = ["random", "team_separated"];
const DRAW_METHOD_STORAGE_KEY = "draw_method";

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
  created() {
    // 沿用上次選的抽籤方式
    const saved = window.localStorage.getItem(DRAW_METHOD_STORAGE_KEY);

    if (DRAW_METHODS.includes(saved)) {
      this.drawMethod = saved;
    }
  },
  data() {
    return {
      activeGender: "M",
      activeCategoryId: null,
      activeProgramId: null,
      athletes: [],
      nowActive: "",
      showResult: false,
      drawAllLoading: false,
      resetAllLoading: false,
      drawMethod: "random",
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
    pendingProgramCount() {
      return this.programs.filter(
        (program) => program.status === PROGRAM_STATUS.created
      ).length;
    },
    drawnProgramCount() {
      return this.programs.filter((program) => program.status > 0).length;
    },
    drawMethodOptions() {
      return [
        { value: "random", label: this.$t("draw_control.method.random") },
        {
          value: "team_separated",
          label: this.$t("draw_control.method.team_separated"),
        },
      ];
    },
    drawMethodHint() {
      return this.$t(`draw_control.method_hint.${this.drawMethod}`);
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
          ]),
          { method: this.drawMethod }
        )
        .then(({ data }) => {
          this.activeProgram.status = 1;
          this.athletes = data.athletes;
        });
    },
    reset() {
      return window.axios
        .post(
          route("manage.competition.program.reset", [
            this.competition.id,
            this.activeProgramId,
          ])
        )
        .then(({ data }) => {
          // 重設後座位會歸零，重新載入名單讓畫面同步
          if (this.activeProgram) {
            this.activeProgram.status = 0;
          }
          this.loadProgram();
        });
    },
    download(){
      return window.axios
        .get(
          route("manage.competition.program.generateOnlineTable", [
            this.competition.id,
            this.activeProgramId,
          ])
        )
        .then(({ data }) => {
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
    confirmDrawAll() {
      if (this.pendingProgramCount === 0) {
        this.$message.info(this.$t("draw_control.draw_all_none"));
        return;
      }

      Modal.confirm({
        title: this.$t("draw_control.draw_all"),
        content: this.$t("draw_control.draw_all_confirm", {
          count: this.pendingProgramCount,
        }),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => this.drawAll(),
      });
    },
    drawAll() {
      this.drawAllLoading = true;

      return window.axios
        .post(
          route("manage.competition.program.draw-all", [this.competition.id]),
          { method: this.drawMethod }
        )
        .then(({ data }) => {
          // 依伺服器回傳結果更新本地項目狀態，避免重新載入頁面
          data.programs.forEach((updated) => {
            const program = this.programs.find((p) => p.id === updated.id);
            if (program) program.status = updated.status;
          });

          if (this.activeProgramId) {
            this.loadProgram();
          }

          this.$message.success(
            this.$t("draw_control.draw_all_success", { count: data.drawn_count })
          );
        })
        .catch(() => {
          this.$message.error(this.$t("draw_control.draw_all_failed"));
        })
        .finally(() => {
          this.drawAllLoading = false;
        });
    },
    lockSeat() {
      this.$inertia.post(
        route("manage.competition.program.lock-seat", [this.competition.id])
      );
    },
    onDrawMethodChange(value) {
      window.localStorage.setItem(DRAW_METHOD_STORAGE_KEY, value);
    },
    confirmResetAll() {
      if (this.drawnProgramCount === 0) {
        this.$message.info(this.$t("draw_control.reset_all_none"));
        return;
      }

      Modal.confirm({
        title: this.$t("draw_control.reset_all"),
        content: this.$t("draw_control.reset_all_confirm", {
          count: this.drawnProgramCount,
        }),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        okButtonProps: { danger: true },
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => this.resetAll(),
      });
    },
    resetAll() {
      this.resetAllLoading = true;

      return window.axios
        .post(
          route("manage.competition.program.reset-all", [this.competition.id])
        )
        .then(({ data }) => {
          // 依伺服器回傳結果更新本地項目狀態，避免重新載入頁面
          data.programs.forEach((updated) => {
            const program = this.programs.find((p) => p.id === updated.id);
            if (program) program.status = updated.status;
          });

          this.athletes = [];

          if (this.activeProgramId) {
            this.loadProgram();
          }

          this.$message.success(
            this.$t("draw_control.reset_all_success", { count: data.reset_count })
          );
        })
        .catch(() => {
          this.$message.error(this.$t("draw_control.reset_all_failed"));
        })
        .finally(() => {
          this.resetAllLoading = false;
        });
    },
  },
};
</script>

<style scoped lang="less">
:deep(.athlete-list .ant-list-items) {
  @apply grid;
  @apply grid-cols-1;
  @apply sm:grid-cols-2;
  @apply lg:grid-cols-3;
  @apply xl:grid-cols-4;
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
