<template>
  <ProgramLayout>
    <a-page-header title="運動員過磅" class="px-6 max-w-7xl !mx-auto">
      <template #extra>
        <template v-if="competition.status >= COMPETITION_STATUS.athletes_confirmed">
          <a-button
            v-if="
              this.athletes.find((x) => x.confirm == 0 && x.is_weight_passed == null) !=
              undefined
            "
            type="link"
          >
            待全部過磅
          </a-button>
          <a-button
            v-else-if="this.athletes.find((x) => x.confirm == 1) == undefined"
            type="primary"
            @click="confirmAllWeight"
          >
            完成過磅
          </a-button>

          <!-- <a-button
            type="link"
            v-if="this.athletes.find((x) => x.confirmed == 0) == undefined"
            >完成過磅</a-button
          > -->
          <a-button v-else type="link"> 已完成過磅 </a-button>
          <!-- <a :href="route('manage.competition.weights.pdf', competition)" target="_blank">
            <a-button type="link"> 下載過磅表 </a-button>
          </a>
          <a :href="route('manage.competition.weights.fail.pdf', competition)" target="_blank">
            <a-button type="link"> 下載過磅失敗表 </a-button>
          </a> -->
        </template>
      </template>
    </a-page-header>

    <div class="p-6 max-w-7xl mx-auto flex flex-col gap-4 h-screen">
      <template v-if="competition.status >= COMPETITION_STATUS.program_arranged">
        <a-alert
          type="success"
          show-icon
          v-if="competition.status === COMPETITION_STATUS.weight_confirmed"
          message="過磅已完成"
          description="恭喜您，現在可以開始比賽了！"
        />
        <div class="font-bold text-lg">共 {{ athletes.length }} 人</div>
        <div class="flex justify-between">
          <div class="flex gap-2">
            <a-select
              placeholder="日期"
              v-model:value="filtered.date"
              :default-value="mappedDays[0]"
              @change="applyFilter"
              class="w-full md:w-48"
              :options="mappedDays"
            ></a-select>
            <a-select
              placeholder="組別"
              :options="mappedCategories"
              class="w-full md:w-48"
              allowClear
              v-model:value="filtered.category"
            >
            </a-select>
            <a-select
              placeholder="公斤級"
              class="w-full md:w-48"
              :options="filteredWeights"
              @change="applyFilter"
              allowClear
              v-model:value="filtered.weight_code"
            ></a-select>
          </div>

          <div>
            <a-input-search
              show-icon
              clearable
              placeholder="運動員姓名"
              v-model:value="filtered.name"
              @search="applyFilter"
            />
          </div>
        </div>
        <a-table
          :dataSource="programAthletes.data"
          :columns="columns"
          :row-class-name="
            (record, index) =>
              record.is_weight_passed !== null
                ? record.is_weight_passed === 0
                  ? 'red'
                  : 'green'
                : 'gray'
          "
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'athlete'">
              <div>{{ record.athlete.name_zh }}</div>
            </template>
            <template v-if="column.key === 'program'">
              <div>
                {{ record.program.weight_code }} &centerdot;
                {{ record.program.competition_category.name }}
              </div>
            </template>
            <template v-if="column.dataIndex === 'result'">
              <CheckCircleOutlined
                v-if="record.is_weight_passed == 1"
                class="!text-green-800 dark:!text-green-400"
              />
              <CloseCircleOutlined
                v-else-if="record.is_weight_passed == 0"
                class="!text-red-800 dark:!text-red-400"
              />
              <QuestionCircleOutlined v-else />
            </template>
            <template v-else-if="column.dataIndex === 'weight'">
              <div class="flex gap-3 justify-end">
                <div class="font-bold flex gap-1 items-center" v-if="!record.confirmed">
                  <a-input-number
                    v-model:value="record.weight"
                    :default-value="0"
                    name="weight"
                    :min="0"
                    :max="999"
                    :precision="2"
                    :step="0.01"
                  ></a-input-number
                  >kg
                </div>
                <span v-else class="font-bold"> {{ record.weight }} kg </span>
                <a-button
                  @click="passWeight(record)"
                  name="pass"
                  shape="circle"
                  v-if="!record.confirmed"
                >
                  <template #icon>
                    <CheckOutlined />
                  </template>
                </a-button>

                <a-button
                  @click="failWeight(record)"
                  name="fail"
                  shape="circle"
                  v-if="!record.confirmed"
                >
                  <template #icon>
                    <CloseOutlined />
                  </template>
                </a-button>
              </div>
            </template>
            <template v-else>
              {{ record[column.dataIndex] }}
            </template>
          </template>
        </a-table>
        <!-- <a-progress
            :steps="programAthletes.total"
            :percent="percent"
            v-if="filtered.weight_code"
          /> -->
      </template>

      <template v-else>
        <a-card>
          <a-empty>
            <template #description>
              <h3 class="font-bold text-lg">抽籤未完成</h3>
              <p>抽籤未完成</p>
            </template>
          </a-empty>
        </a-card>
      </template>
    </div>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { Modal } from "ant-design-vue";
import {
  CheckCircleOutlined,
  CloseCircleOutlined,
  CheckOutlined,
  CloseOutlined,
  MoreOutlined,
  QuestionCircleOutlined,
} from "@ant-design/icons-vue";
import { weightParser } from "@/Utils/weightParser";
import { COMPETITION_STATUS } from "@/constants.js";

export default {
  name: "Weights",
  components: {
    ProgramLayout,
    MoreOutlined,
    CheckCircleOutlined,
    CloseCircleOutlined,
    CheckOutlined,
    CloseOutlined,
    QuestionCircleOutlined,
  },
  props: {
    competition: {
      type: Object,
      required: true,
    },
    programAthletes: {
      type: Object,
      required: true,
    },
    categories: {
      type: Array,
      required: true,
    },
    weights: {
      type: Array,
      required: true,
    },
    programs: {
      type: Object,
      required: true,
    },
    athletes: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const columns = [
      {
        key: "result",
        title: "過磅結果",
        dataIndex: "result",
        width: 100,
      },
      {
        key: "athlete",
        title: "運動員",
      },
      {
        key: "program",
        title: "組別",
      },
      {
        key: "weight",
        title: "過磅重量",
        dataIndex: "weight",
        align: "right",
      },
    ];

    const mappedDays = props.competition.days.map((day) => ({
      label: day,
      value: day,
    }));

    const mappedCategories = props.categories.map((category) => ({
      label: category.name + " / " + category.name_secondary,
      value: category.id,
    }));

    return { columns, COMPETITION_STATUS, mappedDays, mappedCategories };
  },
  mounted() {
    if (this.filtered.date === undefined) {
      this.filtered.date = [this.competition.days[0]];
    }
  },
  computed: {
    canFinishWeights() {
      return (
        this.athletes.find((x) => x.is_weight_passed !== null && x.confirmed == 0) ==
        this.athletes
      );
    },
    filteredWeights() {
      return this.programs
        .filter(
          (program) =>
            program.category_id === this.filtered.category &&
            program.date === this.filtered.date
        )
        ?.map((program) => ({
          label: weightParser(program.weight_code).nameZh,
          weight: weightParser(program.weight_code),
          value: program.weight_code,
        }));
    },
    percent() {
      return Math.round(
        this.athletes.total === 0
          ? 0
          : (this.athletes.data.filter(
              (programAthlete) => programAthlete.athlete.weight !== 0
            ).length /
              this.athletes.total) *
              100
      );
    },
  },
  data() {
    return {
      category: null,
      weight: null,
      filtered: {},
    };
  },
  watch: {
    "table.filtered.category"() {
      this.table.filtered.weight_code = this.filteredWeights[0]?.value;
      this.table.applyFilter();
    },
  },
  methods: {
    updatePassed(record, value) {
      if (record.weight === 0) {
        this.$message.error("請先輸入過磅重量");
        return;
      }

      this.$inertia
        .post(
          route("competition.program-athletes.weight.update", {
            competition: this.competition.id,
            programAthlete: record.id,
            weight: record.weight,
          }),
          null,
          {
            preserveState: false,
            preserveScroll: true,
          }
        )
        .then(() => {
          this.$message.success("更新成功");

          // TODO: fixme, this is a workaround
          setTimeout(() => {
            this.bc.postMessage({
              type: "update",
              payload: JSON.parse(JSON.stringify(record)),
            });
          }, 1000);
        })
        .catch((error) => {
          this.$message.error("更新失敗");
          console.log(error);
        });
    },
    passWeight(record) {
      if (record.weight === null) {
        this.$message.error("過磅重量未錄入");
        return;
      }

      this.$inertia.post(
        route("manage.competition.programAthlete.weight-pass", {
          weight: record.weight,
          competition: this.competition.id,
          programAthlete: record.id,
        }),
        null,
        {
          preserveState: false,
          preserveScroll: true,
          onSuccess: (page) => {
            this.$message.success("已確定");
          },
          onError: (error) => {
            this.$message.error(error.response.data.message);
          },
        }
      );
    },
    failWeight(record) {
      if (record.weight === null) {
        this.$message.error("過磅重量未錄入");
        return;
      }

      this.$inertia.post(
        route("manage.competition.programAthlete.weight-fail", {
          weight: record.weight,
          competition: this.competition.id,
          programAthlete: record.id,
        }),
        null,
        {
          preserveState: false,
          preserveScroll: true,
        }
      );
    },
    applyFilter() {},
    confirmAllWeight() {
      console.log(this.filtered.date[0]);
      Modal.confirm({
        title: "確認過磅",
        content: "確認過磅後，將無法再修改過磅結果",
        okText: "確認",
        cancelText: "取消",
        onOk: () => {
          window.axios
            .post(
              route("manage.competition.athletes.weights.lock", {
                competition: this.competition.id,
                date: this.filtered.date[0],
              })
            )
            .then(() => {
              this.$message.success("過磅已確認");
              this.$inertia.reload();
            })
            .catch((error) => {
              this.$message.error(error.response.data.message);
            });
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
