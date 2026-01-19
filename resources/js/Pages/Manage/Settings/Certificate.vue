<template>
  <a-form layout="vertical">
    <a-form-item label="證書" class="form-group">
      <div class="">
        <a-form-item label="證書背景" help="證書背景">
          <div class="w-full aspect-video mb-3">
            <img class="h-full" :src="certificate" />
          </div>
          <a-upload
            v-model:file-list="newCertificate"
            :multiple="false"
            name="file"
            :action="route('admin.contests.settings.update-certificate', [contest])"
            :headers="headers"
          >
            <a-button> 更換證書 </a-button>
          </a-upload>
        </a-form-item>
      </div>
    </a-form-item>
  </a-form>
</template>

<script>
import useXSRFHeader from "::admin/Utils/useXSRFHeader";

export default {
  name: "Certificate",
  inject: ["contest"],
  props: {
    certificate: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      newCertificate: null,
    };
  },
  setup() {
    const headers = useXSRFHeader();

    return { headers };
  },
};
</script>

<style scoped></style>
