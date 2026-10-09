<template>
  <ProgramLayout :competition="competition">
    <a-page-header
      :title="$t('match_settings')"
      :sub-title="$t('competition_settings_description')"
    >
    </a-page-header>

    <div class="container-fluid">
      <a-card>
        <a-tabs :tab-position="tabPosition" animated size="small">
          <a-tab-pane key="1" :tab="$t('competition_information')">
            <Info :competition="competition" :logo-url="logoUrl" />
          </a-tab-pane>
          <a-tab-pane key="2" :tab="$t('draw')">
            <Draw :competition="competition" :draw="draw" />
          </a-tab-pane>
          <a-tab-pane key="6" :tab="$t('certificate')">
            <Certificate
              :competition="competition"
              :certificate-url="certificateUrl"
            />
          </a-tab-pane>
          <a-tab-pane key="7" :tab="$t('id_card')">
            <IdCard :competition="competition" :id-card="idCard" />
          </a-tab-pane>
          <a-tab-pane key="3" :tab="$t('integration_settings')">
            <Integration :competition="competition" />
          </a-tab-pane>
          <a-tab-pane key="4" :tab="$t('language_settings')">
            <Language :competition="competition" :languages="languages" />
          </a-tab-pane>
          <a-tab-pane key="5" :tab="$t('danger_area')">
            <a-form layout="vertical" class="max-w-3xl">
              <div class="flex flex-col gap-4">
                <a-alert
                  v-if="isCancelled"
                  show-icon
                  type="warning"
                  class="!shadow-none"
                  :message="$t('competitions.cancelled')"
                  :description="$t('competition_cancelled_alert')"
                />
                <a-form-item :label="$t('delete_competition')" class="form-group">
                  <a-alert
                    show-icon
                    type="error"
                    class="!shadow-none"
                    :message="$t('delete_competition')"
                    :description="$t('competitions.delete_warning')"
                  >
                  </a-alert>
                  <template #help>{{
                    $t("competitions.delete_warning_help")
                  }}</template>
                  <a-button danger @click="remove" class="my-3">{{
                    $t("delete_competition")
                  }}</a-button>
                </a-form-item>

                <a-form-item :label="$t('cancel_competition')" class="form-group">
                  <a-button danger :disabled="isCancelled" @click="cancel">{{
                    $t("cancel_competition")
                  }}</a-button>
                  <template #help>{{
                    $t("competitions.cancel_help")
                  }}</template>
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
import Info from "./Info.vue";
import Draw from "./Draw.vue";
import Integration from "./Integration.vue";
import Language from "./Language.vue";
import Certificate from "./Certificate.vue";
import IdCard from "./IdCard.vue";
import { Modal, notification } from "ant-design-vue";
import { createVNode } from "vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";

export default {
  name: "Index",
  components: {
    ProgramLayout,
    Info,
    Draw,
    Integration,
    Language,
    Certificate,
    IdCard,
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
    languages: {
      type: Array,
      default: () => [],
    },
    logoUrl: {
      type: String,
      default: "",
    },
    certificateUrl: {
      type: String,
      default: "",
    },
    idCard: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      // 預設左側分頁；視窗太窄時改成上方（見 syncTabPosition）
      tabPosition: "left",
    };
  },
  computed: {
    isCancelled() {
      return Boolean(this.competition.is_cancelled);
    },
  },
  mounted() {
    this.syncTabPosition();
    window.addEventListener("resize", this.syncTabPosition);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.syncTabPosition);
  },
  methods: {
    /*
     * 左側分頁列至少要 120px，若視窗不夠寬，它會把內容區擠成一條細縫，
     * 導致警示框、說明文字被壓成細長一條而顯示不全。
     * 因此視窗較窄時改用上方分頁，讓內容拿到完整的寬度。
     */
    syncTabPosition() {
      this.tabPosition = window.innerWidth < 1024 ? "top" : "left";
    },
    remove() {
      Modal.confirm({
        title: this.$t("competitions.confirm_delete"),
        icon: createVNode(ExclamationCircleOutlined),
        okText: this.$t("delete_competition"),
        okType: "danger",
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.delete(
            route("manage.competitions.destroy", this.competition.id),
            {
              preserveScroll: true,
              onSuccess: () => {
                notification.success({
                  message: this.$t("competitions.delete_success"),
                });
              },
              onError: () => {
                notification.error({
                  message: this.$t("competitions.delete_failed"),
                });
              },
            }
          );
        },
      });
    },
    cancel() {
      Modal.confirm({
        title: this.$t("competitions.confirm_cancel"),
        icon: createVNode(ExclamationCircleOutlined),
        okText: this.$t("cancel_competition"),
        okType: "danger",
        cancelText: this.$t("action.cancel"),
        onOk: () => {
          this.$inertia.post(
            route("manage.competitions.cancel", this.competition.id),
            {},
            {
              preserveScroll: true,
              onSuccess: () => {
                notification.success({
                  message: this.$t("competitions.cancel_success"),
                });
              },
              onError: () => {
                notification.error({
                  message: this.$t("competitions.cancel_failed"),
                });
              },
            }
          );
        },
      });
    },
  },
};
</script>

<style scoped lang="less">
/*
 * 左側分頁列原本固定 288px（w-72）。容器稍窄時，分頁列就會吃掉
 * 幾乎所有寬度，讓內容區被擠成一條細縫。改成隨容器縮放（1/4 寬，
 * 但有上下限），並只套用在左側模式（上方分頁不需要固定寬度）。
 */
:deep(.ant-tabs-left > .ant-tabs-nav) {
  @apply w-1/4 min-w-[120px] max-w-[288px] shrink-0;
}

/* 內容區要能被壓縮，否則裡面的長文字會撐破版面 */
:deep(.ant-tabs-content-holder) {
  @apply min-w-0;
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

    /*
     * 有 help（說明文字）時，antd 會在 form-item 裡多塞一個
     * .ant-form-margin-offset（inline style: margin-bottom: -24px），
     * 原本是用來抵消 form-item 自己的 24px 下外距。但這裡的 .form-group
     * 有 border + padding，這個負 margin 會把內容往下拉，讓最後的說明文字
     * 超出下邊框約 8px（= 24 - padding 16）。隱藏它，邊框就能完整包住內容。
     */
    & > .ant-form-margin-offset {
      display: none;
    }
  }
}
</style>
