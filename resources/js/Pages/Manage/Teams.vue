<template>
  <inertia-head title="運動員列表" />

  <ProgramLayout :competitionId="competition.id">
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">運動員列表</h2>
      </div>
    </template>
    <div class="py-12 mx-8">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="grid grid-cols-4 gap-12 py-4">
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
        <div class="p-4 mb-2 shadow-lg bg-white rounded-lg">
          <div class="flex justify-between">
            <div class="text-xl font-bold pb-2">Teams List</div>
            <div class="flex gap-3">
              <a-button type="primary" class="bg-blue-500" @click="onCreateRecord"
                >Add team</a-button
              >
            </div>
          </div>
          <a-table :dataSource="teams" :columns="columns">
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
      :title="modal.title"
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
            <div class="w-1/2">
              <a-form-item label="Abbreviation" name="abbreviation">
                <a-input type="input" v-model:value="modal.data.abbreviation" />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item label="Name" name="name">
                <a-input type="input" v-model:value="modal.data.name" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-1/2">
              <a-form-item label="Name Secondary" name="name_secondary">
                <a-input type="input" v-model:value="modal.data.name_secondary" />
              </a-form-item>
            </div>
            <div class="w-1/2">
              <a-form-item label="Leader" name="leader">
                <a-input type="input" v-model:value="modal.data.leader" />
              </a-form-item>
            </div>
          </div>
          <div class="text-right">
            <a-form-item>
              <a-button
                v-if="modal.mode == 'CREATE'"
                class="bg-blue-500"
                type="primary"
                @click="onCreate"
                >創建</a-button
              >
              <a-button
                v-if="modal.mode == 'EDIT'"
                class="bg-blue-500"
                type="primary"
                @click="onUpdate"
                >確定</a-button
              >
              <a-button style="margin-left: 10px" @click="modal.isOpen = false"
                >關閉</a-button
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
export default {
  props: ["competition", "programs", "teams"],
  data() {
    return {
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      columns: [
        {
          title: "Abbreviation",
          dataIndex: "abbreviation",
        },
        {
          title: "Name",
          dataIndex: "name",
        },
        {
          title: "Name Secondary",
          dataIndex: "name_secondary",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
      rules: {
        abbreviation: { required: true },
        name: { required: true },
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
  components: {
    ProgramLayout,
  },
  computed: {
    isEdit() {
      return (team) => team.id === this.form.id;
    },
  },
  methods: {
    onCreateRecord() {
      this.modal.title = "Create";
      this.modal.data = {};
      this.modal.mode = "CREATE";
      this.modal.isOpen = true;
    },
    onEditRecord(record) {
      this.modal.title = "Edit";
      this.modal.mode = "EDIT";
      this.modal.data = { ...record };
      this.modal.isOpen = true;
    },
    deleteTeam(id) {
      window.axios
        .delete(route("admin.contests.teams.destroy", [this.contest.id, id]))
        .then(() => {
          this.$message.success(trans("team.deleted"));
        })
        .catch(() => {
          this.$message.error(trans("action.delete-failed"));
        })
        .finally(() => {
          this.table.reload();
        });
    },
    onCreate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.post(
            route("manage.competition.teams.store", this.competition.id),
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
          console.log(error);
        });
    },
    onUpdate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.put(
            route("manage.competition.teams.update", {
              competition: this.competition.id,
              team: this.modal.data.id,
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
  },
};
</script>

<style scoped></style>
