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
        <div class="text-right">
          <a-button v-if="competition.status === 0" type="primary" @click="lockAthletes"
            >鎖定名單</a-button
          >
          <span v-else class="text-blue-500">名單已鎖定</span>
        </div>
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
        <div class="p-4 mb-2 shadow-lg border border-indigo-600 bg-white rounded-lg">
          <div class="flex justify-between">
            <div class="text-xl font-bold pb-2">運動員列表</div>
            <div class="flex gap-3">
              <a-button
                type="primary"
                v-if="competition.status === 0"
                @click="onCreateRecord"
                >新增運動員</a-button
              >
              <a-button
                type="primary"
                v-if="competition.status === 0"
                @click="visible = true"
                >匯入運動員</a-button
              >
            </div>
          </div>
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
                >創建</a-button
              >
              <a-button v-if="modal.mode == 'EDIT'" type="primary" @click="onUpdate"
                >確定</a-button
              >
              <a-button style="margin-left: 10px" @click="this.modal.isOpen = false"
                >關閉</a-button
              >
            </a-form-item>
          </div>
        </div>
      </a-form>
    </a-modal>
    <a-modal title="匯入運動員名單" v-model:open="visible">
      <a-upload-dragger
        v-model:fileList="files"
        name="file"
        @change="handleFileChange"
        :beforeUpload="() => false"
        :multiple="false"
      >
        <p class="ant-upload-drag-icon">
          <file-excel-outlined />
        </p>
        <p class="ant-upload-text">點擊或拖動文件到此以匯入運動員</p>
        <p class="ant-upload-hint">支援 xlsx 文件上傳，上傳前請確認數據格式同模板相同</p>
      </a-upload-dragger>

      <div class="mt-3" v-if="imported">
        <div class="font-bold my-3 text-green-500" v-if="errors.length === 0">
          匯入成功！
        </div>
        <div class="font-bold my-3 text-yellow-500" v-else>部分資料未導入</div>
        <p v-for="(error, index) in errors" :key="index" class="font-mono m-0">
          <warning-outlined class="!text-yellow-500" />
          row {{ error.row }}, {{ error.errors[0] }}
        </p>
      </div>
      <template #footer>
        <div class="flex w-full justify-between">
          <a href="/templates/athlete_list.xlsx">
            <a-button type="link">
              <template #icon>
                <DownloadOutlined />
              </template>
              下載運動員名單模板
            </a-button>
          </a>
          <a-button type="primary" @click="handleImport" :disabled="files.length === 0"
            >匯入</a-button
          >
        </div>
      </template>
    </a-modal>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { message } from "ant-design-vue";
import {
  DownloadOutlined,
  FileExcelOutlined,
  WarningOutlined,
} from "@ant-design/icons-vue";
export default {
  components: {
    DownloadOutlined,
    FileExcelOutlined,
    WarningOutlined,
    ProgramLayout,
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
      files: [],
      visible: false,
      errors: [],
      imported: false,
    };
  },
  created() {
    console.log(this.competition);
  },
  methods: {
    onCreateRecord() {
      this.modal.title = "Create";
      this.modal.isOpen = true;
      this.modal.data = {};
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
          this.$inertia.post(
            route("manage.competition.athletes.store", this.competition.id),
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
    handleImport() {
      const formData = new FormData();
      formData.append("file", this.files[0].originFileObj);

      // TODO: handle import athlete list
      window.axios
        .post(
          route("manage.competition.athletes.import", this.$page.props.competition.id),
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then(({ data }) => {
          this.$message.success("匯入成功");
          this.files = [];
          this.errors = data.errors;
          this.imported = true;
          this.$inertia.reload();
          this.$emit("imported");
        })
        .catch(() => {
          this.$message.error("匯入失敗");
        });
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
    handleFileChange(info) {
      this.imported = false;
      this.errors = [];
    },
  },
};
</script>
