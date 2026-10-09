<template>
  <a-modal
    :title="$t('weights.import_modal.title')"
    :open="open"
    width="640px"
    @update:open="(value) => $emit('update:open', value)"
  >
    <a-upload-dragger
      v-model:file-list="files"
      name="file"
      :before-upload="() => false"
      :multiple="false"
      :max-count="1"
      @remove="reset"
    >
      <p class="ant-upload-drag-icon">
        <FileExcelOutlined />
      </p>
      <p class="ant-upload-text">{{ $t("weights.import_modal.upload_text") }}</p>
      <p class="ant-upload-hint">{{ $t("weights.import_modal.upload_hint") }}</p>
    </a-upload-dragger>

    <div v-if="imported" class="mt-4">
      <div
        class="my-3 font-bold"
        :class="errors.length ? 'text-yellow-500' : 'text-green-500'"
      >
        {{
          errors.length
            ? $t("weights.import_modal.partial", { count: updated })
            : $t("weights.import_modal.success", { count: updated })
        }}
      </div>
      <p v-for="(error, index) in errors" :key="index" class="font-mono m-0">
        <WarningOutlined class="!text-yellow-500" />
        {{ $t("weights.import_modal.row_error", { row: error.row }) }}
        {{ error.message }}
      </p>
    </div>

    <template #footer>
      <div class="flex w-full items-center justify-between gap-2">
        <a :href="exportUrl">
          <a-button type="link">
            <template #icon><DownloadOutlined /></template>
            {{ $t("weights.import_modal.download_template") }}
          </a-button>
        </a>
        <div class="flex gap-2">
          <a-button @click="$emit('update:open', false)">
            {{ $t("action.cancel") }}
          </a-button>
          <a-button
            type="primary"
            class="bg-blue-500"
            :loading="importing"
            :disabled="files.length === 0"
            @click="handleImport"
          >
            {{ $t("weights.import_modal.button") }}
          </a-button>
        </div>
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
  name: "ImportWeighInModal",
  components: {
    DownloadOutlined,
    FileExcelOutlined,
    WarningOutlined,
  },
  props: {
    open: { type: Boolean, default: false },
    competitionId: { type: [Number, String], required: true },
    exportUrl: { type: String, default: "" },
  },
  emits: ["update:open", "imported"],
  data() {
    return {
      files: [],
      errors: [],
      updated: 0,
      imported: false,
      importing: false,
    };
  },
  methods: {
    reset() {
      this.files = [];
      this.errors = [];
      this.updated = 0;
      this.imported = false;
    },
    handleImport() {
      if (this.files.length === 0) return;

      const formData = new FormData();
      formData.append("file", this.files[0].originFileObj);

      this.importing = true;

      window.axios
        .post(
          route("manage.competition.athletes.weights.import", this.competitionId),
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        )
        .then((response) => {
          this.updated = response.data.updated ?? 0;
          this.errors = response.data.errors ?? [];
          this.imported = true;

          if (this.updated > 0) {
            this.$message.success(
              this.$t("weights.import_modal.success", { count: this.updated })
            );
          }

          this.$emit("imported", { updated: this.updated, errors: this.errors });
        })
        .catch((error) => {
          this.$message.error(
            error?.response?.data?.message ?? this.$t("weights.import_modal.failed")
          );
        })
        .finally(() => {
          this.importing = false;
        });
    },
  },
};
</script>
