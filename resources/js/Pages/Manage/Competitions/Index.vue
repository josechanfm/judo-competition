<template>
  <inertia-head title="賽事管理" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">賽事管理</h2>
      </div>
    </template>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">賽事管理</div>
        <div>
          <inertia-link :href="route('manage.competitions.create')"
            ><a-button class="bg-white">創建新的賽事</a-button>
          </inertia-link>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <a-table :dataSource="competitions" :columns="columns">
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'operation'">
              <a-button :href="route('manage.competitions.edit', record.id)"
                >Edit</a-button
              >
              <a-button :href="route('manage.competition.athletes.index', record.id)"
                >運動員</a-button
              >
              <a-button :href="route('manage.competition.programs.index', record.id)"
                >Manage</a-button
              >
              <a-button :href="route('manage.competition.progress', record.id)"
                >Progress</a-button
              >
            </template>
            <template v-else>
              {{ record[column.dataIndex] }}
            </template>
          </template>
        </a-table>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Dayjs } from "dayjs";
import moment from "moment";
export default {
  components: {
    AdminLayout,
  },
  props: ["countries", "gameTypes", "competitions", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      disabledDate: null,
      tmpContestTime: null,
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
        },
        {
          title: "Country",
          dataIndex: "country",
        },
        {
          title: "Date Start",
          dataIndex: "date_start",
        },
        {
          title: "Date End",
          dataIndex: "date_end",
        },
        {
          title: "Mat Number",
          dataIndex: "mat_number",
        },
        {
          title: "Section Number",
          dataIndex: "section_number",
        },
        {
          title: "Status",
          dataIndex: "status",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
      rules: {
        game_type_id: { required: true },
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
        days: { required: true },
        mat_number: { required: true },
        section_number: { required: true },
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
    selectLanguage() {
      return this.languages.map((x) => {
        console.log(x);
        if (
          x.value == this.modal.data?.competition_type.language ||
          x.value == this.modal.data?.competition_type.language_secondary
        ) {
          return { ...x, disabled: true };
        } else {
          return x;
        }
      });
    },
  },
  created() {
    this.disabledDate = (current) => {
      if (!this.modal.data.date_start && !this.modal.data.date_end) {
        return false;
      }
      return (
        current < moment(this.modal.data.date_start).valueOf() ||
        current > moment(this.modal.data.date_end).valueOf()
      );
    };
    this.endDateDisabled = (current) => {
      if (!this.modal.data.date_start) {
        return false;
      }
      return current < moment(this.modal.date_start).valueOf();
    };
  },
  methods: {
    onCreateRecord() {
      this.modal.title = "Create";
      this.modal.isOpen = true;
      this.modal.mode = "CREATE";
      this.modal.data = {
        days: [],
      };
      this.tmpContestTime = null;
    },
    onEditRecord(record) {
      this.modal.isOpen = true;
      this.modal.title = "Edit";
      this.modal.mode = "EDIT";
      this.modal.data = { ...record };

      console.log(moment().add(7, "days"));
      console.log(moment(this.modal.data.date_start));
    },
    addTimeToForm() {
      // should be unique
      if (this.modal.data.days.includes(this.tmpContestTime) || !this.tmpContestTime) {
        return;
      }

      this.modal.data.days.push(this.tmpContestTime);
    },
    removeTimeFromForm(time) {
      this.modal.data.days = this.modal.data.days.filter((t) => t !== time);
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
                this.modal.title = "";
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
          console.log("error", error);
        });
    },
  },
};
</script>
