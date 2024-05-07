<template>
  <a-modal title="匯入運動員名單" v-model:visible="visible">
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
</template>

<script>
import {
  DownloadOutlined,
  FileExcelOutlined,
  WarningOutlined,
} from "@ant-design/icons-vue";

export default {
  name: "ImportAthleteModal",
  components: {
    DownloadOutlined,
    FileExcelOutlined,
    WarningOutlined,
  },
  data() {
    return {
      files: [],
      errors: [],
      imported: false,
    };
  },
  methods: {
    handleImport() {
      console.log(this.$page.props);
      const formData = new FormData();
      formData.append("file", this.files[0].originFileObj);

      // TODO: handle import athlete list
      window.axios
        .post(
          route("admin.competitions.athletes.import", this.$page.props.competition.id),
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

          this.$emit("imported");
        })
        .catch(() => {
          this.$message.error("匯入失敗");
        });
    },
    handleFileChange(info) {
      this.imported = false;
      this.errors = [];
    },
  },
};
</script>

<style scoped></style>
