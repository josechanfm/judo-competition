<template>
  <inertia-head :title="$t('programs.title')" />

  <ProgramLayout :competition="competition">
    <a-page-header :title="$t('programs.manage')">
      <template #tags>
        <a-tag color="processing">
          <div class="flex items-center gap-1">
            <AppstoreOutlined />
            {{ $t("competitions.mats_count", { mat_number: competition.mat_number }) }}
          </div>
        </a-tag>
        <a-tag color="processing">
          <div class="flex items-center gap-1">
            <ScheduleOutlined />
            {{ $t("competitions.sections_count", { section_number: competition.section_number }) }}
          </div>
        </a-tag>
      </template>
      <template #extra>
        <a-button
          v-if="competition.status === 0"
          type="primary"
          class="bg-blue-500"
          @click="confirmLockAthletes"
          >{{ $t("programs.lock_athletes") }}</a-button
        >
        <span v-if="competition.status < 1">{{ $t("programs.athletes_unlocked") }}</span>
        <a-button
          v-else-if="competition.status === 1"
          type="primary"
          class="bg-blue-500"
          @click="confirmLockSequences"
          >{{ $t("programs.lock_sequence") }}</a-button
        >
        <span v-else class="text-blue-500">{{ $t("programs.sequence_locked") }}</span>
      </template>
    </a-page-header>
    <div class="mx-6">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="flex w-full gap-6">
          <div class="flex flex-1 flex-col">
            <div class="flex justify-between items-center gap-3 flex-wrap mt-6 mb-2">
              <div class="flex items-center gap-3 flex-wrap">
                <div class="text-xl font-bold">
                  {{ $t("programs.total", { count: filteredPrograms.length }) }}
                </div>
                <!-- 篩選：組別 / 公斤級 -->
                <a-select
                  v-model:value="filterCategory"
                  :options="categoryOptions"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  :placeholder="$t('programs.filter_category')"
                  style="min-width: 190px"
                />
                <a-select
                  v-model:value="filterWeight"
                  :options="weightOptions"
                  allow-clear
                  show-search
                  option-filter-prop="label"
                  :placeholder="$t('programs.filter_weight')"
                  style="min-width: 150px"
                />
                <a-button
                  v-if="filterCategory || filterWeight"
                  type="link"
                  @click="clearProgramFilters"
                >
                  <template #icon><CloseCircleOutlined /></template>
                  {{ $t("programs.clear_filter") }}
                </a-button>
              </div>
              <a-radio-group option-type="button" v-model:value="view">
                <a-radio-button value="list">
                  <UnorderedListOutlined />
                </a-radio-button>
                <a-radio-button value="grid">
                  <AppstoreOutlined />
                </a-radio-button>
              </a-radio-group>
            </div>
            <div v-if="view === 'list'" class="my-2 shadow-lg bg-white rounded-lg">
              <div class="pb-2 mx-4 mt-2 flex justify-between">
                <div class="text-xl font-bold">{{ $t("programs.all") }}</div>
                <div class="">
                  <a-button
                    v-if="programsEdit == false"
                    type="link"
                    @click="
                      () => {
                        this.programsEdit = true;
                      }
                    "
                  >
                    <div class="flex items-center gap-2">
                      <EditOutlined />{{ $t("action.edit") }}
                    </div>
                  </a-button>
                  <a-button v-else @click="savePrograms" type="link">
                    <div class="flex items-center gap-2">
                      <SaveOutlined />{{ $t("save") }}
                    </div></a-button
                  >
                  <a-button type="link" @click="showPrintDialog">
                    <div class="flex items-center gap-2">
                      <DownloadOutlined />{{ $t("programs.print_pdf") }}
                    </div></a-button
                  >
                </div>
              </div>
              <a-table :dataSource="filteredPrograms" :columns="columns">
                <template #bodyCell="{ column, record }">
                  <template v-if="column.dataIndex === 'category_group'">
                    {{ record.competition_category.name }}
                  </template>
                  <template v-if="column.dataIndex === 'operation'">
                    <a-button
                      :href="
                        route('manage.competition.programs.show', [
                          record.competition_category.competition_id,
                          record.id,
                        ])
                      "
                    >
                      {{ $t("action.view") }}
                    </a-button>
                  </template>
                  <template v-if="column.dataIndex === 'athletes'">
                    <span>{{ record.athletes_count }}</span>
                  </template>
                  <template v-if="column.dataIndex === 'competition_system'">
                    <template v-if="programsEdit == false">
                      {{ contestSystemLabel(record.competition_system) }}
                    </template>
                    <template v-else>
                      <a-select
                        v-model:value="record.competition_system"
                        :options="competitionSystems"
                      ></a-select>
                    </template>
                  </template>
                  <template v-else-if="column.dataIndex === 'weight_code'">
                    {{ weightGroupLabel(record.weight_code) }}
                  </template>
                  <template v-else>
                    {{ record[column.dataIndex] }}
                  </template>
                </template>
              </a-table>
            </div>
            <template v-else>
              <div
                v-if="competition.status <= 4"
                class="pr-4 py-2 flex justify-between bg-white shadow-md rounded-sm"
              >
                <div class="flex justify-start">
                  <div v-if="!editDraggable">
                    <div v-if="!multipleMove" class="flex gap-2">
                      <a-button type="link" @click="openMoveCategory">
                        <div class="flex items-center gap-2">
                          <EnvironmentOutlined />{{ $t("programs.move_category") }}
                        </div>
                      </a-button>
                      <a-button type="link" @click="multipleMove = !multipleMove"
                        >{{ $t("programs.batch_move") }}</a-button
                      >
                    </div>
                    <div v-else class="flex gap-2 items-center pl-4">
                      <span>
                        {{ $t("programs.move_selected", { count: selectedPrograms.length }) }}
                      </span>
                      <a-select class="w-32" v-model:value="batchMoveForm.day" name="day">
                        <a-select-option
                          v-for="day in competition.days"
                          :id="`opt-day-${day}`"
                          :key="day"
                          :value="day"
                          >{{ day }}
                        </a-select-option>
                      </a-select>
                      <a-select
                        class="w-24"
                        v-model:value="batchMoveForm.section"
                        name="section"
                      >
                        <a-select-option
                          v-for="section in competition.section_number"
                          :id="`opt-section-${section}`"
                          :key="section"
                          :value="section"
                          >{{ $t("competitions.section") }} {{ section }}
                        </a-select-option>
                      </a-select>
                      <a-select class="w-24" v-model:value="batchMoveForm.mat" name="mat">
                        <a-select-option
                          v-for="mat in competition.mat_number"
                          :id="`opt-mat-${mat}`"
                          :key="mat"
                          :value="mat"
                          >{{ $t("competitions.mat") }} {{ mat }}
                        </a-select-option>
                      </a-select>
                      <a-button @click="batchMovePrograms">{{ $t("programs.move_and_save") }}</a-button>
                      <a-button @click="cancelMovePrograms">{{ $t("cancel") }}</a-button>
                    </div>
                  </div>
                </div>
                <div class="flex justify-end">
                  <div v-if="!multipleMove">
                    <a-button
                      type="link"
                      v-if="!editDraggable"
                      @click="editDraggable = !editDraggable"
                      >{{ $t("action.edit") }}</a-button
                    >
                    <template v-else>
                      <a-button type="link" @click="saveDrag">{{ $t("save") }}</a-button>
                      <a-button type="link" @click="cancelDrag">{{ $t("cancel") }}</a-button>
                    </template>
                  </div>
                  <a-button type="link" @click="showPrintDialog">
                    <template #icon> <DownloadOutlined /> </template
                    >{{ $t("programs.print_pdf") }}</a-button
                  >
                </div>
              </div>
              <div v-for="day in competition.days" class="mb-6" :key="day">
                <div class="text-2xl font-medium mb-6">{{ day }}</div>
                <div v-for="section in competition.section_number" :key="section">
                  <div class="font-bold text-lg mb-3">
                    {{ $t("competitions.section") }} {{ section }}
                  </div>
                  <div class="grid grid-cols-2 gap-3 mb-6">
                    <a-card
                      v-for="mat in competition.mat_number"
                      :title="`${$t('competitions.mat')} ${mat}`"
                      :key="mat"
                      class="borderless"
                      style="min-height: 300px"
                    >
                      <template #extra>
                        {{
                          $t("programs.mat_summary", {
                            count: matSecProgramsCount(day, section, mat),
                            time: matSecMaxTimeEst(day, section, mat),
                          })
                        }}
                        <a-dropdown class="ml-3" placement="bottomRight">
                          <a-button type="text">
                            <template #icon>
                              <MoreOutlined />
                            </template>
                          </a-button>

                          <template #overlay>
                            <a-menu>
                              <a-menu-item>
                                <a
                                  :href="
                                    route('admin.contests.programs.pdf-mat-brackets', {
                                      competition: competition,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                    })
                                  "
                                  target="_blank"
                                  >{{ $t("programs.download_online_table") }}</a
                                >
                              </a-menu-item>

                              <a-menu-item>
                                <a
                                  :href="
                                    route('admin.contests.programs.pdf-mat-brackets', {
                                      competition: competition,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                      show_sequence: true,
                                    })
                                  "
                                  target="_blank"
                                  >{{ $t("programs.download_online_table_sequence") }}</a
                                >
                              </a-menu-item>

                              <a-menu-divider />

                              <a-menu-item>
                                <a
                                  :href="
                                    route('admin.contests.programs.pdf-mat-bouts', {
                                      competition: competition,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                      show_sequence: true,
                                    })
                                  "
                                  target="_blank"
                                  >{{ $t("programs.download_bout_list") }}</a
                                >
                              </a-menu-item>
                            </a-menu>
                          </template>
                        </a-dropdown>
                      </template>
                      <draggable
                        class="py-2"
                        :disabled="!editDraggable"
                        @end="onDragEnd(day, section, mat)"
                        :list="partitionedPrograms[day][section][mat]"
                      >
                        <div
                          :class="editDraggable ? 'cursor-grab' : ''"
                          class="flex items-center px-4 pt-2"
                          v-for="element in partitionedPrograms[day][section][mat]"
                          :key="element.name"
                        >
                          <div class="w-8" v-if="multipleMove">
                            <a-checkbox
                              :value="element.id"
                              :checked="isProgramChecked(element)"
                              :id="`chk-${element.id}`"
                              @change="toggleProgramChecked(element)"
                            />
                          </div>
                          <div class="flex-1 flex items-center gap-3">
                            <template v-if="editDraggable">
                              <HolderOutlined />
                            </template>
                            <div>
                              <div class="mb-2">
                                <a-tag>
                                  {{ element.competition_category.name }}
                                </a-tag>
                                <a-tag>{{ contestSystemLabel(element.competition_system) }}</a-tag>
                                {{ weightGroupLabel(element.weight_code) }}
                              </div>
                              <div class="text-sm text-neutral-500">
                                {{
                                  $t("programs.people_bouts", {
                                    people: element.athletes_count,
                                    bouts: element.bouts_count,
                                  })
                                }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </draggable>
                    </a-card>
                  </div>
                </div>
              </div>
            </template>
          </div>
          <div class="w-72 flex flex-col">
            <div class="">
              <h3 class="font-bold text-lg mb-3">{{ $t("programs.documents") }}</h3>
              <div class="flex flex-col gap-2">
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.program.export.medal-quantity', competition.id)"
                >{{ $t("programs.medal_quantity") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.program.export.program-time', competition.id)"
                >{{ $t("programs.program_time") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.generateAllProgramsOnlineTable', competition.id)"
                >{{ $t("programs.all_online_tables") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.allSchedule',competition.id)"
                >{{ $t("programs.all_schedules") }}</a>  
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.result-table', {'competition':competition.id,'blankMedals':false})"
                >{{ $t("programs.result_tables") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.athletes.export.id-card', competition.id)"
                >{{ $t("programs.id_cards") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.team-athletes-result-table',competition.id)"
                >{{ $t("programs.team_results") }}</a>
                <a 
                  class="text-blue-500" 
                  target="_blank" 
                  :href="route('manage.competition.fail-weighIn-table',competition.id)"
                >{{ $t("weights_fail_table") }}</a>
                <a
                  class="text-blue-500"
                  target="_blank"
                  :href="route('manage.competition.checkIn-table', competition.id)"
                >{{ $t("programs.check_in_table") }}</a>
              </div>
              <h3 class="font-bold text-lg mb-3 pt-2">{{ $t("programs.other_documents") }}</h3>
              <div class="flex flex-col gap-2">
                <a
                  class="text-blue-500"
                  @click="showPrintDialogWithParams()"
                >{{ $t("programs.print_more") }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Print Dialog Modal -->
    <a-modal
      v-model:open="printDialogVisible"
      :title="$t('programs.print_dialog_title')"
      :footer="null"
      width="500px"
    >
      <div class="flex flex-col gap-4 py-4">
        <div class="flex flex-col gap-2">
          <label class="font-semibold">{{ $t("programs.document_type") }}</label>
          <a-select v-model:value="selectedPrintType" :placeholder="$t('programs.select_document')">
            <a-select-option value="onlineTable">{{ $t("programs.print_type.online_table") }}</a-select-option>
            <a-select-option value="schedule">{{ $t("programs.print_type.schedule") }}</a-select-option>
          </a-select>
        </div>

        <!-- Date Selection (顯示當選擇依日期相關選項時) -->
        <div 
          class="flex flex-col gap-2"
        >
          <label class="font-semibold">{{ $t("programs.select_date") }}</label>
          <a-select v-model:value="selectedDate" :placeholder="$t('programs.select_date')">
            <a-select-option 
              v-for="day in competition.days" 
              :key="day" 
              :value="day"
            >
              {{ day }}
            </a-select-option>
          </a-select>
        </div>

        <!-- Section Selection (顯示當選擇依場次相關選項時) -->
        <div 
          class="flex flex-col gap-2"
        >
          <label class="font-semibold">{{ $t("programs.select_section") }}</label>
          <a-select v-model:value="selectedSection" :placeholder="$t('programs.select_section')">
            <a-select-option 
              v-for="section in competition.section_number" 
              :key="section" 
              :value="section"
            >
              {{ $t("competitions.section") }} {{ section }}
            </a-select-option>
          </a-select>
        </div>

        <!-- Mat Selection (顯示當選擇依場地相關選項時) -->
        <div 
          class="flex flex-col gap-2"
        >
          <label class="font-semibold">{{ $t("programs.select_mat") }}</label>
          <a-select v-model:value="selectedMat" :placeholder="$t('programs.select_mat')">
            <a-select-option 
              v-for="mat in competition.mat_number" 
              :key="mat" 
              :value="mat"
            >
              {{ $t("competitions.mat") }} {{ mat }}
            </a-select-option>
          </a-select>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <a-button @click="printDialogVisible = false">{{ $t("cancel") }}</a-button>
          <a-button type="primary" @click="generatePrintFile">{{ $t("programs.confirm_print") }}</a-button>
        </div>
      </div>
    </a-modal>

    <!-- Move Category Modal -->
    <a-modal
      v-model:open="moveCategoryVisible"
      :title="$t('programs.move_category_title')"
      :footer="null"
      width="480px"
    >
      <div class="flex flex-col gap-4 py-4">
        <div class="flex flex-col gap-2">
          <label class="font-semibold">{{ $t("programs.select_category") }}</label>
          <a-select
            v-model:value="moveCategoryForm.competition_category_id"
            :placeholder="$t('programs.select_category')"
            :options="categoryOptions"
          />
          <div v-if="moveCategoryPrograms.length" class="text-sm text-neutral-500">
            {{
              $t("programs.category_programs_count", {
                count: moveCategoryPrograms.length,
              })
            }}
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label class="font-semibold">{{ $t("date") }}</label>
          <a-select
            v-model:value="moveCategoryForm.day"
            :placeholder="$t('programs.select_date')"
          >
            <a-select-option
              v-for="day in competition.days"
              :key="day"
              :value="day"
              >{{ day }}
            </a-select-option>
          </a-select>
        </div>

        <div class="flex flex-col gap-2">
          <label class="font-semibold">{{ $t("programs.select_section") }}</label>
          <a-select
            v-model:value="moveCategoryForm.section"
            :placeholder="$t('programs.select_section')"
          >
            <a-select-option
              v-for="section in competition.section_number"
              :key="section"
              :value="section"
              >{{ $t("competitions.section") }} {{ section }}
            </a-select-option>
          </a-select>
        </div>

        <div class="flex flex-col gap-2">
          <label class="font-semibold">{{ $t("programs.select_mat") }}</label>
          <a-select
            v-model:value="moveCategoryForm.mat"
            :placeholder="$t('programs.select_mat')"
          >
            <a-select-option
              v-for="mat in competition.mat_number"
              :key="mat"
              :value="mat"
              >{{ $t("competitions.mat") }} {{ mat }}
            </a-select-option>
          </a-select>
        </div>

        <p class="text-sm text-neutral-500 m-0">
          {{ $t("programs.move_category_hint") }}
        </p>

        <div class="flex justify-end gap-2 mt-2">
          <a-button @click="moveCategoryVisible = false">{{ $t("cancel") }}</a-button>
          <a-button
            type="primary"
            :disabled="!moveCategoryForm.competition_category_id || !moveCategoryPrograms.length"
            @click="moveCategory"
            >{{ $t("programs.move_and_save") }}</a-button
          >
        </div>
      </div>
    </a-modal>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import dayjs from "dayjs";
import { weightGroupLabel as weightGroupLabelUtil } from "@/Utils/weightParser";
import { message } from "ant-design-vue";
import { Modal } from "ant-design-vue";
import { createVNode } from "vue";
import { VueDraggableNext } from "vue-draggable-next";
import duration from "dayjs/plugin/duration";
import {
  UnorderedListOutlined,
  AppstoreOutlined,
  ExclamationCircleOutlined,
  HolderOutlined,
  EditOutlined,
  LockOutlined,
  ScheduleOutlined,
  EnvironmentOutlined,
  SaveOutlined,
  DownloadOutlined,
  ClockCircleOutlined,
  MoreOutlined,
  CloseCircleOutlined,
} from "@ant-design/icons-vue";

dayjs.extend(duration);
export default {
  components: {
    ProgramLayout,
    UnorderedListOutlined,
    ExclamationCircleOutlined,
    AppstoreOutlined,
    HolderOutlined,
    EditOutlined,
    LockOutlined,
    ScheduleOutlined,
    EnvironmentOutlined,
    DownloadOutlined,
    SaveOutlined,
    ClockCircleOutlined,
    MoreOutlined,
    CloseCircleOutlined,
    draggable: VueDraggableNext,
  },
  props: ["competition", "programs", "athletes"],
  data() {
    return {
      view: "list",
      programsEdit: false,
      // 篩選：組別（competition_category_id）／公斤級（weight_code）
      filterCategory: null,
      filterWeight: null,
      dateFormat: "YYYY-MM-DD",
      editDraggable: false,
      multipleMove: false,
      selectedPrograms: [],
      edit: false,
      partitionedPrograms: {},
      batchMoveForm: {
        from: {
          day: null,
          section: null,
          mat: null,
        },
        day: this.competition.days[0],
        section: 1,
        mat: 1,
      },
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      rules: {
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
      },
      // Print dialog related data
      printDialogVisible: false,
      selectedPrintType: null,
      selectedDate: null,
      selectedSection: 1,
      selectedMat: 1,
      // 移動整個組別到指定場地/時段
      moveCategoryVisible: false,
      moveCategoryForm: {
        competition_category_id: null,
        day: this.competition.days[0],
        section: 1,
        mat: 1,
      },
    };
  },
  computed: {
    // 依「組別」（competition_category_id）與「公斤級」（weight_code）篩選後的項目（清單與上線表共用）
    filteredPrograms() {
      return this.programs.filter((program) => {
        const matchesCategory =
          !this.filterCategory ||
          program.competition_category_id === this.filterCategory;
        const matchesWeight =
          !this.filterWeight || program.weight_code === this.filterWeight;

        return matchesCategory && matchesWeight;
      });
    },
    // 公斤級選項（依目前語系的顯示名稱排序）
    weightOptions() {
      const codes = [
        ...new Set(this.programs.map((program) => program.weight_code).filter(Boolean)),
      ];

      return codes
        .map((code) => ({ value: code, label: this.weightGroupLabel(code) }))
        .sort((a, b) => a.label.localeCompare(b.label));
    },
    // 表格欄位標題要跟隨語系，所以放 computed（放 data() 會被凍結在初始化時的語言）
    columns() {
      return [
        {
          title: this.$t("competitions.sequence"),
          dataIndex: "sequence",
        },
        {
          title: this.$t("date"),
          dataIndex: "date",
        },
        {
          title: this.$t("competitions.category"),
          dataIndex: "category_group",
        },
        {
          title: this.$t("competitions.weight"),
          dataIndex: "weight_code",
        },
        {
          title: this.$t("competitions.mat"),
          dataIndex: "mat",
        },
        {
          title: this.$t("competitions.section"),
          dataIndex: "section",
        },
        {
          title: this.$t("competition_system"),
          dataIndex: "competition_system",
          // 依目前語系的賽制名稱排序
          sorter: (a, b) =>
            this.contestSystemLabel(a.competition_system).localeCompare(
              this.contestSystemLabel(b.competition_system),
            ),
        },
        {
          title: this.$t("programs.duration"),
          dataIndex: "duration_formatted",
        },
        {
          title: this.$t("competitions.athletes_count"),
          dataIndex: "athletes",
          // 依報名人數數值排序
          sorter: (a, b) => a.athletes_count - b.athletes_count,
        },
        {
          title: this.$t("action"),
          dataIndex: "operation",
        },
      ];
    },
    // Ant Design 的訊息樣板使用 ${label} 佔位符，故以字串串接避免 vue-i18n 誤判為插值
    validateMessages() {
      const label = "${label}";
      return {
        required: `${label} ${this.$t("validation.is_required")}`,
        types: {
          email: `${label} ${this.$t("validation.email")}`,
          number: `${label} ${this.$t("validation.number")}`,
        },
        number: {
          range: `${label} ${this.$t("validation.range", {
            min: "${min}",
            max: "${max}",
          })}`,
        },
      };
    },
    // 賽制下拉選單：值仍然是 erm / kos / rrb / rrba，標籤跟隨語系
    competitionSystems() {
      return ["rrb", "rrba", "kos", "erm"].map((value) => ({
        value,
        label: this.contestSystemLabel(value),
      }));
    },
    matSecMaxTimeEst() {
      return (day, section, mat) => {
        const seconds = this.partitionedPrograms[day][section][mat].reduce(
          (acc, program) => {
            return acc + program.duration * program.bouts_count;
          },
          0
        );

        return dayjs.duration(seconds, "seconds").format("HH:mm:ss");
      };
    },
    matSecProgramsCount() {
      return (day, section, mat) => {
        return this.partitionedPrograms[day][section][mat].reduce((acc, program) => {
          return acc + program.bouts_count;
        }, 0);
      };
    },
    isProgramChecked() {
      return (program) => {
        return this.selectedPrograms.includes(program.id);
      };
    },
    // 可搬移的組別：由項目反推（只會有這場賽事實際存在項目的組別）
    categoryOptions() {
      const categories = new Map();

      this.programs.forEach((program) => {
        const category = program.competition_category;

        if (category && !categories.has(category.id)) {
          categories.set(category.id, {
            value: category.id,
            label: `${category.code ? category.code + " " : ""}${category.name}`,
          });
        }
      });

      return [...categories.values()];
    },
    // 目前選定組別的項目（用來顯示將搬移幾個項目）
    moveCategoryPrograms() {
      return this.programs.filter(
        (program) =>
          program.competition_category_id === this.moveCategoryForm.competition_category_id
      );
    },
  },
  watch: {
    // 篩選條件變更時，重新計算上線表分區
    filterCategory() {
      if (this.view === "grid") {
        this.getPartitionedPrograms();
      }
    },
    filterWeight() {
      if (this.view === "grid") {
        this.getPartitionedPrograms();
      }
    },
    view(val) {
      if (val === "grid") {
        this.getPartitionedPrograms();
      }

      this.selectedPrograms = [];

      this.$inertia.reload({
        preserveScroll: true,
      });
    },
    isBatchEditing(val) {
      if (!val) {
        this.selectedPrograms = [];
      }
    },
  },
  created() {
    // 初始化 selectedDate 為第一個日期
    if (this.competition.days && this.competition.days.length > 0) {
      this.selectedDate = this.competition.days[0];
    }
  },
  methods: {
    // 賽制代碼 (erm/kos/rrb/rrba) → 目前語系的顯示名稱，找不到翻譯時退回顯示代碼
    contestSystemLabel(contestSystem) {
      if (!contestSystem) {
        return "";
      }

      const key = `competition_system.${contestSystem}`;
      const label = this.$t(key);

      return label === key ? contestSystem : label;
    },
    // 公斤級代碼 (MW60- / FW42+ / MWULW) → 目前語系的顯示名稱
    weightGroupLabel(weightCode) {
      return weightGroupLabelUtil(weightCode, this.$t);
    },
    showPrintDialog() {
      this.printDialogVisible = true;
    },
    showPrintDialogWithParams() {
      this.printDialogVisible = true;
    },
    openMoveCategory() {
      // 確保棋盤資料是最新的，再開啟對話框
      this.getPartitionedPrograms();
      this.moveCategoryForm.competition_category_id = null;
      this.moveCategoryVisible = true;
    },
    /**
     * 把整個組別的項目搬到指定日期/時段/場地。
     *
     * 作法跟批次移動一致：從所有場地抽出該組別的項目，依序接到目標場地最後面，
     * 再交給 saveDrag() 重編序號並送出（後端 updateSequence 會一併更新 bouts 的場地資訊）。
     */
    moveCategory() {
      const categoryId = this.moveCategoryForm.competition_category_id;
      const { day, section, mat } = this.moveCategoryForm;

      if (!categoryId) {
        return;
      }

      const target = this.partitionedPrograms[day]?.[section]?.[mat];

      if (!target) {
        this.$message.error(this.$t("programs.move_category_failed"));
        return;
      }

      // 先把這個組別從所有場地抽出來
      const moving = [];

      Object.values(this.partitionedPrograms).forEach((sections) => {
        Object.values(sections).forEach((mats) => {
          Object.values(mats).forEach((programs) => {
            for (let index = programs.length - 1; index >= 0; index--) {
              if (programs[index].competition_category_id === categoryId) {
                moving.unshift(programs.splice(index, 1)[0]);
              }
            }
          });
        });
      });

      if (moving.length === 0) {
        return;
      }

      // 依原本序號排序後接到目標場地最後面，序號由 saveDrag() 重新編排
      moving.sort((a, b) => a.sequence - b.sequence);

      moving.forEach((program) => {
        program.date = day;
        program.section = section;
        program.mat = mat;
        target.push(program);
      });

      this.moveCategoryVisible = false;
      this.saveDrag();
    },
    generatePrintFile() {
      let url = null;

      switch (this.selectedPrintType) {
        case "onlineTables":
          url = route('manage.competition.generateAllProgramsOnlineTable', this.competition.id);
          break;
        case "schedules":
          url = route('manage.competition.allSchedule', this.competition.id);
          break;
      }

      if (url) {
        window.open(url, '_blank');
        this.printDialogVisible = false;
        message.success(this.$t("programs.generating"));
      }
    },
    onCreateRecord() {
      this.modal.title = "Create";
      this.modal.isOpen = true;
      this.modal.mode = "CREATE";
    },
    onEditRecord(record) {
      this.modal.isOpen = true;
      this.modal.title = "Edit";
      this.modal.mode = "EDIT";
      this.modal.data = { ...record };
    },
    onUpdate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.put(
            route("manage.competitions.update", this.modal.data.id),
            this.modal.data,
            {
              onSuccess: (page) => {
                this.modal.data = {};
                this.modal.isOpen = false;
              },
              onError: (err) => {
                console.log(err);
              },
            }
          );

          console.log("values", this.modal.data, this.modal.data);
        })
        .catch((error) => {
          console.log("error", error);
        });
    },
    confirmLockAthletes() {
      Modal.confirm({
        title: this.$t("programs.confirm_lock_athletes"),
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => {
          this.lockAthletes();
        },
        onCancel() {
          console.log("Cancel");
        },
        class: "test",
      });
    },
    confirmLockSequences() {
      Modal.confirm({
        title: this.$t("programs.confirm_lock_sequences"),
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => {
          this.confirmProgramArrangement();
        },
        onCancel() {
          console.log("Cancel");
        },
        class: "test",
      });
    },
    onCreate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.post(route("manage.competitions.store"), this.modal.data, {
            onSuccess: (page) => {
              this.modal.data = {};
              this.modal.isOpen = false;
            },
            onError: (err) => {
              console.log(err);
            },
          });

          console.log("values", this.modal.data, this.modal.data);
        })
        .catch((error) => {
          message.error(error);
        });
    },
    getPartitionedPrograms() {
      this.initializing = true;
      this.competition.days.forEach((day) => {
        this.partitionedPrograms[day] = {};
        for (let s = 0; s != this.competition.section_number; s++) {
          this.partitionedPrograms[day][s + 1] = {};
          for (let m = 0; m != this.competition.mat_number; m++) {
            this.partitionedPrograms[day][s + 1][m + 1] = this.getProgramByDSM(
              day,
              s + 1,
              m + 1
            );
            this.partitionedPrograms[day][s + 1][m + 1].sort((a, b) => {
              return a.sequence - b.sequence;
            });
          }
        }
      });
      console.log(this.partitionedPrograms);
      this.initializing = false;
    },
    getProgramByDSM(date, section, mat) {
      return (
        this.filteredPrograms.filter((program) => {
          return (
            program.date === date && program.section === section && program.mat === mat
          );
        }) ?? []
      );
    },
    // 清除組別／公斤級篩選
    clearProgramFilters() {
      this.filterCategory = null;
      this.filterWeight = null;
    },
    lockAthletes() {
      this.$inertia.post(
        route("manage.competition.athletes.lock", this.competition.id),
        "",
        {
          onSuccess: (page) => {
            console.log(page);
          },
          onError: (err) => {
            console.log(err);
          },
        }
      );
    },
    toggleProgramChecked(program) {
      console.debug("toggleProgramChecked", program);
      console.debug("batchMoveForm", this.batchMoveForm);

      const isSameFromGroup =
        program.date === this.batchMoveForm.from.day &&
        program.section === this.batchMoveForm.from.section &&
        program.mat === this.batchMoveForm.from.mat;

      console.debug("isSameFromGroup", isSameFromGroup);

      if (!this.selectedPrograms.length || !isSameFromGroup) {
        this.selectedPrograms = [];
        this.batchMoveForm.from.day = program.date;
        this.batchMoveForm.from.section = program.section;
        this.batchMoveForm.from.mat = program.mat;
      }

      if (this.selectedPrograms.includes(program.id)) {
        this.selectedPrograms = this.selectedPrograms.filter((id) => id !== program.id);
      } else {
        this.selectedPrograms.push(program.id);
      }
    },
    onDragEnd(day, section, mat) {
      this.partitionedPrograms[day][section][mat].forEach((element, idx) => {
        element.sequence = idx + 1;
      });
    },
    saveDrag() {
      const programs = [];
      this.competition.days.forEach((day) => {
        for (let s = 0; s != this.competition.section_number; s++) {
          for (let m = 0; m != this.competition.mat_number; m++) {
            this.partitionedPrograms[day][s + 1][m + 1].forEach((program, index) => {
              program.sequence = index + 1;
              programs.push(program);
            });
          }
        }
      });
      this.editDraggable = false;
      this.multipleMove = false;
      this.$inertia.post(
        route("manage.competition.program.sequence.update", this.competition.id),
        programs,
        {
          onSuccess: (page) => {
            // 重新由伺服器回來的資料建立棋盤，避免畫面與實際排序不一致
            this.getPartitionedPrograms();
            this.$message.success(this.$t("programs.move_success"));
          },
        }
      );
    },
    savePrograms() {
      this.$inertia.post(
        route("manage.competition.programs-update", this.competition.id),
        this.programs,
        {
          onSuccess: (page) => {
            this.programsEdit = false;
            this.$message.success(this.$t("programs.save_success"));
          },
        }
      );
    },
    cancelDrag() {
      this.editDraggable = false;
      this.getPartitionedPrograms();
    },
    cancelMovePrograms() {
      this.selectedPrograms = [];
      this.multipleMove = false;
    },
    batchMovePrograms() {
      const day = this.batchMoveForm.day;
      const section = this.batchMoveForm.section;
      const mat = this.batchMoveForm.mat;

      console.debug("batchMoveForm", this.batchMoveForm);

      const fromSection = this.partitionedPrograms[this.batchMoveForm.from.day][
        this.batchMoveForm.from.section
      ][this.batchMoveForm.from.mat];
      const toSection = this.partitionedPrograms[day][section][mat];

      this.selectedPrograms.forEach((programId) => {
        // FIXME: old position not removed
        // remove from old position
        const program = fromSection.splice(
          fromSection.findIndex((program) => program.id === programId),
          1
        )[0];
        // change day section and mat
        program.date = day;
        program.section = section;
        program.mat = mat;

        // add to new position
        toSection.push(program);
      });

      this.selectedPrograms = [];
      this.saveDrag();
    },
    confirmProgramArrangement() {
      this.$inertia.post(
        route("manage.competition.program.lock", this.competition),
        null,
        {
          preserveScroll: true,
          onSuccess: () => {
            this.$message.success(this.$t("programs.sequence_confirmed"));
            this.$inertia.reload({
              preserveScroll: true,
              only: ["programs"],
            });
          },
        }
      );
    },
  },
};
</script>
<style scoped lang="less">
.ongoing-contests-list {
  :deep(.ant-list-items) {
    @apply flex flex-col gap-2;
  }
}

.borderless {
  :deep(.ant-card-body) {
    @apply py-0;
    @apply px-0;
  }
}
</style>