<template>
  <a-modal
    :title="$t('competitions.import_modal.title')"
    :open="open"
    width="640px"
    @update:open="(value) => $emit('update:open', value)"
  >
    <a-upload-dragger
      v-model:file-list="files"
      name="file"
      accept=".zip,application/zip"
      :before-upload="() => false"
      :multiple="false"
      :max-count="1"
      @remove="reset"
    >
      <p class="ant-upload-drag-icon">
        <InboxOutlined />
      </p>
      <p class="ant-upload-text">
        {{ $t("competitions.import_modal.upload_text") }}
      </p>
      <p class="ant-upload-hint">
        {{ $t("competitions.import_modal.upload_hint") }}
      </p>
    </a-upload-dragger>

    <template #footer>
      <div class="flex justify-end gap-2">
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
          {{ $t("competitions.import_modal.button") }}
        </a-button>
      </div>
    </template>
  </a-modal>
</template>

<script>
import { InboxOutlined } from "@ant-design/icons-vue";

export default {
  name: "ImportCompetitionModal",
  components: {
    InboxOutlined,
  },
  props: {
    open: { type: Boolean, default: false },
  },
  emits: ["update:open", "imported"],
  data() {
    return {
      files: [],
      importing: false,
    };
  },
  methods: {
    reset() {
      this.files = [];
    },
    handleImport() {
      if (this.files.length === 0) return;

      const formData = new FormData();
      formData.append("file", this.files[0].originFileObj);

      this.importing = true;

      window.axios
        .post(route("manage.competitions.import"), formData, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then((response) => {
          const competition = response.data?.competition ?? {};

          this.$message.success(
            this.$t("competitions.import_modal.success", {
              name: competition.name ?? "",
            })
          );

          this.reset();
          this.$emit("update:open", false);
          this.$emit("imported", competition);
        })
        .catch((error) => {
          this.$message.error(
            error?.response?.data?.message ??
              this.$t("competitions.import_modal.failed")
          );
        })
        .finally(() => {
          this.importing = false;
        });
    },
  },
};
</script>
