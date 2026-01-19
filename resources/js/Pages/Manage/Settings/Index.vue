<template>
  <ProgramLayout :competition="competition">
    <a-page-header
      :title="$t('match_settings')"
      :sub-title="$t('competition_settings_description')"
    >
    </a-page-header>

    <div class="container-fluid">
      <a-card>
        <a-tabs tab-position="left" animated size="small">
          <a-tab-pane key="1" :tab="$t('competition_information')">
            <!-- <info /> -->
          </a-tab-pane>
          <a-tab-pane key="2" :tab="$t('draw')">
            <Draw :draw="draw" />
          </a-tab-pane>
          <a-tab-pane key="6" :tab="$t('certificate')">
            <!-- <certificate :certificate="certificate" /> -->
          </a-tab-pane>
          <a-tab-pane key="3" :tab="$t('integration_settings')">
            <!-- <integration /> -->
          </a-tab-pane>
          <a-tab-pane key="4" :tab="$t('language_settings')">
            <!-- <language :languages="languages" /> -->
          </a-tab-pane>
          <a-tab-pane key="5" :tab="$t('danger_area')">
            <a-form layout="vertical" class="max-w-3xl">
              <div class="flex flex-col gap-4">
                <a-form-item :label="$t('delete_competition')" class="form-group">
                  <a-alert
                    show-icon
                    type="error"
                    class="!shadow-none"
                    message="刪除賽事"
                    description="刪除賽事將會刪除所有相關資料，包括所有參賽者、所有比賽結果、所有比賽資料，請確認是否要刪除賽事。"
                  >
                  </a-alert>
                  <template #help> 刪除賽事後，所有資料將無法復原。 </template>
                  <a-button danger @click="remove" class="my-3">{{
                    $t("delete_competition")
                  }}</a-button>
                </a-form-item>

                <a-form-item :label="$t('cancel_competition')" class="form-group">
                  <a-button danger @click="cancel">{{ $t("cancel_competition") }}</a-button>
                  <template #help> 您可以在賽事開始前取消賽事。 </template>
                </a-form-item>
              </div>
            </a-form>
          </a-tab-pane>
        </a-tabs>
      </a-card>
    </div>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
// import Info from "./Info.vue";
import Draw from "./Draw.vue";
// import Integration from "./Integration.vue";
// import Language from "./Language.vue";
// import Certificate from "./Certificate.vue";
import { notification } from "ant-design-vue";
// import CertificateVue from "./Certificate.vue";

export default {
  name: "Index",
  provide() {
    return {
      competition: this.competition,
    };
  },
  components: {
    ProgramLayout,
    // Info,
    Draw,
    // Integration,
  },
  props: {
    competition: {
      type: Object,
      required: true,
    },
    draw: {
      type: Object,
      required: true,
    },
  },
  methods: {
    remove() {
      confirmPassword("remove", true).then(() => {
        this.$inertia.delete(route("admin.competition.destroy", this.competition.id), {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            notification.success({
              message: "刪除成功",
              description: "賽事已刪除",
            });
          },
        });
      });
    },
    cancel() {
      confirmPassword("cancel", true).then(() => {
        this.$inertia.post(route("admin.competition.cancel", this.competition.id), {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            notification.success({
              message: "取消成功",
              description: "賽事已取消",
            });
          },
        });
      });
    },
  },
};
</script>

<style scoped lang="less">
:deep(.ant-tabs-nav) {
  @apply w-72;
}

:deep(.ant-card-body) {
  @apply pl-0;
}

:deep(.ant-form) {
  .form-group {
    @apply rounded;
    @apply border;
    @apply p-4;

    & > .ant-form-item-label > label {
      @apply font-bold;
      @apply text-base;
    }
  }
}
</style>
