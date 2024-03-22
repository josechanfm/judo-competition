<template>
  <inertia-head title="運動員列表" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">運動員列表</h2>
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
            <a-statistic title="Atheles" :value="competition.athletes.length" />
          </a-card>
        </div>
        <div class="p-4 mx-8 mb-2 shadow-lg border border-indigo-600 bg-white rounded-lg">
          <div class="text-xl font-bold pb-2">運動員列表</div>
          <a-table :dataSource="competition.athletes" :columns="columns">
            <template #bodyCell="{ column, record }">
              <template v-if="column.dataIndex === 'operation'">
                <a-button @click="onEditRecord(record)">Edit</a-button>
              </template>
              <template v-else>
                {{ record[column.dataIndex] }}
              </template>
            </template>
          </a-table>
        </div>
      </div>
    </div>
    <a-modal
      v-model:open="modal.isOpen"
      width="1000px"
      :footer="null"
      title="Basic Modal"
    >
      <a-form
        name="ModalForm"
        ref="formRef"
        :model="modal.data"
        layout="vertical"
        autocomplete="off"
        :rules="rules"
        :validate-messages="validateMessages"
      >
        <div class="flex flex-col">
          <div class="flex justify-between gap-3">
            <div class="w-1/3">
              <a-form-item label="Name" name="name_zh">
                <a-input v-model:value="modal.data.name_zh" />
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item label="Name Pt" name="name_pt">
                <a-input v-model:value="modal.data.name_pt" />
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item label="Name display" name="name_display">
                <a-input v-model:value="modal.data.name_display" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-1/2">
              <a-form-item label="Gender" name="gender">
                <a-select
                  v-model:value="modal.data.gender"
                  :options="genders.map((item) => ({ value: item }))"
                />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item label="Team" name="team_id">
                <a-select
                  v-model:value="modal.data.team_id"
                  :options="
                    teams.map((item) => ({ value: item.id, label: item.name_zh }))
                  "
                />
              </a-form-item>
            </div>
          </div>
          <div class="text-right">
            <a-form-item>
              <a-button v-if="modal.mode == 'CREATE'" type="primary" @click="onCreate"
                >Create</a-button
              >
              <a-button v-if="modal.mode == 'EDIT'" type="primary" @click="onUpdate"
                >Update</a-button
              >
              <a-button style="margin-left: 10px" @click="this.modal.isOpen = false"
                >Close</a-button
              >
            </a-form-item>
          </div>
        </div>
      </a-form>
    </a-modal>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
export default {
  components: {
    AdminLayout,
  },
  props: ["competition", "programs", "teams"],
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
          dataIndex: "name_zh",
        },
        {
          title: "Gender",
          dataIndex: "gender",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
      genders: ["M", "F"],
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
  created() {
    console.log(this.competition);
  },
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
            route("manage.competition.athletes.update", {
              competition: this.competition.id,
              athlete: this.modal.data.id,
            }),
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
    onEditRecord(record) {
      this.modal.isOpen = true;
      this.modal.title = "Edit";
      this.modal.mode = "EDIT";
      this.modal.data = { ...record };
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
