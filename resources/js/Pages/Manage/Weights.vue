<template>
  <ProgramLayout :competitionId="competition.id">
    <h3>{{ competition.name }}</h3>
    <a-form>
      <a-form-item label="Category" name="category">
        <a-radio-group
          v-model:value="categoryId"
          button-style="solid"
          @change="onChangeCategory"
        >
          <template v-for="category in competition.categories">
            <a-radio-button :value="category.id">{{
              category.name
            }}</a-radio-button>
          </template>
        </a-radio-group>
      </a-form-item>
      <a-form-item label="Program" name="program">
        <a-select
          :options="programs"
          :fieldNames="{ value: 'id', label: 'weight_code' }"
          @change="onChangeProgram"
        />
      </a-form-item>
    </a-form>


    <div>
      <a-button
        v-if="
          this.athletes.find(
            (x) => x.confirm == 0 && x.is_weight_passed == null
          ) != undefined
        "
        type="link"
      >
        待全部過磅
      </a-button>
      <a-button
        v-else-if="this.athletes.find((x) => x.confirm == 1) == undefined"
        type="primary"
        @click="confirmAllWeig123ht"
      >
        完成過磅
      </a-button>
    </div>
    <a-table :dataSource="program.athletes" :columns="columns">
      <template #bodyCell="{ column, record }">
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
              v-if="!record.confirmed"
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
              v-if="!record.confirmed"
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
  props: ["competition"],
    // programs: {
    //   type: Object,
    //   required: true,
    // },
  data() {
    return {
      categoryId: null,
      programs: [],
      program: {
        athletes:[]
      },
      athletes: [],
      columns:[
        {
          key: "name_display",
          title: "運動員",
          dataIndex: "name_display",
        },
        {
          key: "result",
          title: "過磅結果",
          dataIndex: "result",
          width: 100,
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
      ]
    };
  },
  methods: {
    onChangeCategory(event) {
      this.programs = this.competition.programs_athletes.filter(
        (p) => p.competition_category_id == event.target.value
      );
      console.log(event.target.value);
    },
    onChangeProgram(event) {
      this.program = this.competition.programs_athletes.find((p) => p.id == event);
    },

    passWeight(record) {
      if (record.pivot.weight === null) {
        this.$message.error("過磅重量未錄入");
        return;
      }

      this.$inertia.post(
        route("manage.competition.programAthlete.weightChecked", {
          weight: record.pivot.weight,
          competition: this.competition.id,
          programAthlete: record.id,
        }),
        null,
        {
          //preserveState: false,
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
