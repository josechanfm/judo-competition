<template>
  <inertia-head :title="programTitle" />

  <ProgramLayout :competition="competition">
    <a-page-header :title="programTitle">
      <template #tags>
        <a-tag v-if="program.competition_system" color="processing">
          {{ $t("competition_system." + program.competition_system) }}
        </a-tag>
        <a-tag>{{ program.date }}</a-tag>
      </template>
      <template #extra>
        <a-button
          type="primary"
          class="bg-blue-500"
          :href="
            route('manage.competition.program.generateCert', {
              competition: competition.id,
              program: program.id,
            })
          "
          target="_blank"
        >
          <template #icon><DownloadOutlined /></template>
          {{ $t("program.download_certificate") }}
        </a-button>
      </template>
    </a-page-header>

    <div class="py-8 xl:mx-16 mx-8">
      <div class="flex flex-col gap-4">
        <!-- 概要 -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <a-card class="shadow-md">
            <a-statistic :title="$t('status')" :value="$t('athletes.ready_to_start')" />
          </a-card>
          <a-card class="shadow-md">
            <a-statistic :title="$t('date')" :value="program.date ?? '-'" />
          </a-card>
          <a-card class="shadow-md">
            <a-statistic :title="$t('program.bout_count')" :value="program.bouts.length" />
          </a-card>
          <a-card class="shadow-md">
            <a-statistic :title="$t('athletes')" :value="athletes ? athletes.length : 0" />
          </a-card>
        </div>
        <div class="flex flex-col gap-6">
          <!-- 運動員名單 -->
          <a-card class="shadow-md" :body-style="{ padding: '16px 20px 4px' }">
            <template #title>
              <div class="flex flex-wrap items-center justify-between gap-2">
                <span>{{ $t("athletes.title") }}</span>
                <a-tag>
                  {{ $t("athletes_total", { total: athletes ? athletes.length : 0 }) }}
                </a-tag>
              </div>
            </template>
            <a-table
              :data-source="athletes"
              :columns="athleteColumns"
              :row-key="(record) => record.id"
              :pagination="false"
              :scroll="{ x: 680 }"
              size="middle"
            >
              <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'operation'">
                  <a-space :size="4">
                    <a-button type="primary" class="bg-blue-500" @click="onEditAthlete(record)">
                      <template #icon><EditOutlined /></template>
                      {{ $t("action.edit") }}
                    </a-button>
                    <a-popconfirm
                      :title="$t('program.confirm_remove_athlete')"
                      :ok-text="$t('ok')"
                      :cancel-text="$t('action.cancel')"
                      @confirm="moveAthlete(record)"
                    >
                      <a-button danger>
                        <template #icon><DeleteOutlined /></template>
                        {{ $t("remove") }}
                      </a-button>
                    </a-popconfirm>
                  </a-space>
                </template>
                <template v-else-if="column.key === 'gender'">
                  {{
                    record.gender === "M"
                      ? $t("gender.male")
                      : record.gender === "F"
                      ? $t("gender.female")
                      : record.gender
                  }}
                </template>
                <template v-else-if="column.key === 'is_weight_passed'">
                  <a-tag v-if="record.pivot.is_weight_passed == 1" color="success">
                    {{ $t("weights.result.passed") }}
                  </a-tag>
                  <a-tag v-else-if="record.pivot.is_weight_passed == 0" color="error">
                    {{ $t("weights.result.failed") }}
                  </a-tag>
                  <span v-else class="text-slate-400">
                    {{ $t("weights.result.pending") }}
                  </span>
                </template>
                <template v-else-if="column.key === 'seed'">
                  {{ record.pivot.seed ?? "-" }}
                </template>
                <template v-else-if="column.key === 'rank'">
                  {{ record.pivot.rank || "-" }}
                </template>
              </template>
            </a-table>
          </a-card>
          <!-- 上線表 -->
          <a-card class="shadow-md">
            <template #title>
              <div class="flex flex-wrap items-center justify-between gap-2">
                <span>{{ $t("draw_control.online_table") }}</span>
                <a-button
                  v-if="program.competition_system"
                  type="primary"
                  class="bg-blue-500"
                  :href="
                    route('manage.competition.program.generateOnlineTable', [
                      competition.id,
                      program.id,
                    ])
                  "
                  target="_blank"
                >
                  <template #icon><PrinterOutlined /></template>
                  {{ $t("programs.print_pdf") }}
                </a-button>
              </div>
            </template>
            <component
              v-if="program.bouts.length > 0"
              :is="tournamentTable"
              :contestSystem="program.competition_system"
              :bouts="bouts"
            />
            <a-empty v-else :description="$t('bouts.not_found')" />
          </a-card>
        </div>
      </div>
    </div>

    <!-- 編輯運動員 -->
    <a-modal
      v-model:open="editModal.isOpen"
      :title="$t('action.edit')"
      :footer="null"
      width="720px"
    >
      <a-form ref="editFormRef" :model="editModal.data" layout="vertical" :rules="editRules">
        <div class="flex flex-col">
          <div class="flex justify-between gap-3">
            <div class="w-1/2">
              <a-form-item :label="$t('name')" name="name">
                <a-input type="input" v-model:value="editModal.data.name" />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item :label="$t('athletes.name_secondary')" name="name_secondary">
                <a-input type="input" v-model:value="editModal.data.name_secondary" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-1/2">
              <a-form-item :label="$t('display_name')" name="name_display">
                <a-input type="input" v-model:value="editModal.data.name_display" />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item :label="$t('gender')" name="gender">
                <a-select v-model:value="editModal.data.gender" :options="genderOptions" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-1/2">
              <a-form-item :label="$t('program.seed')" name="seed">
                <a-input-number
                  v-model:value="editModal.data.seed"
                  class="w-full"
                  :min="1"
                  :precision="0"
                />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item :label="$t('program.rank')" name="rank">
                <a-input-number
                  v-model:value="editModal.data.rank"
                  class="w-full"
                  :min="1"
                  :precision="0"
                />
              </a-form-item>
            </div>
          </div>
          <div class="text-right">
            <a-form-item>
              <a-button
                class="bg-blue-500"
                type="primary"
                :loading="saving"
                @click="onUpdateProgramAthlete"
                >{{ $t("athletes.update") }}</a-button
              >
              <a-button style="margin-left: 10px" @click="editModal.isOpen = false"
                >{{ $t("athletes.close") }}</a-button
              >
            </a-form-item>
          </div>
        </div>
      </a-form>
    </a-modal>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import Tournament4 from "@/Components/TournamentTable/Elimination4.vue";
import Tournament8 from "@/Components/TournamentTable/Elimination8.vue";
import Tournament16 from "@/Components/TournamentTable/Elimination16.vue";
import Tournament32 from "@/Components/TournamentTable/Elimination32.vue";
import Tournament64 from "@/Components/TournamentTable/Elimination64.vue";
import {
  DeleteOutlined,
  DownloadOutlined,
  EditOutlined,
  PrinterOutlined,
} from "@ant-design/icons-vue";
import { weightGroupLabel as weightGroupLabelUtil } from "@/Utils/weightParser";

export default {
  components: {
    ProgramLayout,
    Tournament4,
    Tournament8,
    Tournament16,
    Tournament32,
    Tournament64,
    DeleteOutlined,
    DownloadOutlined,
    EditOutlined,
    PrinterOutlined,
  },
  props: ["competition", "program", "athletes"],
  data() {
    return {
      masterSequence: false,
      bouts: [],
      saving: false,
      editModal: {
        isOpen: false,
        data: {},
      },
      tournamentTable: "Tournament" + this.program.chart_size,
      editRules: {
        name: { required: true },
        gender: { required: true },
      },
    };
  },
  computed: {
    // 標題：公斤級 + 組別（Inertia 序列化的關聯是 snake_case：competition_category）
    programTitle() {
      const weightLabel = this.weightGroupLabel(this.program.weight_code);
      const categoryName = this.program.competition_category?.name ?? "";
      return `${weightLabel} ${categoryName}`.trim();
    },
    // 欄位標題要跟隨語系變動，因此放 computed（放 data 會被凍結）
    athleteColumns() {
      return [
        {
          key: "name_display",
          title: this.$t("display_name"),
          dataIndex: "name_display",
          width: 160,
        },
        {
          key: "gender",
          title: this.$t("gender"),
          dataIndex: "gender",
          width: 70,
        },
        {
          key: "is_weight_passed",
          title: this.$t("weights.column.result"),
          dataIndex: "is_weight_passed",
          width: 120,
        },
        {
          key: "seed",
          title: this.$t("program.seed"),
          dataIndex: "seed",
          width: 60,
        },
        {
          key: "rank",
          title: this.$t("program.rank"),
          dataIndex: "rank",
          width: 60,
        },
        {
          key: "operation",
          title: this.$t("action"),
          dataIndex: "operation",
          width: 210,
        },
      ];
    },
    genderOptions() {
      return [
        { value: "M", label: this.$t("gender.male") },
        { value: "F", label: this.$t("gender.female") },
      ];
    },
  },
  created() {
    this.rebuildBouts();
  },
  methods: {
    rebuildBouts() {
      this.bouts = [];
      this.program.bouts.forEach((b) => {
        b.white_name_display = b.white_player.name_display;
        b.blue_name_display = b.blue_player.name_display;
        if (this.masterSequence) {
          b.circle = b.sequence;
        } else {
          b.circle = b.in_program_sequence;
        }
        this.bouts.push(b);
      });
      // KOS 的賽程索引需要位移以對應各尺寸寫死的表格；
      // 但 4 人表（chart_size = 4）的索引本來就對齊，插入空字串反而會把選手清空。
      if (this.program.competition_system == "kos" && this.program.chart_size > 4) {
        this.bouts.splice(this.program.chart_size - 2, 0, "");
        this.bouts.splice(this.program.chart_size - 1, 0, "");
        this.bouts.splice(this.program.chart_size - 4, 0, "");
        this.bouts.splice(this.program.chart_size - 3, 0, "");
      }
    },
    joinAthlete(athlete) {
      this.$inertia.post(
        route("manage.program.joinAthlete", {
          program: this.program.id,
          athlete: athlete,
        }),
        null,
        {
          preserveScroll: true,
        }
      );
    },
    moveAthlete(record) {
      this.$inertia.delete(
        route("manage.program.removeAthlete", {
          program: this.program.id,
          athlete: record.id,
        }),
        {
          preserveScroll: true,
        }
      );
    },
    // 編輯此項目下的運動員（基本資料 + program_athlete 的種子/排名）
    onEditAthlete(record) {
      this.editModal.data = {
        id: record.id,
        name: record.name ?? "",
        name_secondary: record.name_secondary ?? "",
        name_display: record.name_display ?? "",
        gender: record.gender ?? null,
        // 種子 / 排名來自 program_athlete（pivot）；0 代表沒有值，顯示空白
        seed: record.pivot?.seed || null,
        rank: record.pivot?.rank || null,
      };
      this.editModal.isOpen = true;
    },
    onUpdateProgramAthlete() {
      this.$refs.editFormRef
        .validateFields()
        .then(() => {
          this.saving = true;
          this.$inertia.put(
            route("manage.program.updateAthlete", {
              program: this.program.id,
              athlete: this.editModal.data.id,
            }),
            this.editModal.data,
            {
              preserveScroll: true,
              onSuccess: () => {
                this.$message.success(this.$t("programs.save_success"));
                this.editModal.isOpen = false;
              },
              onError: () => {
                this.$message.error(this.$t("save_failed"));
              },
              onFinish: () => {
                this.saving = false;
              },
            }
          );
        })
        .catch(() => {});
    },
    // 公斤級代碼 (MW60- / FW42+ / MWULW) → 目前語系的顯示名稱
    weightGroupLabel(weightCode) {
      return weightGroupLabelUtil(weightCode, this.$t);
    },
  },
};
</script>
