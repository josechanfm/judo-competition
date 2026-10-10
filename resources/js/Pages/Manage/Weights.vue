<template>
  <inertia-head :title="$t('menu.competitions.athletes_weight_in')" />

  <ProgramLayout :competition="competition">
    <a-page-header :title="$t('menu.competitions.athletes_weight_in')">
      <template #tags>
        <template v-if="competitionDrawn && program.id">
          <a-tag v-if="hasPendingAthletes" color="warning">
            {{ $t("weights.status.not_weighed") }}
          </a-tag>
          <a-tag v-else-if="isLocked" color="success">
            {{ $t("weights.status.locked") }}
          </a-tag>
          <a-tag v-else color="processing">
            {{ $t("weights.status.weighed") }}
          </a-tag>
        </template>
      </template>
      <template #extra>
        <a-space v-if="competitionDrawn" :size="8" wrap>
          <a-button :href="exportWeightsUrl" target="_blank">
            <template #icon><DownloadOutlined /></template>
            {{ $t("weights.export_data") }}
          </a-button>
          <a-button
            type="primary"
            class="bg-blue-500"
            @click="importModalOpen = true"
          >
            <template #icon><UploadOutlined /></template>
            {{ $t("weights.import_data") }}
          </a-button>
          <a-button
            type="primary"
            class="bg-blue-500"
            :href="downloadWeighInTableUrl"
            target="_blank"
          >
            <template #icon><DownloadOutlined /></template>
            {{ $t("weights.download_table") }}
          </a-button>
          <a-button
            v-if="!allProgramsLocked"
            type="primary"
            class="bg-blue-500"
            @click="lockAllWeighIn"
          >
            <template #icon><LockOutlined /></template>
            {{ $t("weights.lock_all") }}
          </a-button>
          <a-button v-if="hasLockedProgram" @click="cancelLockAllWeighIn">
            <template #icon><UnlockOutlined /></template>
            {{ $t("weights.cancel_lock_all") }}
          </a-button>
          <a-button danger @click="resetAllWeights">
            <template #icon><UndoOutlined /></template>
            {{ $t("weights.reset_all") }}
          </a-button>
        </a-space>
      </template>
    </a-page-header>

    <template v-if="competitionDrawn">
      <div class="mx-6 flex flex-col gap-4 pb-6">
        <!-- 過磅設定 -->
        <a-card class="shadow-md" :body-style="{ padding: '20px 24px' }">
          <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="flex flex-wrap items-end gap-8">
              <div>
                <div class="mb-2 text-sm font-medium text-slate-500">
                  {{ $t("category") }}
                </div>
                <a-radio-group
                  v-model:value="categoryId"
                  button-style="solid"
                  @change="onChangeCategory"
                >
                  <a-radio-button
                    v-for="category in competition.categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </a-radio-button>
                </a-radio-group>
              </div>
              <div class="min-w-[220px]">
                <div class="mb-2 text-sm font-medium text-slate-500">
                  {{ $t("athletes.program") }}
                </div>
                <a-select
                  v-model:value="programId"
                  :options="programOptions"
                  :placeholder="$t('weights.select_program')"
                  :disabled="!categoryId"
                  class="w-full min-w-[200px]"
                  @change="onChangeProgram"
                />
              </div>
            </div>

            <div v-if="program.id" class="flex items-center gap-3">
              <a-button danger @click="resetProgramWeights">
                <template #icon><UndoOutlined /></template>
                {{ $t("weights.reset_program") }}
              </a-button>
              <a-button
                v-if="!hasPendingAthletes && !isLocked"
                type="primary"
                class="bg-blue-500"
                @click="lockProgramWeighIn"
              >
                <template #icon><LockOutlined /></template>
                {{ $t("weights.lock") }}
              </a-button>
              <a-button v-if="isLocked" @click="cancelLockWeighIn">
                <template #icon><UnlockOutlined /></template>
                {{ $t("weights.cancel_lock") }}
              </a-button>
            </div>
          </div>
        </a-card>
        <!-- 過磅名單 -->
        <a-card v-if="program.id" class="shadow-md" :body-style="{ padding: 0 }">
          <div
            class="flex flex-wrap items-center justify-between gap-3 border-b px-6 py-4"
          >
            <div>
              <div class="text-base font-semibold text-slate-800">
                {{ programTitle }}
              </div>
              <div class="text-sm text-slate-400">
                {{ $t("weights.list_hint") }}
              </div>
            </div>
            <a-space :size="4">
              <a-tag color="success">
                {{ $t("weights.summary.passed", { count: passedCount }) }}
              </a-tag>
              <a-tag color="error">
                {{ $t("weights.summary.failed", { count: failedCount }) }}
              </a-tag>
              <a-tag>
                {{ $t("weights.summary.pending", { count: pendingCount }) }}
              </a-tag>
            </a-space>
          </div>
          <a-table
            :data-source="programAthletes"
            :columns="columns"
            :row-key="(record) => record.id"
            :row-class-name="rowClassName"
            :pagination="false"
            size="middle"
          >
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'athlete'">
                <div class="font-medium text-slate-800">
                  {{ record.athlete?.name }}
                </div>
                <div
                  v-if="record.athlete?.name_secondary"
                  class="text-xs text-slate-400"
                >
                  {{ record.athlete.name_secondary }}
                </div>
              </template>
              <template v-else-if="column.key === 'result'">
                <a-tag v-if="record.is_weight_passed == 1" color="success">
                  <CheckCircleOutlined class="mr-1" />
                  {{ $t("weights.result.passed") }}
                </a-tag>
                <a-tag v-else-if="record.is_weight_passed == 0" color="error">
                  <CloseCircleOutlined class="mr-1" />
                  {{ $t("weights.result.failed") }}
                </a-tag>
                <a-tag v-else>
                  <QuestionCircleOutlined class="mr-1" />
                  {{ $t("weights.result.pending") }}
                </a-tag>
              </template>
              <template v-else-if="column.key === 'weight'">
                <div class="flex items-center justify-end gap-3">
                  <template v-if="!record.confirm">
                    <a-input-number
                      v-model:value="record.weight"
                      :min="0"
                      :max="999"
                      :precision="2"
                      :step="0.01"
                      class="w-28"
                    />
                    <span class="text-slate-400">kg</span>
                    <a-tooltip :title="$t('weights.pass_tooltip')">
                      <a-button
                        type="primary"
                        size="small"
                        @click="passWeight(record, 1)"
                      >
                        <template #icon><CheckOutlined /></template>
                      </a-button>
                    </a-tooltip>
                    <a-tooltip :title="$t('weights.fail_tooltip')">
                      <a-button
                        danger
                        size="small"
                        @click="passWeight(record, 0)"
                      >
                        <template #icon><CloseOutlined /></template>
                      </a-button>
                    </a-tooltip>
                  </template>
                  <span v-else class="font-semibold">
                    {{ record.weight }} kg
                  </span>
                </div>
              </template>
              <template v-else-if="column.key === 'operation'">
                <a-tooltip :title="$t('weights.reset_single')">
                  <a-button
                    danger
                    size="small"
                    :disabled="!hasWeighInData(record)"
                    @click="resetAthlete(record)"
                  >
                    <template #icon><UndoOutlined /></template>
                  </a-button>
                </a-tooltip>
              </template>
              <template v-else>
                {{ record[column.dataIndex] }}
              </template>
            </template>
          </a-table>
        </a-card>

        <!-- 尚未選擇項目 -->
        <a-card v-else class="shadow-md">
          <a-empty>
            <template #description>
              <div class="font-medium text-slate-600">
                {{ $t("weights.select_program_hint") }}
              </div>
              <div class="mt-1 text-sm text-slate-400">
                {{ $t("weights.select_program_hint_description") }}
              </div>
            </template>
          </a-empty>
        </a-card>
      </div>
    </template>

    <template v-else>
      <div class="p-6">
        <a-card class="shadow-md">
          <a-empty>
            <template #description>
              <h3 class="text-lg font-bold">{{ $t("draw_not_confirmed") }}</h3>
              <p>{{ $t("draw_not_confirmed_hint") }}</p>
              <inertia-link
                :href="
                  route('manage.competition.athletes.drawControl', competition.id)
                "
              >
                <a-button type="primary" class="bg-blue-500">
                  {{ $t("weights.go_to_draw") }}
                </a-button>
              </inertia-link>
            </template>
          </a-empty>
        </a-card>
      </div>
    </template>

    <!-- 滙入過磅資料（Excel） -->
    <ImportWeighInModal
      v-model:open="importModalOpen"
      :competition-id="competition.id"
      :export-url="exportWeightsUrl"
      @imported="onWeightsImported"
    />
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import ImportWeighInModal from "@/Pages/Manage/ImportWeighInModal.vue";
import { Modal } from "ant-design-vue";
import {
  CheckCircleOutlined,
  CloseCircleOutlined,
  CheckOutlined,
  CloseOutlined,
  DownloadOutlined,
  LockOutlined,
  QuestionCircleOutlined,
  UnlockOutlined,
  UploadOutlined,
  UndoOutlined,
} from "@ant-design/icons-vue";
import { weightGroupLabel as weightGroupLabelUtil } from "@/Utils/weightParser";
import { COMPETITION_STATUS } from "@/constants.js";

export default {
  name: "Weights",
  components: {
    ProgramLayout,
    ImportWeighInModal,
    CheckCircleOutlined,
    CloseCircleOutlined,
    CheckOutlined,
    CloseOutlined,
    DownloadOutlined,
    LockOutlined,
    QuestionCircleOutlined,
    UnlockOutlined,
    UploadOutlined,
    UndoOutlined,
  },
  props: ["competition", "programs"],
  // programs: {
  //   type: Object,
  //   required: true,
  // },
  computed: {
    // 抽籤完成後才可過磅
    competitionDrawn() {
      return this.competition.status >= COMPETITION_STATUS.seat_locked;
    },
    programAthletes() {
      return this.program?.program_athletes ?? [];
    },
    // 依組別篩選出來的項目，標籤用公斤級顯示名稱（值仍是 program id）
    programOptions() {
      return this.select_programs.map((program) => ({
        value: program.id,
        label: this.weightGroupLabel(program.weight_code),
      }));
    },
    // 標題：公斤級 + 組別
    programTitle() {
      return this.program?.id ? this.programLabel(this.program) : "";
    },
    // 是否有任何項目已鎖定（決定「取消鎖定全部」是否顯示）
    hasLockedProgram() {
      return (this.programs ?? []).some((program) =>
        this.isProgramLocked(program)
      );
    },
    // 是否所有項目都已鎖定（決定「鎖定全部」是否顯示）
    allProgramsLocked() {
      const list = this.programs ?? [];
      return list.length > 0 && list.every((p) => this.isProgramLocked(p));
    },
    // 尚未完成過磅的項目（與後端 weightsLockAll 的判斷一致）
    incompletePrograms() {
      return (this.programs ?? []).filter(
        (program) => this.unweighedAthletes(program).length > 0
      );
    },
    downloadWeighInTableUrl() {
      return route("generate.all.weighIn.table", this.competition.id);
    },
    exportWeightsUrl() {
      return route("manage.competition.athletes.weights.export", this.competition.id);
    },
    hasPendingAthletes() {
      return this.programAthletes.some(
        (athlete) => !athlete.confirm && athlete.is_weight_passed == null
      );
    },
    isLocked() {
      return this.programAthletes.some((athlete) => athlete.confirm == 1);
    },
    passedCount() {
      return this.programAthletes.filter((a) => a.is_weight_passed == 1).length;
    },
    failedCount() {
      return this.programAthletes.filter((a) => a.is_weight_passed == 0).length;
    },
    pendingCount() {
      return this.programAthletes.filter((a) => a.is_weight_passed == null)
        .length;
    },
    // 欄位標題要跟隨語系變動，因此放 computed（放 data 會被凍結）
    columns() {
      return [
        {
          key: "athlete",
          title: this.$t("athletes"),
          dataIndex: "athlete",
        },
        {
          key: "result",
          title: this.$t("weights.column.result"),
          dataIndex: "result",
          width: 170,
        },
        {
          key: "weight",
          title: this.$t("weights.column.weight"),
          dataIndex: "weight",
          align: "right",
          width: 280,
        },
        {
          key: "operation",
          title: this.$t("action"),
          dataIndex: "operation",
          align: "center",
          width: 90,
        },
      ];
    },
  },
  data() {
    return {
      categoryId: null,
      programId: null,
      select_programs: [],
      program: {},
      importModalOpen: false,
    };
  },
  methods: {
    // 匯入完成後重新載入 programs，並保留目前選取的組別與項目
    onWeightsImported() {
      this.$inertia.reload({
        only: ["programs"],
        preserveScroll: true,
        onSuccess: (page) => this.syncAfterReload(page?.props?.programs),
      });
    },

    // props 更新後，重建組別的項目清單並保留目前選取
    syncAfterReload(programs) {
      const list = programs ?? this.programs;
      this.select_programs = list.filter(
        (p) => p.competition_category_id == this.categoryId
      );
      this.onChangeProgram(this.programId);
    },

    // 這列是否已有過磅資料（決定重置鈕是否可用）
    hasWeighInData(record) {
      return (
        record.is_weight_passed !== null && record.is_weight_passed !== undefined
      ) || Number(record.weight) > 0;
    },

    confirmReset({ title, content, warnLocked = true, onOk }) {
      Modal.confirm({
        title,
        content:
          warnLocked && this.isLocked
            ? `${content} ${this.$t("weights.reset_locked_hint")}`
            : content,
        okText: this.$t("weights.reset_confirm_ok"),
        okType: "danger",
        cancelText: this.$t("action.cancel"),
        onOk,
      });
    },

    postReset(url) {
      this.$inertia.post(url, null, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          this.$message.success(this.$t("weights.reset_success"));
          this.syncAfterReload();
        },
        onError: (error) => {
          this.$message.error(
            error?.response?.data?.message ?? this.$t("weights.action_failed")
          );
        },
      });
    },

    // 重置單一選手
    resetAthlete(record) {
      this.confirmReset({
        title: this.$t("weights.reset_single_title"),
        content: this.$t("weights.reset_single_confirm"),
        onOk: () =>
          this.postReset(
            route("manage.competition.programAthlete.weightReset", {
              competition: this.competition.id,
              programAthlete: record.id,
            })
          ),
      });
    },

    // 重置目前項目
    resetProgramWeights() {
      this.confirmReset({
        title: this.$t("weights.reset_program_title"),
        content: this.$t("weights.reset_program_confirm"),
        onOk: () =>
          this.postReset(
            route("manage.competition.program.weights.reset", {
              competition: this.competition.id,
              program: this.program.id,
            })
          ),
      });
    },

    // 重置整場賽事
    resetAllWeights() {
      this.confirmReset({
        title: this.$t("weights.reset_all_title"),
        content: this.$t("weights.reset_all_confirm"),
        warnLocked: false,
        onOk: () =>
          this.postReset(
            route(
              "manage.competition.athletes.weights.resetAll",
              this.competition.id
            )
          ),
      });
    },
    // 公斤級代碼 (MW60- / FW42+ / MWULW) → 目前語系的顯示名稱
    weightGroupLabel(weightCode) {
      return weightGroupLabelUtil(weightCode, this.$t);
    },
    // 項目的顯示標籤（公斤級 + 組別），供標題與「未完成過磅」清單使用
    programLabel(program) {
      const weightLabel = this.weightGroupLabel(program?.weight_code);
      const categoryName = program?.competition_category?.name ?? "";
      return `${weightLabel} ${categoryName}`.trim();
    },
    // 與頁面上 isLocked 相同的判斷：只要有任一位選手的 confirm 為 1 就算已鎖定
    isProgramLocked(program) {
      return (program?.program_athletes ?? []).some(
        (athlete) => athlete.confirm == 1
      );
    },
    // 該項目尚未過磅的選手
    unweighedAthletes(program) {
      return (program?.program_athletes ?? []).filter(
        (athlete) => athlete.is_weight_passed == null
      );
    },
    onChangeCategory(event) {
      this.select_programs = this.programs.filter(
        (p) => p.competition_category_id == event.target.value
      );
      this.programId = null;
      this.program = {};
    },
    onChangeProgram(programId) {
      this.program = this.programs.find((p) => p.id == programId) ?? {};
    },
    // 依過磅結果為整列上色（對應下方 style 的 .green / .red）
    rowClassName(record) {
      if (record.is_weight_passed == 1) return "green";
      if (record.is_weight_passed == 0) return "red";
      return "";
    },

    passWeight(record, is_weight_passed) {
      if (record.weight === null || record.weight === undefined) {
        this.$message.error(this.$t("weights.weight_not_input"));
        return;
      }

      this.$inertia.post(
        route("manage.competition.programAthlete.weightChecked", {
          weight: record.weight,
          competition: this.competition.id,
          programAthlete: record.id,
          is_weight_passed: is_weight_passed,
        }),
        null,
        {
          // 保留選擇的組別/項目，避免每次過磅後要重新選一次
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            this.$message.success(this.$t("weights.weight_recorded"));
            this.onChangeProgram(this.programId);
          },
          onError: (error) => {
            this.$message.error(
              error?.response?.data?.message ?? this.$t("weights.action_failed")
            );
          },
        }
      );
    },

    lockProgramWeighIn() {
      Modal.confirm({
        title: this.$t("weights.confirm_lock_title"),
        content: this.$t("weights.confirm_lock_content"),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.post(
            route("manage.competition.athletes.weights.lock", {
              competition: this.competition.id,
              program: this.program.id,
            }),
            null,
            {
              preserveScroll: true,
              preserveState: true,
              onSuccess: () => {
                this.$message.success(this.$t("weights.lock_success"));
                this.onChangeProgram(this.program.id);
              },
              onError: (error) => {
                this.$message.error(
                  error?.response?.data?.message ?? this.$t("weights.action_failed")
                );
              },
            }
          );
        },
      });
    },
    cancelLockWeighIn() {
      Modal.confirm({
        title: this.$t("weights.confirm_cancel_lock_title"),
        content: this.$t("weights.confirm_cancel_lock_content"),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.post(
            route("manage.competition.athletes.weights.cancelLock", {
              competition: this.competition.id,
              program: this.program.id,
            }),
            null,
            {
              preserveScroll: true,
              preserveState: true,
              onSuccess: () => {
                this.$message.success(this.$t("weights.cancel_lock_success"));
                this.onChangeProgram(this.program.id);
              },
              onError: (error) => {
                this.$message.error(
                  error?.response?.data?.message ?? this.$t("weights.action_failed")
                );
              },
            }
          );
        },
      });
    },
    // 鎖定整場賽事的過磅（全部項目）
    lockAllWeighIn() {
      const incomplete = this.incompletePrograms;

      // 與後端 weightsLockAll 一致：有任何項目未完成過磅就完全不鎖
      if (incomplete.length > 0) {
        Modal.error({
          title: this.$t("weights.lock_all_incomplete_title"),
          content: this.$t("weights.lock_all_incomplete", {
            programs: incomplete.map((p) => this.programLabel(p)).join("、"),
          }),
          okText: this.$t("ok"),
        });
        return;
      }

      Modal.confirm({
        title: this.$t("weights.confirm_lock_all_title"),
        content: this.$t("weights.confirm_lock_all_content"),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.post(
            route(
              "manage.competition.athletes.weights.lockAll",
              this.competition.id
            ),
            null,
            {
              preserveScroll: true,
              preserveState: true,
              onSuccess: (page) => {
                // 後端若回報「有項目未完成」，就不能顯示鎖定成功
                if (Object.keys(page?.props?.errors ?? {}).length > 0) return;

                this.$message.success(this.$t("weights.lock_all_success"));
                this.syncAfterReload();
              },
              onError: (errors) => {
                this.$message.error(
                  errors?.weights_lock_all
                    ? this.$t("weights.lock_all_incomplete", {
                        programs: errors.weights_lock_all,
                      })
                    : this.$t("weights.action_failed")
                );
              },
            }
          );
        },
      });
    },
    // 取消鎖定整場賽事的過磅（全部項目）
    cancelLockAllWeighIn() {
      Modal.confirm({
        title: this.$t("weights.confirm_cancel_lock_all_title"),
        content: this.$t("weights.confirm_cancel_lock_all_content"),
        okText: this.$t("ok"),
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.post(
            route(
              "manage.competition.athletes.weights.cancelLockAll",
              this.competition.id
            ),
            null,
            {
              preserveScroll: true,
              preserveState: true,
              onSuccess: () => {
                this.$message.success(
                  this.$t("weights.cancel_lock_all_success")
                );
                this.syncAfterReload();
              },
              onError: () => {
                this.$message.error(this.$t("weights.action_failed"));
              },
            }
          );
        },
      });
    },
  },
};
</script>

<style lang="less">
.green {
  & > .ant-table-cell,
  &:hover {
    @apply bg-green-300;
    @apply dark:bg-green-700;
  }

  & > td.ant-table-cell-row-hover {
    @apply !bg-green-200;
    @apply dark:!bg-green-800;
  }
}

.red {
  & > .ant-table-cell,
  &:hover {
    @apply bg-red-300;
    @apply dark:bg-red-700;
  }

  & > td.ant-table-cell-row-hover,
  &:hover > td {
    @apply !bg-red-200;
    @apply dark:!bg-red-800;
  }
}

.gray {
  & > .ant-table-cell,
  &:hover {
    @apply bg-gray-300;
    @apply dark:bg-neutral-700;
  }

  & > td.ant-table-cell-row-hover,
  &:hover > td {
    @apply !bg-gray-200;
    @apply dark:!bg-neutral-800;
  }
}
</style>
