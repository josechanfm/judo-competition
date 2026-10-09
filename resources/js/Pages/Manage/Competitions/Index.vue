<template>
  <inertia-head :title="$t('competitions.manage')" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ $t("competitions.manage") }}
        </h2>
      </div>
    </template>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between flex-col md:flex-row">
        <div class="text-xl font-bold">{{ $t("competitions.manage") }}</div>
        <div class="flex gap-2 mt-2 md:mt-0">
          <a-button class="bg-white" @click="importOpen = true">
            <template #icon><UploadOutlined /></template>
            {{ $t("competitions.import") }}
          </a-button>
          <inertia-link :href="route('manage.competitions.create')"
            ><a-button class="bg-white">{{ $t("competitions.create") }}</a-button>
          </inertia-link>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <a-table :dataSource="competitions" :columns="columns">
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'operation'">
              <a-button :href="route('manage.competitions.edit', record.id)">{{
                $t("action.edit")
              }}</a-button>
              <a-button :href="route('manage.competition.athletes.index', record.id)">{{
                $t("athletes")
              }}</a-button>
              <a-button :href="route('manage.competition.programs.index', record.id)">{{
                $t("action.manage")
              }}</a-button>
              <a-button :href="route('manage.competition.progress', record.id)">{{
                $t("action.progress")
              }}</a-button>
              <a-button
                :href="route('manage.competitions.export', record.id)"
                :title="$t('competitions.export_hint')"
              >
                <template #icon><DownloadOutlined /></template>
                {{ $t("competitions.export") }}
              </a-button>
            </template>
            <template v-else>
              {{ record[column.dataIndex] }}
            </template>
          </template>
        </a-table>
      </div>

      <ImportCompetitionModal v-model:open="importOpen" @imported="onImported" />
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ImportCompetitionModal from "../ImportCompetitionModal.vue";
import { isDateOutsideRange, isDateBefore } from "@/Utils/dateRange";
import { DownloadOutlined, UploadOutlined } from "@ant-design/icons-vue";
import moment from "moment";
export default {
  components: {
    AdminLayout,
    ImportCompetitionModal,
    DownloadOutlined,
    UploadOutlined,
  },
  props: ["countries", "gameTypes", "competitions", "languages"],
  data() {
    return {
      importOpen: false,
      dateFormat: "YYYY-MM-DD",
      disabledDate: null,
      tmpContestTime: null,
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      rules: {
        game_type_id: { required: true },
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
        days: { required: true },
        mat_number: { required: true },
        section_number: { required: true },
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
  computed: {
    // 表頭要跟著語系切換 → 放 computed（放 data() 會被凍結，見專案 i18n 慣例）
    columns() {
      return [
        { title: this.$t("competitions.name"), dataIndex: "name" },
        { title: this.$t("competitions.country_or_region"), dataIndex: "country" },
        { title: this.$t("start_date"), dataIndex: "date_start" },
        { title: this.$t("end_date"), dataIndex: "date_end" },
        { title: this.$t("mat_number"), dataIndex: "mat_number" },
        { title: this.$t("section_number"), dataIndex: "section_number" },
        { title: this.$t("competitions.token"), dataIndex: "token" },
        { title: this.$t("competitions.status"), dataIndex: "status" },
        { title: this.$t("action"), dataIndex: "operation" },
      ];
    },
    selectLanguage() {
      return this.languages.map((x) => {
        console.log(x);
        if (
          x.value == this.modal.data?.competition_type.language ||
          x.value == this.modal.data?.competition_type.language_secondary
        ) {
          return { ...x, disabled: true };
        } else {
          return x;
        }
      });
    },
  },
  created() {
    this.disabledDate = (current) =>
      isDateOutsideRange(
        current,
        this.modal.data.date_start,
        this.modal.data.date_end
      );
    this.endDateDisabled = (current) =>
      isDateBefore(current, this.modal.data.date_start);
  },
  methods: {
    onCreateRecord() {
      this.modal.title = "Create";
      this.modal.isOpen = true;
      this.modal.mode = "CREATE";
      this.modal.data = {
        days: [],
      };
      this.tmpContestTime = null;
    },
    onEditRecord(record) {
      this.modal.isOpen = true;
      this.modal.title = "Edit";
      this.modal.mode = "EDIT";
      this.modal.data = { ...record };

      console.log(moment().add(7, "days"));
      console.log(moment(this.modal.data.date_start));
    },
    addTimeToForm() {
      // should be unique
      if (this.modal.data.days.includes(this.tmpContestTime) || !this.tmpContestTime) {
        return;
      }

      this.modal.data.days.push(this.tmpContestTime);
    },
    removeTimeFromForm(time) {
      this.modal.data.days = this.modal.data.days.filter((t) => t !== time);
    },
    onUpdate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.put(
            route("manage.competitions.update", this.modal.data.id),
            this.modal.data,
            {
              onSuccess: (page) => {
                this.modal.data = {};
                this.modal.title = "";
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
    onImported() {
      this.importOpen = false;
      this.$inertia.reload();
    },
  },
};
</script>
