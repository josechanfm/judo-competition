<template>
  <inertia-head title="賽事管理" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">賽事管理</h2>
      </div>
    </template>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">賽事管理</div>
        <div>
          <inertia-link :href="route('manage.competitions.create')"
            ><a-button type="primary">創建新的賽事</a-button>
          </inertia-link>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <a-table :dataSource="competitions" :columns="columns">
          <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'operation'">
              <a-button @click="onEditRecord(record)">Edit</a-button>
              <a-button :href="route('manage.competition.athletes.index', record.id)"
                >運動員</a-button
              >
              <a-button :href="route('manage.competition.programs.index', record.id)"
                >Manage</a-button
              >
              <a-button :href="route('manage.competition.progress', record.id)"
                >Progress</a-button
              >
            </template>
            <template v-else>
              {{ record[column.dataIndex] }}
            </template>
          </template>
        </a-table>
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
            <div class="w-full" v-if="modal.title == 'Create'">
              <a-form-item label="Type" name="game_type_id">
                <a-select
                  v-model:value="modal.data.game_type_id"
                  show-search
                  :options="gameTypes"
                  :fieldNames="{ value: 'id', label: 'name' }"
                />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="Country" name="country">
                <a-select
                  v-model:value="modal.data.country"
                  show-search
                  :options="countries"
                  :fieldNames="{ value: 'name', label: 'name' }"
                />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="scale" name="scale">
                <a-input v-model:value="modal.data.scale" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-full">
              <a-form-item label="Name" name="name">
                <a-input v-model:value="modal.data.name" />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="Name (Foreign)" name="name_secondary">
                <a-input v-model:value="modal.data.name_secondary" />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-full">
              <a-form-item label="Start Date" name="date_start">
                <a-date-picker
                  v-model:value="modal.data.date_start"
                  :format="dateFormat"
                  :valueFormat="dateFormat"
                />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="End Date" name="date_end">
                <a-date-picker
                  v-model:value="modal.data.date_end"
                  :format="dateFormat"
                  :disabled-date="endDateDisabled"
                  :valueFormat="dateFormat"
                />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="Mat Number" name="mat_number">
                <a-input-number
                  v-model:value="modal.data.mat_number"
                  style="width: 150px"
                  :min="1"
                />
              </a-form-item>
            </div>
            <div class="w-full">
              <a-form-item label="Section Number" name="section_number">
                <a-input-number
                  v-model:value="modal.data.section_number"
                  style="width: 150px"
                  :min="1"
                />
              </a-form-item>
            </div>
          </div>
          <div class="flex justify-between gap-3">
            <div class="w-full">
              <a-form-item label="Days" name="days">
                <div class="flex gap-3 mb-2">
                  <a-date-picker
                    v-model:value="tmpContestTime"
                    :disabled-date="disabledDate"
                    value-format="YYYY-MM-DD"
                  />
                  <a-button @click="addTimeToForm">新增</a-button>
                </div>
                <div
                  class="rounded shadow border p-2 w-72 flex items-center mb-2"
                  v-for="time in modal.data.days"
                  :key="time"
                >
                  <div class="flex-1 font-bold">{{ time }}</div>
                  <div>
                    <a-button type="link" danger @click="removeTimeFromForm(time)"
                      >移除</a-button
                    >
                  </div>
                </div>
              </a-form-item>
            </div>
            <div class="w-full flex flex-col" v-if="modal.title == 'Edit'">
              <div class="">
                <a-form-item
                  label="Competition Name"
                  :name="['competition_type', 'name_secondary']"
                  :rules="[
                    {
                      required: true,
                      message: 'Competition Name is required!',
                    },
                  ]"
                >
                  <a-input v-model:value="modal.data.competition_type.name"></a-input>
                </a-form-item>
              </div>
              <div
                class=""
                v-if="modal.data?.competition_type?.is_language_secondary_enabled == 1"
              >
                <a-form-item
                  label="Competition Name (Foreign)"
                  :name="['competition_type', 'name_secondary']"
                  :rules="[
                    {
                      required: true,
                      message: 'Competition Name (Foreign) is required!',
                    },
                  ]"
                >
                  <a-input
                    v-model:value="modal.data.competition_type.name_secondary"
                  ></a-input>
                </a-form-item>
              </div>
            </div>
            <div class="w-full flex flex-col" v-if="modal.title == 'Edit'">
              <div class="">
                <a-form-item
                  label="Competition Language"
                  :name="['competition_type', 'language']"
                  :rules="[
                    { required: true, message: 'Competition Language is required!' },
                  ]"
                >
                  <a-select
                    v-model:value="modal.data.competition_type.language"
                    :options="selectLanguage"
                    :fieldNames="{ value: 'value', label: 'name' }"
                  ></a-select>
                </a-form-item>
              </div>
              <div
                class=""
                v-if="modal.data?.competition_type?.is_language_secondary_enabled == 1"
              >
                <a-form-item
                  label="Competition Language (Foreign)"
                  :name="['competition_type', 'language_secondary']"
                  :rules="[
                    {
                      required: true,
                      message: 'Competition Language (Foreign) is required!',
                    },
                  ]"
                >
                  <a-select
                    v-model:value="modal.data.competition_type.language_secondary"
                    :options="selectLanguage"
                    :fieldNames="{ value: 'value', label: 'name' }"
                  ></a-select>
                </a-form-item>
              </div>
            </div>
            <div class="w-1/2" v-if="modal.title == 'Edit'">
              <a-form-item
                label="開啓第二語言"
                name="competition_type_is_language_secondary_enabled"
              >
                <a-switch
                  v-model:checked="
                    modal.data.competition_type.is_language_secondary_enabled
                  "
                  :unCheckedValue="0"
                  :checkedValue="1"
                />
              </a-form-item>
            </div>
          </div>
          <a-form-item label="Remark" name="remark">
            <a-textarea v-model:value="modal.data.remark" :rows="5" />
          </a-form-item>
          <a-form-item>
            <a-button v-if="modal.mode == 'CREATE'" type="primary" @click="onCreate"
              >Create</a-button
            >
            <a-button v-if="modal.mode == 'EDIT'" type="primary" @click="onUpdate"
              >Update</a-button
            >
            <a-button style="margin-left: 10px" @click="this.modal.isOpen = false"
              >Close</a-button
            >
          </a-form-item>
        </div>
      </a-form>
    </a-modal>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Dayjs } from "dayjs";
import moment from "moment";
export default {
  components: {
    AdminLayout,
  },
  props: ["countries", "gameTypes", "competitions", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      disabledDate: null,
      tmpContestTime: null,
      modal: {
        isOpen: false,
        mode: null,
        title: "Record Modal",
        data: {},
      },
      columns: [
        {
          title: "Name",
          dataIndex: "name",
        },
        {
          title: "Country",
          dataIndex: "country",
        },
        {
          title: "Date Start",
          dataIndex: "date_start",
        },
        {
          title: "Date End",
          dataIndex: "date_end",
        },
        {
          title: "Mat Number",
          dataIndex: "mat_number",
        },
        {
          title: "Section Number",
          dataIndex: "section_number",
        },
        {
          title: "Status",
          dataIndex: "status",
        },
        {
          title: "Operation",
          dataIndex: "operation",
        },
      ],
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
    this.disabledDate = (current) => {
      if (!this.modal.data.date_start && !this.modal.data.date_end) {
        return false;
      }
      return (
        current < moment(this.modal.data.date_start).valueOf() ||
        current > moment(this.modal.data.date_end).valueOf()
      );
    };
    this.endDateDisabled = (current) => {
      if (!this.modal.data.date_start) {
        return false;
      }
      return current < moment(this.modal.date_start).valueOf();
    };
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
  },
};
</script>
