<template>
  <inertia-head title="Dashboard" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
      </div>
    </template>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">賽事創建</div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex">
        <div class="border-r-2 border-gray-300 shrink-0 py-2 px-2">
          <div class="flex flex-col font-bold text-lg gap-2">
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 0 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(0)"
                >基礎設定</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 1 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(1)"
                >組別設定</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 2 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(2)"
                >賽事類型</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 3 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(3)"
                >語言設定</a-button
              >
            </div>
          </div>
        </div>
        <div class="w-full p-4">
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
              <div class="flex flex-col" v-if="setting_index == 0">
                <div class="">
                  <a-form-item label="賽事類型" name="game_type_id">
                    <a-select
                      type="select"
                      v-model:value="modal.data.game_type_id"
                      show-search
                      :options="gameTypes"
                      :fieldNames="{ value: 'id', label: 'name' }"
                    />
                  </a-form-item>
                </div>
                <div class="flex justify-between gap-3">
                  <div class="w-full">
                    <a-form-item label="賽事名稱" name="name">
                      <a-input type="input" v-model:value="modal.data.name" />
                    </a-form-item>
                  </div>
                </div>
                <div class="">
                  <a-form-item label="國家" name="country">
                    <a-select
                      v-model:value="modal.data.country"
                      show-search
                      :options="countries"
                      :fieldNames="{ value: 'name', label: 'name' }"
                    />
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="城市" name="city">
                    <a-input class="" type="input" v-model:value="modal.data.scale" />
                  </a-form-item>
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
                        defaultValue="1"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="Section Number" name="section_number">
                      <a-input-number
                        v-model:value="modal.data.section_number"
                        style="width: 150px"
                        defaultValue="1"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="Referee Number" name="referee_number">
                      <a-input-number
                        v-model:vlaue="modal.data.referee_number"
                        style="width: 150px"
                        defaultValue="1"
                        :min="1"
                      >
                      </a-input-number>
                    </a-form-item>
                  </div>
                </div>
                <div class="">
                  <a-form-item label="Remark" name="remark">
                    <a-textarea v-model:value="modal.data.remark" :rows="5" />
                  </a-form-item>
                </div>
              </div>
              <div class="flex flex-col" v-else-if="setting_index == 1">
                <div class="">
                  <a-form-item label="類型" name="type">
                    <a-radio-group v-model:value="modal.data.type">
                      <a-radio :value="1">Individual</a-radio>
                      <a-radio :value="2">Teams</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="年齡組別" name="age_group">
                    <a-radio-group v-model:value="modal.data.age_group">
                      <a-radio :value="1">Seniors</a-radio>
                      <a-radio :value="2">U23</a-radio>
                      <a-radio :value="3">Juniors</a-radio>
                      <a-radio :value="4">Cadets</a-radio>
                      <a-radio :value="5">IBSA</a-radio>
                      <a-radio :value="6">EYOF</a-radio>
                      <a-radio :value="7">Others</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="性別" name="gender">
                    <a-radio-group v-model:value="modal.data.gender">
                      <a-radio :value="1">male & female</a-radio>
                      <a-radio :value="2">male</a-radio>
                      <a-radio :value="3">female</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="男性組別" name="categories_male">
                    <div class="" v-for="category in gameTypes[0].categories">
                      <div class="">
                        {{ category.weights.includes('MW') }}
                      </div>
                    </div>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="女性組別" name="categories_female"> </a-form-item>
                </div>
              </div>
              <div class="flex justify-between gap-3">
                <!-- <div class="w-full">
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
                </div> -->
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
                    v-if="
                      modal.data?.competition_type?.is_language_secondary_enabled == 1
                    "
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
                    v-if="
                      modal.data?.competition_type?.is_language_secondary_enabled == 1
                    "
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
              <div class="flex justify-end h-full">
                <a-form-item>
                  <a-button class="text-right" @click="onCreate">Create</a-button>
                </a-form-item>
              </div>
            </div>
          </a-form>
        </div>
      </div>
    </div>
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
  props: ["countries", "gameTypes", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      disabledDate: null,
      tmpContestTime: null,
      setting_index: 0,
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
    changeSettingIndex(index) {
      this.setting_index = index;
    },
  },
};
</script>
