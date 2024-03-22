<template>
  <inertia-head title="Dashboard" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
      </div>
    </template>
    <div class="py-12">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="grid grid-cols-4 gap-12 py-4 mx-8">
          <a-card class="shadow-lg">
            <a-statistic title="Status" value="Ready to start" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic
              title="Date"
              :value="competition.date_start + ' ~ ' + competition.date_end"
            />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic title="Programs" :value="programs.length" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic title="Atheles" :value="athletes.length" />
          </a-card>
        </div>
        <div class="p-4 mx-8 mb-2 shadow-lg bg-white rounded-lg">
          <div class="text-xl font-bold pb-2">全部組別</div>
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
                <span>{{ record.athletes.length }}</span>
              </template>
              <template v-else>
                {{ record[column.dataIndex] }}
              </template>
            </template>
          </a-table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
export default {
  components: {
    AdminLayout,
  },
  props: ["competition", "programs", "athletes"],
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
          dataIndex: "weight_group",
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
          dataIndex: "duration",
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
          console.log("error", error);
        });
    },
  },
};
</script>
