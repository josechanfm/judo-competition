<template>
  <a-form layout="vertical" class="max-w-3xl">
    <a-form-item :label="$t('certificate')" class="form-group">
      <a-form-item
        :label="$t('certificate_background')"
        :help="$t('certificate_background_help')"
      >
        <div class="w-full aspect-video mb-3">
          <img
            class="h-full object-contain"
            :src="certificateUrl"
            :alt="$t('certificate_background')"
          />
        </div>
        <a-upload
          v-model:file-list="newCertificate"
          :multiple="false"
          name="file"
          :action="route('manage.competition.setting.update-certificate', [competition.id])"
          :headers="headers"
          @change="onUploadChange"
        >
          <a-button> {{ $t('certificate_change') }} </a-button>
        </a-upload>
      </a-form-item>
    </a-form-item>
  </a-form>
</template>

<script>
import Cookie from "js-cookie";

export default {
  name: "Certificate",
  props: {
    competition: {
      type: Object,
      required: true,
    },
    certificateUrl: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      newCertificate: [],
      headers: {
        "X-XSRF-TOKEN": Cookie.get("XSRF-TOKEN"),
      },
    };
  },
  methods: {
    // 上傳完成後重載，讓新圖立刻顯示
    onUploadChange(info) {
      if (info?.file?.status === "done") {
        this.newCertificate = [];
        this.$inertia.reload({ only: ["certificateUrl"] });
      } else if (info?.file?.status === "error") {
        this.$message.error(this.$t("upload_failed"));
      }
    },
  },
};
</script>

<style scoped></style>
