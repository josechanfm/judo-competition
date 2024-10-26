<template>
  <inertia-head title="Dashboard" />

  <ProgramLayout :competition="competition">
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
      </div>
    </template>
    <a-typography-title :level="3">Competition Info:</a-typography-title>
    <div>Country: {{ competition.country }}</div>
    <div>Scale: {{ competition.scale }}</div>
    <div>name: {{ competition.name }}</div>
    <div>Name Secondary{{ competition.name_secondary }}</div>
    <div>Dates: {{ competition.dates }}</div>
    <a-typography-title :level="3">Program Info:</a-typography-title>
    <div>{{ program.date }}</div>
    <div>{{ program.competition_system }}</div>
    <div>{{ program.chat_size }}</div>
    <div>{{ program.weight_code }}</div>
    <div>{{ program.competition_category.name }}</div>
    <div>{{ program.competition_category.weights }}</div>
    
    
    <a-table :dataSource="athletes" :columns="columns">
      <template #bodyCell="{column, record, text}">
        <template v-if="column.dataIndex=='operation'">
            
        </template>
        <template v-else-if="column.dataIndex=='seed'">
          {{ record.pivot.seed }}
        </template>
        <template v-else-if="column.dataIndex=='is_weight_passed'">
          {{ record.pivot.is_weight_passed }}
        </template>
      </template>
    </a-table>

    
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
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
    ProgramLayout,
    UnorderedListOutlined,
    AppstoreOutlined,
    HolderOutlined,
    LockOutlined,
    ScheduleOutlined,
    EnvironmentOutlined,
    DownloadOutlined,
    ClockCircleOutlined,
    MoreOutlined,
    draggable: VueDraggableNext,
  },
  props: ["competition", "program", "athletes"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      columns: [
        {
          title: "Name",
          dataIndex: "name",
        },{
          title: "Name Display",
          dataIndex: "name_display",
        },{
          title: "Gender",
          dataIndex: "gender",
        },{
          title: "Seed",
          dataIndex: "seed",
        },{
          title: "Weight Passed",
          dataIndex: "is_weight_passed",
        },{
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
  },
  watch: {
  },
  created() {},
  methods: {
  },
};
</script>
