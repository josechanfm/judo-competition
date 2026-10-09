<template>
  <inertia-head :title="$t('athletes.title')" />

  <ProgramLayout :competition="competition">
    <a-page-header></a-page-header>
    <div class="py-12 mx-8">
      <div class="overflow-hidden flex flex-col gap-3">
        <div class="flex justify-end items-center gap-3">
          <a-button
            v-if="competition.status === 0"
            type="primary"
            class="bg-blue-500"
            @click="confirmLockAthletes"
            >{{ $t("athletes.lock_list") }}</a-button
          >
          <span v-else class="text-blue-500">{{ $t("athletes.list_locked") }}</span>
          <a-button
            v-if="competition.status === 1"
            type="primary"
            class="bg-blue-500"
            @click="unLockAthletes"
            >{{ $t("athletes.unlock") }}</a-button
          >
        </div>
        <div class="grid grid-cols-4 gap-12 py-4">
          <a-card class="shadow-lg">
            <a-statistic
              :title="$t('status')"
              :value="$t('athletes.ready_to_start')"
            />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic
              :title="$t('date')"
              :value="competition.date_start + ' ~ ' + competition.date_end"
            />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic :title="$t('athletes.programs')" :value="programs.length" />
          </a-card>
          <a-card class="shadow-lg">
            <a-statistic
              :title="$t('athletes')"
              :value="competition.athletes.length"
            />
          </a-card>
        </div>
        <div class="p-4 mb-2 shadow-lg bg-white rounded-lg">
          <!-- 工具列 -->
          <div
            class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100"
          >
            <div class="text-xl font-bold">{{ $t("athletes.title") }}</div>
            <div class="flex flex-wrap items-center justify-end gap-2 md:gap-3">
              <a-button
                type="primary"
                v-if="competition.status === 0"
                class="bg-blue-500"
                @click="onCreateRecord"
                >{{ $t("athletes.add") }}
              </a-button>
              <a-button
                type="primary"
                v-if="competition.status === 0"
                class="bg-blue-500"
                @click="visible = true"
                >{{ $t("athletes.import") }}</a-button
              >
              <a-button
                type="primary"
                @click="sendAthleteCards"
                :loading="isLoading"
              >
                {{ isLoading ? $t("athletes.send_cards.sending") : $t("athletes.send_cards") }}
              </a-button>
            </div>
          </div>

          <!-- 下載文件（獨立一列，與篩選區分開） -->
          <div class="flex flex-wrap items-center gap-2 py-4">
            <span class="mr-1 text-sm text-gray-500">{{ $t("download") }}</span>
            <a :href="route('athletes.generateIdCards', competition.id)" target="_blank">
              <a-button type="default">
                <template #icon><DownloadOutlined /></template>
                {{ $t("athletes.download_id_cards") }}
              </a-button>
            </a>
            <a
              :href="route('manage.competition.teams-athletes-statistics-table', competition.id)"
              target="_blank"
            >
              <a-button type="default">
                <template #icon><DownloadOutlined /></template>
                {{ $t("athletes.download_statistics") }}
              </a-button>
            </a>
            <a
              :href="route('manage.competition.teams-athletes-table', competition.id)"
              target="_blank"
            >
              <a-button type="default">
                <template #icon><DownloadOutlined /></template>
                {{ $t("athletes.download_all_teams") }}
              </a-button>
            </a>
          </div>

          <a-alert
            v-if="showResult"
            class="mb-4"
            :type="resultType"
            :message="resultMessage"
            show-icon
            closable
            @close="showResult = false"
          />

          <!-- 篩選功能 -->
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="text-lg font-semibold mb-3">
              {{ $t("athletes.filter.title") }}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{
                  $t("athletes.team")
                }}</label>
                <a-select
                  v-model:value="filters.team"
                  :placeholder="$t('athletes.select_team')"
                  allowClear
                  style="width: 100%"
                  :options="teamOptions"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{
                  $t("gender")
                }}</label>
                <a-select
                  v-model:value="filters.gender"
                  :placeholder="$t('athletes.select_gender')"
                  allowClear
                  style="width: 100%"
                  :options="genderOptions"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{
                  $t("athletes.program")
                }}</label>
                <a-select
                  v-model:value="filters.program"
                  :placeholder="$t('athletes.select_program')"
                  allowClear
                  style="width: 100%"
                  :options="programOptions"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{
                  $t("name")
                }}</label>
                <a-input
                  v-model:value="filters.name"
                  :placeholder="$t('athletes.search_name')"
                  allow-clear
                />
              </div>
            </div>
            <div class="flex justify-end mt-3 gap-2">
              <a-button @click="resetFilters">{{
                $t("athletes.reset_filters")
              }}</a-button>
              <a-button
                type="primary"
                class="bg-blue-500"
                @click="applyFilters"
                >{{ $t("athletes.apply_filters") }}</a-button
              >
            </div>
          </div>

          <a-table :dataSource="filteredAthletes" :columns="columns">
            <template #bodyCell="{ column, record }">
              <template v-if="column.dataIndex === 'operation'">
                <a-button @click="onEditRecord(record)">{{
                  $t("action.edit")
                }}</a-button>
              </template>
              <template v-else-if="column.dataIndex === 'team'">
                {{ record?.team?.name }}
              </template>
              <template v-else-if="column.dataIndex === 'program'">
                {{
                  record.programs
                    .map((program) => weightGroupLabel(program.weight_code))
                    .join(", ")
                }}
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
            <div class="w-1/3">
              <a-form-item :label="$t('name')" name="name">
                <a-input type="input" v-model:value="modal.data.name" />
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item :label="$t('athletes.name_secondary')" name="name_secondary">
                <a-input type="input" v-model:value="modal.data.name_secondary" />
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item :label="$t('display_name')" name="name_display">
                <a-input type="input" v-model:value="modal.data.name_display" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-1/3">
              <a-form-item :label="$t('gender')" name="gender">
                <a-select
                  @change="changeGender"
                  v-model:value="modal.data.gender"
                  :options="genderOptions"
                />
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item :label="$t('athletes.programs')" name="programs">
                <a-select
                  v-model:value="modal.data.programs"
                  mode="multiple"
                  :disabled="modal.data.gender == null"
                  :options="
                    filter_programs.map((item) => ({
                      label: weightGroupLabel(item.weight_code),
                      value: item.id,
                    }))
                  "
                ></a-select>
              </a-form-item>
            </div>
            <div class="w-1/3">
              <a-form-item :label="$t('athletes.team')" name="team_id">
                <div class="flex gap-3" v-if="modal.data.new_team == false">
                  <a-select
                    v-model:value="modal.data.team_id"
                    :options="teams.map((item) => ({ value: item.id, label: item.name }))"
                  />
                  <a-button @click="modal.data.new_team = true">{{
                    $t("athletes.new_team")
                  }}</a-button>
                </div>
                <div class="flex gap-3" v-else>
                  <a-input type="input" v-model:value="modal.data.team" />
                  <a-button @click="modal.data.new_team = false">{{
                    $t("athletes.old_team")
                  }}</a-button>
                </div>
              </a-form-item>
            </div>
          </div>
          <div class="text-right">
            <a-form-item>
              <a-button
                v-if="modal.mode == 'CREATE'"
                class="bg-blue-500"
                type="primary"
                :loading="saving"
                @click="onCreate"
                >{{ $t("action.create") }}</a-button
              >
              <a-button
                v-if="modal.mode == 'EDIT'"
                class="bg-blue-500"
                type="primary"
                :loading="saving"
                @click="onUpdate"
                >{{ $t("athletes.update") }}</a-button
              >
              <a-button style="margin-left: 10px" @click="this.modal.isOpen = false"
                >{{ $t("athletes.close") }}</a-button
              >
            </a-form-item>
          </div>
        </div>
      </a-form>
    </a-modal>
    <a-modal :title="$t('athletes.import_title')" v-model:open="visible">
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
        <p class="ant-upload-text">{{ $t("athletes.import_drag_text") }}</p>
        <p class="ant-upload-hint">
          {{ $t("athletes.import_hint") }}
        </p>
      </a-upload-dragger>

      <div class="mt-3" v-if="imported">
        <div class="font-bold my-3 text-green-500" v-if="errors.length === 0">
          {{ $t("athletes.import.success") }}!
        </div>
        <div class="font-bold my-3 text-yellow-500" v-else>
          {{ $t("athletes.import.partial") }}
        </div>
        <p v-for="(error, index) in errors" :key="index" class="font-mono m-0">
          <warning-outlined class="!text-yellow-500" />
          {{ $t("athletes.import.row_error", { row: error.row }) }},
          {{ error.errors[0] }}
        </p>
      </div>
      <template #footer>
        <div class="flex w-full justify-between">
          <a href="/templates/athlete_list.xlsx">
            <a-button type="link">
              <template #icon>
                <DownloadOutlined />
              </template>
              {{ $t("athletes.download_template") }}
            </a-button>
          </a>
          <a-button
            type="primary"
            class="bg-blue-500"
            @click="handleImport"
            :disabled="files.length === 0"
            >{{ $t("athletes.import_confirm") }}</a-button
          >
        </div>
      </template>
    </a-modal>
  </ProgramLayout>
</template>

<script>
import ProgramLayout from "@/Layouts/ProgramLayout.vue";
import { message } from "ant-design-vue";
import { Modal } from "ant-design-vue";
import { createVNode } from "vue";
import { weightGroupLabel as weightGroupLabelUtil } from "@/Utils/weightParser";
import {
  DownloadOutlined,
  FileExcelOutlined,
  WarningOutlined,
  ExclamationCircleOutlined,
} from "@ant-design/icons-vue";

export default {
  components: {
    ExclamationCircleOutlined,
    DownloadOutlined,
    FileExcelOutlined,
    WarningOutlined,
    ProgramLayout,
  },
  props: ["competition", "programs", "teams"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      filter_programs: [],
      modal: {
        isOpen: false,
        mode: null,
        title: "",
        data: {},
      },
      isLoading:false,
      showResult:false,
      resultMessage:'',
      resultType:'success',
      saving: false,
      // 添加筛选状态
      filters: {
        team: null,
        gender: null,
        program: null,
        name: '',
      },
      rules: {
        country: { required: true },
        name: { required: true },
        date_start: { required: true },
        date_end: { required: true },
      },
      files: [],
      visible: false,
      errors: [],
      imported: false,
    };
  },
  computed: {
    columns() {
      return [
        { title: this.$t("athletes.team"), dataIndex: "team" },
        { title: this.$t("name"), dataIndex: "name_display" },
        { title: this.$t("gender"), dataIndex: "gender" },
        { title: this.$t("athletes.program"), dataIndex: "program" },
        { title: this.$t("action"), dataIndex: "operation" },
      ];
    },
    genderOptions() {
      return [
        { value: "M", label: this.$t("gender.male") },
        { value: "F", label: this.$t("gender.female") },
      ];
    },
    // Ant Design 的訊息樣板使用 ${label} 佔位符，故以字串串接避免 vue-i18n 誤判為插值
    validateMessages() {
      const label = "${label}";
      return {
        required: `${label} ${this.$t("validation.is_required")}`,
        types: {
          email: `${label} ${this.$t("validation.email")}`,
          number: `${label} ${this.$t("validation.number")}`,
        },
        number: {
          range: `${label} ${this.$t("validation.range", {
            min: "${min}",
            max: "${max}",
          })}`,
        },
      };
    },
    // 计算筛选后的运动员列表
    filteredAthletes() {
      let athletes = this.competition.athletes;

      // 按队伍筛选
      if (this.filters.team) {
        athletes = athletes.filter(athlete => 
          athlete.team && athlete.team.id === this.filters.team
        );
      }

      // 按性别筛选
      if (this.filters.gender) {
        athletes = athletes.filter(athlete => 
          athlete.gender === this.filters.gender
        );
      }

      // 按项目筛选
      if (this.filters.program) {
        athletes = athletes.filter(athlete =>
          athlete.programs.some(program => program.id === this.filters.program)
        );
      }

      // 按姓名筛选
      if (this.filters.name) {
        const searchTerm = this.filters.name.toLowerCase();
        athletes = athletes.filter(athlete =>
          athlete.name_display?.toLowerCase().includes(searchTerm) ||
          athlete.name?.toLowerCase().includes(searchTerm)
        );
      }

      return athletes;
    },
    // 队伍选项
    teamOptions() {
      return this.teams.map(team => ({
        value: team.id,
        label: team.name
      }));
    },
    // 项目选项
    programOptions() {
      return this.programs.map(program => ({
        value: program.id,
        label: this.weightGroupLabel(program.weight_code)
      }));
    }
  },
  created() {
    this.filter_programs = this.programs;
  },
  methods: {
    // 公斤級代碼 (MW60- / FW42+ / MWULW) → 目前語系的顯示名稱
    weightGroupLabel(weightCode) {
      return weightGroupLabelUtil(weightCode, this.$t);
    },
    // 应用筛选
    applyFilters() {
      // 计算属性会自动更新，这里可以添加其他逻辑
      message.success(this.$t("athletes.filters_applied"));
    },
    // 重置筛选
    resetFilters() {
      this.filters = {
        team: null,
        gender: null,
        program: null,
        name: '',
      };
      message.info(this.$t("athletes.filters_reset"));
    },
    // 其他方法保持不变
    onCreateRecord() {
      this.modal.title = this.$t("action.create");
      this.modal.data = {
        new_team: false,
      };
      this.modal.mode = "CREATE";
      this.modal.isOpen = true;
    },
    onEditRecord(record) {
      this.modal.title = this.$t("action.edit");
      this.modal.mode = "EDIT";
      this.modal.data = { ...record, new_team: false };
      this.modal.data.programs = this.modal.data.programs.map((x) => x.id);
      this.filter_programs = this.programs.filter((x) =>
        x.weight_code.includes(record.gender)
      );
      console.log(record.gender);
      console.log(this.programs);
      this.modal.isOpen = true;
    },
    onUpdate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.saving = true;
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
              onFinish: () => {
                this.saving = false;
              },
            }
          );

          console.log("values", this.modal.data, this.modal.data);
        })
        .catch((error) => {
          console.log("error", error);
        });
    },
    changeGender(value) {
      console.log(value);
      this.filter_programs = this.programs.filter((x) => x.weight_code.includes(value));

      // 換性別後，已選但不符合新性別的項目要清掉，避免存到不該參加的組別
      const allowedIds = this.filter_programs.map((x) => x.id);
      this.modal.data.programs = (this.modal.data.programs ?? []).filter((id) =>
        allowedIds.includes(id)
      );
    },
    onCreate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.saving = true;
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
              onFinish: () => {
                this.saving = false;
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
          this.files = [];
          this.errors = data.errors || [];
          this.imported = true;

          if (this.errors.length === 0) {
            this.$message.success(this.$t("athletes.import.success"));
          } else {
            this.$message.warning(this.$t("athletes.import.partial"));
          }

          this.$inertia.reload();
          this.$emit("imported");
        })
        .catch(() => {
          this.$message.error(this.$t("athletes.import.failed"));
        });
    },
    confirmLockAthletes() {
      Modal.confirm({
        title: this.$t("athletes.confirm_lock"),
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => {
          this.lockAthletes();
        },
        onCancel() {
          console.log("Cancel");
        },
        class: "test",
      });
    },
    unLockAthletes() {
      Modal.confirm({
        title: this.$t("athletes.confirm_unlock"),
        icon: createVNode(ExclamationCircleOutlined),
        style: "top:20vh",
        onOk: () => {
          this.$inertia.post(
            route("manage.competition.athletes.unlock", this.competition.id),
            "",
            {
              onSuccess: (page) => {
                message.success(this.$t("athletes.unlock_success"));
              },
              onError: (page) => {
                message.error(this.$t("athletes.unlock_failed"));
              },
            }
          );
        },
        onCancel() {
          console.log("Cancel");
        },
        class: "test",
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
    async sendAthleteCards() {
      // 確認對話框
      if (!confirm(this.$t("athletes.send_cards.confirm"))) {
        return;
      }
      
      this.isLoading = true;
      this.showResult = false;
      
      try {
        const response = await axios.post(`/manage/competition/${this.competition.id}/send/athletes_card`);
        
        // 處理成功回應
        this.resultMessage = response.data.message || this.$t('athletes.send_cards.success');
        this.resultType = 'success';
        this.showResult = true;

        setTimeout(() => {
          this.showResult = false;
        }, 3000);
        
      } catch (error) {
        // 處理錯誤
        console.error('發送失敗:', error);
        
        if (error.response && error.response.data) {
          this.resultMessage = error.response.data.message || this.$t('athletes.send_cards.failed');
        } else {
          this.resultMessage = this.$t('athletes.send_cards.network_error');
        }

        this.resultType = 'error';
        this.showResult = true;
        
      } finally {
        this.isLoading = false;
      }
    }
  },
};
</script>
