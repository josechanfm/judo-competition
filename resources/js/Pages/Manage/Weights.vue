<template>
  <ProgramLayout :competition="competition">
    <a-page-header title="Weigh-in"> </a-page-header>
    <template v-if="competition.status >= 3">
      <div class="mx-6 flex flex-col gap-3">
        <div class="bg-white p-2 rounded-md shadow-md">
          <div class="text-xl font-bold mb-4">Weight Choose</div>
          <a-form>
            <a-form-item label="Category" name="category">
              <a-radio-group
                v-model:value="categoryId"
                button-style="solid"
                @change="onChangeCategory"
              >
                <template v-for="category in competition.categories" :key="category">
                  <a-radio-button :value="category.id">{{
                    category.name
                  }}</a-radio-button>
                </template>
              </a-radio-group>
            </a-form-item>
            <a-form-item label="Program" name="program">
              <a-select
                :options="programs"
                v-model:value="programId"
                :fieldNames="{ value: 'id', label: 'weight_code' }"
                @change="onChangeProgram"
              />
            </a-form-item>
          </a-form>
        </div>
        <div v-if="program.athletes != null">
          <a-button
            v-if="
              this.program.athletes.find(
                (x) => x.pivot.confirm == 0 && x.pivot.is_weight_passed == null
              ) != undefined
            "
            type="link"
          >
            Program athletes not weighed
          </a-button>

          <a-button
            v-else-if="
              this.program.athletes.find((x) => x.pivot.confirm == 1) == undefined
            "
            type="primary"
            class="bg-blue-500"
            @click="lockProgramWeighIn"
          >
            Lock weigh-in
          </a-button>
          <div class="flex gap-2 items-center" v-else>
            <span>Program athletes weighed</span>
            <a-button type="primary" @click="cancelLockWeighIn">Cancel Lock</a-button>
          </div>
        </div>
        <a-table :dataSource="program.athletes" :columns="columns">
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'result'">
              <CheckCircleOutlined
                v-if="record.pivot.is_weight_passed == 1"
                class="!text-green-800 dark:!text-green-400"
              />
              <CloseCircleOutlined
                v-else-if="record.pivot.is_weight_passed == 0"
                class="!text-red-800 dark:!text-red-400"
              />
              <QuestionCircleOutlined v-else />
            </template>
            <template v-if="column.key === 'program'">
              <span>
                {{ program.weight_code }}
                {{ program.competition_category.name }}
              </span>
            </template>
            <template v-else-if="column.dataIndex === 'weight'">
              <div class="flex gap-3 justify-end">
                <div
                  class="font-bold flex gap-1 items-center"
                  v-if="!record.pivot.confirm"
                >
                  <a-input-number
                    v-model:value="record.pivot.weight"
                    :default-value="0"
                    name="weight"
                    :min="0"
                    :max="999"
                    :precision="2"
                    :step="0.01"
                  ></a-input-number
                  >kg
                </div>
                <span v-else class="font-bold"> {{ record.pivot.weight }} kg </span>
                <a-button
                  @click="passWeight(record)"
                  name="pass"
                  shape="circle"
                  v-if="!record.pivot.confirm"
                >
                  <template #icon>
                    <CheckOutlined />
                  </template>
                </a-button>
              </div>
            </template>
            <template v-else>
              {{ record[column.dataIndex] }}
            </template>
          </template>
        </a-table>
      </div>
    </template>
    <template v-else>
      <div class="p-6">
        <a-card>
          <a-empty>
            <template #description>
              <h3 class="font-bold text-lg">Not draw</h3>
              <p>draw not finish</p>
              <inertia-link
                :href="route('manage.competition.athletes.drawControl', competition.id)"
              >
                <a-button type="primary" class="bg-blue-500">
                  go to athletes draw
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
  props: ["competition", "programs_athletes"],
  // programs: {
  //   type: Object,
  //   required: true,
  // },
  data() {
    return {
      categoryId: null,
      programId: null,
      programs: [],
      program: {},
      athletes: [],
      columns: [
        {
          key: "name_display",
          title: "Athletes",
          dataIndex: "name_display",
        },
        {
          key: "result",
          title: "Weigh-in result",
          dataIndex: "result",
          width: 150,
        },
        {
          key: "program",
          title: "Program",
        },
        {
          key: "weight",
          title: "Weight",
          dataIndex: "weight",
          align: "right",
        },
      ],
    };
  },
  methods: {
    onChangeCategory(event) {
      this.programs = this.programs_athletes.filter(
        (p) => p.competition_category_id == event.target.value
      );
      this.programId = null;
      this.program = [];
      console.log(event.target.value);
    },
    onChangeProgram(event) {
      this.program = this.programs_athletes.find((p) => p.id == event);
      console.log(this.program);
    },

    passWeight(record) {
      if (record.pivot.weight === null) {
        this.$message.error("Weight not input");
        return;
      }

      this.$inertia.post(
        route("manage.competition.programAthlete.weightChecked", {
          weight: record.pivot.weight,
          competition: this.competition.id,
          programAthlete: record.pivot.id,
        }),
        null,
        {
          //preserveState: false,
          preserveScroll: true,
          onSuccess: (page) => {
            this.$message.success("Weight recorded");
            this.onChangeProgram(this.programId);
          },
          onError: (error) => {
            this.$message.error(error.response.data.message);
          },
        }
      );
    },

    lockProgramWeighIn() {
      Modal.confirm({
        title: "Confirm Lock?",
        content: "Do you want to lock the weigh-in record?",
        okText: "OK",
        cancelText: "Cancel",
        onOk: () => {
          this.$inertia.post(
            route("manage.competition.athletes.weights.lock", {
              competition: this.competition.id,
              program: this.program.id,
            }),
            null,
            {
              //preserveState: false,
              preserveScroll: true,
              onSuccess: (page) => {
                this.$message.success("Already lock");
                this.$inertia.reload({
                  preserveScroll: true,
                  only: ["programs_athletes"],
                });
                this.onChangeProgram(this.program.id);
              },
              onError: (error) => {
                this.$message.error(error.response.data.message);
              },
            }
          );
        },
      });
    },
    cancelLockWeighIn() {
      Modal.confirm({
        title: "Cancel lock?",
        content: "Do you want to cancel the locked wight-in record?",
        okText: "OK",
        cancelText: "Cancel",
        onOk: () => {
          this.$inertia.post(
            route("manage.competition.athletes.weights.cancelLock", {
              competition: this.competition.id,
              program: this.program.id,
            }),
            null,
            {
              //preserveState: false,
              preserveScroll: true,
              onSuccess: (page) => {
                this.$message.success("Already cancel lock");
                this.$inertia.reload({
                  preserveScroll: true,
                  only: ["programs_athletes"],
                });
                this.onChangeProgram(this.program.id);
              },
              onError: (error) => {
                this.$message.error(error.response.data.message);
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
