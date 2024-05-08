<template>
  <inertia-head title="Dashboard" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
      </div>
    </template>
    <div class="py-12 mx-4">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="flex justify-between">
          <div class="flex gap-3">
            <div class="text-xl font-bold">比賽項目管理</div>
            <div class="flex items-center">
              <a-tag color="processing">
                <template #icon>
                  <AppstoreOutlined />
                </template>
                {{ competition.mat_number }} Mats
              </a-tag>
              <a-tag color="processing">
                <template #icon>
                  <ScheduleOutlined />
                </template>
                {{ competition.section_number }} Sections
              </a-tag>
            </div>
          </div>
          <div class="">
            <a-button type="primary" @click="confirmProgramArrangement"
              >鎖定排序</a-button
            >
          </div>
        </div>
        <div class="flex w-full gap-6">
          <div class="flex flex-1 flex-col">
            <div class="flex justify-between">
              <div class="text-xl font-bold mt-6 mb-2">共 {{ programs.length }} 項目</div>
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
                <div class="text-xl font-bold">全部組別</div>
                <div class="">
                  <a-button type="link">
                    <template #icon> <DownloadOutlined /> </template>匯出pdf</a-button
                  >
                </div>
              </div>
              <a-table :dataSource="programs" :columns="columns">
                <template #bodyCell="{ column, record }">
                  <template v-if="column.dataIndex === 'operation'">
                    <a-button
                      :href="
                        route('manage.competition.programs.show', [
                          record.competition_id,
                          record.id,
                        ])
                      "
                      >View</a-button
                    >
                  </template>
                  <template v-if="column.dataIndex === 'athletes'">
                    <span>{{ record.athletes_count }}</span>
                  </template>
                  <template v-else>
                    {{ record[column.dataIndex] }}
                  </template>
                </template>
              </a-table>
            </div>
            <template v-else>
              <div class="pr-4 py-2 flex justify-between bg-white shadow-md rounded-sm">
                <div class="flex justify-start">
                  <div v-if="!editDraggable">
                    <div v-if="!multipleMove">
                      <a-button type="link" @click="multipleMove = !multipleMove"
                        >批量移動(場地/時段)</a-button
                      >
                    </div>
                    <div v-else class="flex gap-2 items-center pl-4">
                      <span> 將 {{ selectedPrograms.length }} 項移動至： </span>
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
                          >時段 {{ section }}
                        </a-select-option>
                      </a-select>
                      <a-select class="w-24" v-model:value="batchMoveForm.mat" name="mat">
                        <a-select-option
                          v-for="mat in competition.mat_number"
                          :id="`opt-mat-${mat}`"
                          :key="mat"
                          :value="mat"
                          >場地 {{ mat }}
                        </a-select-option>
                      </a-select>
                      <a-button @click="batchMovePrograms">移動並保存</a-button>
                      <a-button @click="cancelMovePrograms">取消</a-button>
                    </div>
                  </div>
                </div>
                <div class="flex justify-end">
                  <div v-if="!multipleMove">
                    <a-button
                      type="link"
                      v-if="!editDraggable"
                      @click="editDraggable = !editDraggable"
                      >編輯</a-button
                    >
                    <template v-else>
                      <a-button type="link" @click="saveDrag">保存</a-button>
                      <a-button type="link" @click="cancelDrag">取消</a-button>
                    </template>
                  </div>
                  <a-button type="link">
                    <template #icon> <DownloadOutlined /> </template>匯出pdf</a-button
                  >
                </div>
              </div>
              <div v-for="day in competition.days" class="mb-6" :key="day">
                <div class="text-2xl font-medium mb-6">{{ day }}</div>
                <div v-for="section in competition.section_number" :key="section">
                  <div class="font-bold text-lg mb-3">時段 {{ section }}</div>
                  <div class="grid grid-cols-2 gap-3 mb-6">
                    <a-card
                      v-for="mat in competition.mat_number"
                      :title="`場地 ${mat}`"
                      :key="mat"
                      class="borderless"
                      style="min-height: 300px"
                    >
                      <template #extra>
                        {{ matSecProgramsCount(day, section, mat) }}場,
                        {{ matSecMaxTimeEst(day, section, mat) }}
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
                                      contest: contest,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                    })
                                  "
                                  target="_blank"
                                  >下載上線表</a
                                >
                              </a-menu-item>

                              <a-menu-item>
                                <a
                                  :href="
                                    route('admin.contests.programs.pdf-mat-brackets', {
                                      contest: contest,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                      show_sequence: true,
                                    })
                                  "
                                  target="_blank"
                                  >下載上線表（場次安排次序）</a
                                >
                              </a-menu-item>

                              <a-menu-divider />

                              <a-menu-item>
                                <a
                                  :href="
                                    route('admin.contests.programs.pdf-mat-bouts', {
                                      contest: contest,
                                      date: day,
                                      section: section,
                                      mat: mat,
                                      show_sequence: true,
                                    })
                                  "
                                  target="_blank"
                                  >下載場次表</a
                                >
                              </a-menu-item>
                            </a-menu>
                          </template>
                        </a-dropdown>
                      </template>
                      <draggable
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
                          <div class="flex-1">
                            <div class="mb-2">
                              <a-tag>
                                {{ element.competition_category.name }}
                              </a-tag>
                              <a-tag class="uppercase"
                                >{{ element.contest_system }}
                              </a-tag>
                              {{ element.weight_code }}
                            </div>
                            <div class="text-sm text-neutral-500">
                              共 {{ element.athletes_count }} 人，{{
                                element.bouts_count
                              }}
                              場次
                            </div>
                          </div>
                        </div>
                      </draggable>
                      <!-- <draggable
                        :list="partitionedPrograms[day][section][mat]"
                        handle=".handle"
                        group="programs"
                        :id="`programs_${day}_${section}_${mat}`"
                        :emptyInsertThreshold="200"
                        @end="onDragEnd"
                        item-key="id"
                      >
                        <template #footer>
                          <div class="absolute w-full mt-6">
                            <a-empty
                              v-if="partitionedPrograms[day][section][mat].length === 0"
                            >
                              <template #description>
                                <p>尚未安排項目</p>
                                <p>拖動項目以安排場次</p>
                              </template>
                            </a-empty>
                          </div>
                        </template>
                        <template #item="{ element, index }">
                          <a-list-item :id="element.id">
                            <div class="flex items-center px-5 w-full">
                              <div class="w-12">
                                <a-checkbox
                                  v-if="isBatchEditing"
                                  :checked="isProgramChecked(element)"
                                  :value="element.id"
                                  :id="`chk-${element.id}`"
                                  @change="toggleProgramChecked(element)"
                                />
                                <a-tag v-else>{{ index + 1 }}</a-tag>
                              </div>
                              <div class="flex-1">
                                <div class="mb-2">
                                  <a-tag>
                                    {{ element.category.name }}
                                  </a-tag>
                                  <a-tag class="uppercase"
                                    >{{ element.contest_system_name }}
                                  </a-tag>
                                  {{ element.name }}
                                </div>
                                <div class="text-sm text-neutral-500">
                                  共 {{ element.athletes_count }} 人，{{
                                    element.bouts_count
                                  }}
                                  場次
                                </div>
                              </div>

                              <div>
                                <a-button
                                  type="text"
                                  class="handle"
                                  v-if="
                                    contest.status < CONTEST_STATUS.program_arranged &&
                                    contest.status >= CONTEST_STATUS.athletes_confirmed &&
                                    editing
                                  "
                                >
                                  <template #icon>
                                    <HolderOutlined />
                                  </template>
                                </a-button>
                              </div>
                            </div>
                          </a-list-item>
                        </template>
                      </draggable> -->
                    </a-card>
                  </div>
                </div>
              </div>
            </template>
          </div>
          <div class="w-72 flex flex-col">
            <div class="">
              <h3 class="font-bold text-lg mb-3">更多功能</h3>
              <div class="flex flex-col gap-2"><a>查看對陣</a></div>
            </div>
            <div class="">
              <h3 class="font-bold text-lg mb-3">文件匯出</h3>
              <div class="flex flex-col gap-2">
                <a>比賽秩序表</a>
                <a>各場地安排</a>
                <a>各組別項目表</a>
                <a>全部賽程表</a>
                <a>全部上線表</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import dayjs from "dayjs";
import { message } from "ant-design-vue";
import { VueDraggableNext } from "vue-draggable-next";
import duration from "dayjs/plugin/duration";
import {
  UnorderedListOutlined,
  AppstoreOutlined,
  HolderOutlined,
  LockOutlined,
  ScheduleOutlined,
  EnvironmentOutlined,
  DownloadOutlined,
  ClockCircleOutlined,
  MoreOutlined,
} from "@ant-design/icons-vue";

dayjs.extend(duration);
export default {
  components: {
    AdminLayout,
    UnorderedListOutlined,
    AppstoreOutlined,
    HolderOutlined,
    LockOutlined,
    ScheduleOutlined,
    EnvironmentOutlined,
    DownloadOutlined,
    ClockCircleOutlined,
    MoreOutlined,
    dayjs,
    draggable: VueDraggableNext,
  },
  props: ["competition", "programs", "athletes"],
  data() {
    return {
      view: "list",
      dateFormat: "YYYY-MM-DD",
      editDraggable: false,
      multipleMove: false,
      selectedPrograms: [],
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
      columns: [
        {
          title: "Seq.",
          dataIndex: "sequence",
        },
        {
          title: "Date",
          dataIndex: "date",
        },
        {
          title: "Category",
          dataIndex: "category_group",
        },
        {
          title: "Weight Group",
          dataIndex: "weight_code",
        },
        {
          title: "Mat",
          dataIndex: "mat",
        },
        {
          title: "Section",
          dataIndex: "section",
        },
        {
          title: "Contest System",
          dataIndex: "contest_system",
        },
        {
          title: "Duration",
          dataIndex: "duration_formatted",
        },
        {
          title: "Athletes",
          dataIndex: "athletes",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
      rules: {
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
      },
      validateMessages: {
        required: "${label} is required!",
        types: {
          email: "${label} is not a valid email!",
          number: "${label} is not a valid number!",
        },
        number: {
          range: "${label} must be between ${min} and ${max}",
        },
      },
    };
  },
  computed: {
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
  },
  watch: {
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
  created() {},
  methods: {
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
        this.programs.filter((program) => {
          return (
            program.date === date && program.section === section && program.mat === mat
          );
        }) ?? []
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
      this.$inertia.post(
        route("manage.competition.programs.sequence.update", this.competition.id),
        programs,
        {
          onSuccess: (page) => {
            this.editDraggable = false;
            console.log(page);
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
      this.multipleMove = false;
      this.$message.success("移動成功");
    },
    confirmProgramArrangement() {
      this.$inertia.post(route("manage.competition.programs.lock", this.competition), null, {
        preserveScroll: true,
        onSuccess: () => {
          this.$message.success("已確認比賽安排");
          this.$inertia.reload({
            preserveScroll: true,
            only: ["programs"],
          });
        },
      });
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
