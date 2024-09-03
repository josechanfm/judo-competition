<template>
  <inertia-head title="Competition Create" />

  <AdminLayout>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">Competition Create</div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex">
        <div class="border-r-2 border-gray-300 shrink-0 py-2 px-2">
          <div class="flex flex-col font-bold text-lg gap-2">
            <div>
              <a-button
                type="text"
                class="text-base font-bold flex items-center py-4"
                :class="setting_index == 0 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(0)"
                >Basie information</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-base font-bold flex items-center py-4"
                :class="setting_index == 1 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(1)"
                >Category Setting</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-base font-bold flex items-center py-4"
                :class="setting_index == 2 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(2)"
                >System Setting</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-base font-bold flex items-center py-4"
                :class="setting_index == 3 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(3)"
                >Language Setting</a-button
              >
            </div>
          </div>
        </div>
        <div class="w-full p-4">
          <a-form
            name="ModalForm"
            ref="formRef"
            :model="create_competition"
            layout="vertical"
            autocomplete="off"
            :rules="rules"
            :validate-messages="validateMessages"
          >
            <div class="flex flex-col">
              <div class="flex flex-col" v-if="setting_index == 0">
                <div class="">
                  <a-form-item label="Competition Type" name="game_type_id">
                    <a-select
                      @change="changeGameType"
                      type="select"
                      v-model:value="create_competition.game_type_id"
                      show-search
                      :options="gameTypes"
                      :fieldNames="{ value: 'id', label: 'name' }"
                    />
                  </a-form-item>
                </div>
                <div class="flex justify-between gap-3">
                  <div class="w-full">
                    <a-form-item label="Competition Name" name="name">
                      <a-input type="input" v-model:value="create_competition.name" />
                    </a-form-item>
                  </div>
                </div>
                <div class="">
                  <a-form-item label="Country" name="country">
                    <a-select
                      v-model:value="create_competition.country"
                      show-search
                      :options="countries"
                      :fieldNames="{ value: 'name', label: 'name' }"
                    />
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="City" name="city">
                    <a-input
                      class=""
                      type="input"
                      v-model:value="create_competition.scale"
                    />
                  </a-form-item>
                </div>

                <div class="flex justify-between gap-3">
                  <div class="w-full">
                    <a-form-item label="Start Date" name="date_start">
                      <a-date-picker
                        v-model:value="create_competition.date_start"
                        :format="dateFormat"
                        :valueFormat="dateFormat"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="End Date" name="date_end">
                      <a-date-picker
                        v-model:value="create_competition.date_end"
                        :format="dateFormat"
                        :disabled-date="endDateDisabled"
                        :valueFormat="dateFormat"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="Mat Number" name="mat_number">
                      <a-input-number
                        v-model:value="create_competition.mat_number"
                        style="width: 150px"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="Section Number" name="section_number">
                      <a-input-number
                        v-model:value="create_competition.section_number"
                        style="width: 150px"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item label="Referee Number" name="referee_number">
                      <a-input-number
                        v-model:vlaue="create_competition.referee_number"
                        style="width: 150px"
                        defaultValue="1"
                        :min="1"
                      >
                      </a-input-number>
                    </a-form-item>
                  </div>
                </div>
                <div class="w-full flex-col">
                  <a-form-item label="Days" name="days">
                    <div
                      class="rounded shadow border p-2 w-72 flex items-center mb-2"
                      v-for="time in create_competition.days"
                      :key="time"
                    >
                      <div class="flex-1 font-bold">{{ time }}</div>
                      <div>
                        <a-button type="link" danger @click="removeTimeFromForm(time)"
                          >Remove</a-button
                        >
                      </div>
                    </div>
                  </a-form-item>
                  <div class="flex gap-3 mb-2">
                    <a-date-picker
                      :disabled="
                        !create_competition.date_start || !create_competition.date_end
                      "
                      v-model:value="tmpContestTime"
                      :disabled-date="disabledDate"
                      value-format="YYYY-MM-DD"
                    />
                    <a-button @click="addTimeToForm">Add</a-button>
                  </div>
                </div>
                <div class="">
                  <a-form-item label="Remark" name="remark">
                    <a-textarea v-model:value="create_competition.remark" :rows="5" />
                  </a-form-item>
                </div>
              </div>
              <div class="flex flex-col" v-else-if="setting_index == 1">
                <div class="" v-if="create_competition.game_type_id != null">
                  <div class="">
                    <a-form-item label="Type" name="type">
                      <a-radio-group v-model:value="create_competition.type">
                        <a-radio value="I">Individual</a-radio>
                        <a-radio value="T">Teams</a-radio>
                      </a-radio-group>
                    </a-form-item>
                  </div>
                  <div class="">
                    <a-form-item label="Gender" name="gender">
                      <a-radio-group v-model:value="create_competition.gender">
                        <a-radio :value="2">male & female</a-radio>
                        <a-radio :value="1">male</a-radio>
                        <a-radio :value="0">female</a-radio>
                      </a-radio-group>
                    </a-form-item>
                  </div>
                  <div
                    class=""
                    v-if="
                      create_competition.gender == 1 || create_competition.gender == 2
                    "
                  >
                    <a-form-item label="Male Categories" name="categories_male">
                      <div
                        class=""
                        v-for="category in create_competition.competition_type.categories"
                        :key="category.id"
                      >
                        <div class="">
                          {{ category.name }}
                        </div>
                        <div class="">
                          {{
                            category.weights
                              .filter((x) => x.includes("MW"))
                              .map((weight) => {
                                if (weight.slice(2, -1) == "UL") {
                                  return "無限量";
                                } else {
                                  return `-${weight.slice(2, -1)}kg`;
                                }
                              })
                              .join(",")
                          }}
                        </div>
                      </div>
                    </a-form-item>
                  </div>
                  <div
                    class=""
                    v-if="
                      create_competition.gender == 0 || create_competition.gender == 2
                    "
                  >
                    <a-form-item label="Female Categories" name="categories_female">
                      <div
                        class=""
                        v-for="category in create_competition.competition_type.categories"
                        :key="category.id"
                      >
                        <div class="">
                          {{ category.name }}
                        </div>
                        <div class="">
                          {{
                            category.weights
                              .filter((x) => x.includes("FW"))
                              .map((weight) => {
                                if (weight.slice(2, -1) == "UL") {
                                  return "無限量";
                                } else {
                                  return `-${weight.slice(2, -1)}kg`;
                                }
                              })
                              .join(",")
                          }}
                        </div>
                      </div>
                    </a-form-item>
                  </div>
                </div>
              </div>
              <div class="" v-if="setting_index == 2">
                <div class="">
                  <a-form-item label="Competition System" name="system">
                    <a-radio-group v-model:value="create_competition.system">
                      <a-radio value="Q">Quarter</a-radio>
                      <a-radio value="F">Full</a-radio>
                      <a-radio value="K">KO</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="Seeding" name="seeding">
                    <a-radio-group v-model:value="create_competition.seeding">
                      <a-radio :value="8">8 players</a-radio>
                      <a-radio :value="4">4 players</a-radio>
                      <a-radio :value="0">no player</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div>
                  <a-form-item label="Less than 5 people">
                    <div class="flex gap-3">
                      <div class="">2 players</div>
                      <a-radio-group v-model:value="create_competition.small_system[2]">
                        <a-radio :value="false">one Final</a-radio>
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">3 players</div>
                      <a-radio-group v-model:value="create_competition.small_system[3]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true">Semi-Finals + Final</a-radio>
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">4 players</div>
                      <a-radio-group v-model:value="create_competition.small_system[4]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true">Semi-Finals + one Bronze + Final</a-radio>
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">5 players</div>
                      <a-radio-group v-model:value="create_competition.small_system[5]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true"
                          >Pool with 2 and pool with 3 - best each in Final, second in one
                          Bronze</a-radio
                        >
                      </a-radio-group>
                    </div>
                  </a-form-item>
                </div>
              </div>
              <div class="" v-if="setting_index == 3">
                <div class="">
                  <a-form-item
                    label="Open language secondary"
                    :name="['competition_type', 'is_language_secondary_enabled']"
                  >
                    <a-switch
                      v-model:checked="
                        create_competition.competition_type.is_language_secondary_enabled
                      "
                      :unCheckedValue="0"
                      :checkedValue="1"
                    />
                  </a-form-item>
                </div>
                <div class="flex justify-between gap-3">
                  <div class="w-full flex flex-col">
                    <div class="w-full">
                      <a-form-item label="Name" name="name">
                        <a-input type="input" v-model:value="create_competition.name" />
                      </a-form-item>
                    </div>
                    <div
                      class=""
                      v-if="
                        create_competition.competition_type
                          .is_language_secondary_enabled == 1
                      "
                    >
                      <a-form-item
                        label="Competition Name Secondary"
                        name="name_secondary"
                      >
                        <a-input
                          type="input"
                          v-model:value="create_competition.name_secondary"
                        ></a-input>
                      </a-form-item>
                    </div>
                  </div>
                  <div class="w-full flex flex-col">
                    <div class="">
                      <a-form-item
                        label="Competition Language"
                        :name="['competition_type', 'language']"
                        :rules="[
                          {
                            required: true,
                            message: 'Competition Language is required!',
                          },
                        ]"
                      >
                        <a-select
                          v-model:value="create_competition.competition_type.language"
                          :options="selectLanguage"
                          :fieldNames="{ value: 'value', label: 'name' }"
                        ></a-select>
                      </a-form-item>
                    </div>
                    <div
                      class=""
                      v-if="
                        create_competition.competition_type
                          .is_language_secondary_enabled == 1
                      "
                    >
                      <a-form-item
                        label="Competition Language Secondary"
                        :name="['competition_type', 'language_secondary']"
                        :rules="[
                          {
                            required: true,
                            message: 'Competition Language Secondary is required!',
                          },
                        ]"
                      >
                        <a-select
                          v-model:value="
                            create_competition.competition_type.language_secondary
                          "
                          :options="selectLanguage"
                          :fieldNames="{ value: 'value', label: 'name' }"
                        ></a-select>
                      </a-form-item>
                    </div>
                  </div>
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
      create_competition: {
        system: "q",
        type: "individual",
        mat_number: 1,
        section_number: 1,
        referee_number: 1,
        gender: 2,
        days: [],
        seeding: 8,
        language: "en",
        small_system: {
          2: false,
          3: false,
          4: false,
          5: false,
        },
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
        name_secondary: { required: true },
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
          x.value == this.create_competition?.language ||
          x.value == this.create_competition?.language_secondary
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
      if (!this.create_competition.date_start && !this.create_competition.date_end) {
        return false;
      }
      return (
        current < moment(this.create_competition.date_start).valueOf() ||
        current > moment(this.create_competition.date_end).valueOf()
      );
    };
    this.endDateDisabled = (current) => {
      if (!this.create_competition.date_start) {
        return false;
      }
      return current < moment(this.create_competition.date_start).valueOf();
    };
  },
  methods: {
    addTimeToForm() {
      // should be unique
      if (
        this.create_competition.days.includes(this.tmpContestTime) ||
        !this.tmpContestTime
      ) {
        return;
      }

      this.create_competition.days.push(this.tmpContestTime);
    },
    removeTimeFromForm(time) {
      this.create_competition.days = this.create_competition.days.filter(
        (t) => t !== time
      );
    },
    changeGameType(value) {
      this.create_competition.competition_type = this.gameTypes.find(
        (x) => x.id == value
      );
      console.log(this.create_competition.competition_type);
    },
    onCreate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.post(
            route("manage.competitions.store"),
            this.create_competition,
            {
              onSuccess: (page) => {
                this.create_competition = {};
              },
              onError: (err) => {
                console.log(err);
              },
            }
          );

          console.log("values", this.create_competition, this.create_competition);
        })
        .catch((error) => {
          console.log("error", error);
        });
    },
    changeSettingIndex(index) {
      this.$refs.formRef.validateFields().then(() => {
        this.setting_index = index;
      });
    },
  },
};
</script>
