<template>
  <inertia-head :title="$t('competitions.edit_competition')" />

  <AdminLayout>
    <div class="py-12 mx-8">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">{{ $t("competitions.edit_competition") }}</div>
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
                >{{ $t("competitions.basic") }}</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 1 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(1)"
                >{{ $t("competitions.category_setting") }}</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 2 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(2)"
                >{{ $t("competitions.system_setting") }}</a-button
              >
            </div>
            <div>
              <a-button
                type="text"
                class="text-lg font-bold flex items-center py-4"
                :class="setting_index == 3 ? 'bg-gray-300' : ''"
                @click="changeSettingIndex(3)"
                >{{ $t("competitions.language_setting") }}</a-button
              >
            </div>
          </div>
        </div>
        <div class="w-full p-4">
          <a-form
            name="ModalForm"
            ref="formRef"
            :model="competition"
            layout="vertical"
            autocomplete="off"
            :rules="rules"
            :validate-messages="validateMessages"
          >
            <div class="flex flex-col">
              <div class="flex flex-col" v-if="setting_index == 0">
                <div class="">
                  <a-form-item :label="$t('competitions.type')" name="game_type_id">
                    {{ competition.competition_type.name }}
                  </a-form-item>
                </div>
                <div class="flex justify-between gap-3">
                  <div class="w-full">
                    <a-form-item :label="$t('competitions.name')" name="name">
                      <a-input type="input" v-model:value="competition.name" />
                    </a-form-item>
                  </div>
                </div>
                <div class="">
                  <a-form-item
                    :label="$t('competitions.country_or_region')"
                    name="country"
                  >
                    <a-select
                      v-model:value="competition.country"
                      show-search
                      :options="countries"
                      :fieldNames="{ value: 'name', label: 'name' }"
                    />
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item :label="$t('city')" name="city">
                    <a-input class="" type="input" v-model:value="competition.scale" />
                  </a-form-item>
                </div>

                <div class="flex justify-between gap-3">
                  <div class="w-full">
                    <a-form-item :label="$t('start_date')" name="date_start">
                      <a-date-picker
                        v-model:value="competition.date_start"
                        :format="dateFormat"
                        :valueFormat="dateFormat"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item :label="$t('end_date')" name="date_end">
                      <a-date-picker
                        v-model:value="competition.date_end"
                        :format="dateFormat"
                        :disabled-date="endDateDisabled"
                        :valueFormat="dateFormat"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item :label="$t('mat_number')" name="mat_number">
                      <a-input-number
                        v-model:value="competition.mat_number"
                        style="width: 150px"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item :label="$t('section_number')" name="section_number">
                      <a-input-number
                        v-model:value="competition.section_number"
                        style="width: 150px"
                        :min="1"
                      />
                    </a-form-item>
                  </div>
                  <div class="w-full">
                    <a-form-item :label="$t('referee_number')" name="referee_number">
                      <a-input-number
                        v-model:vlaue="competition.referee_number"
                        style="width: 150px"
                        defaultValue="1"
                        :min="1"
                      >
                      </a-input-number>
                    </a-form-item>
                  </div>
                </div>
                <div class="w-full flex-col">
                  <a-form-item :label="$t('competitions.days')" name="days">
                    <div
                      class="rounded shadow border p-2 w-72 flex items-center mb-2"
                      v-for="time in competition.days"
                      :key="time"
                    >
                      <div class="flex-1 font-bold">{{ time }}</div>
                      <div>
                        <a-button type="link" danger @click="removeTimeFromForm(time)">{{
                          $t("action.remove")
                        }}</a-button>
                      </div>
                    </div>
                  </a-form-item>
                  <div class="flex gap-3 mb-2">
                    <a-date-picker
                      :disabled="!competition.date_start || !competition.date_end"
                      v-model:value="tmpContestTime"
                      :disabled-date="disabledDate"
                      value-format="YYYY-MM-DD"
                    />
                    <a-button @click="addTimeToForm">{{ $t("add") }}</a-button>
                  </div>
                </div>
                <div class="">
                  <a-form-item :label="$t('remark')" name="remark">
                    <a-textarea v-model:value="competition.remark" :rows="5" />
                  </a-form-item>
                </div>
                <div class="flex justify-end h-full gap-3">
                  <a-button
                    class="text-right"
                    @click="
                      () => {
                        this.$refs.formRef.validateFields().then(() => {
                          this.setting_index++;
                        });
                      }
                    "
                    >Next</a-button
                  >
                </div>
              </div>
              <div class="flex flex-col" v-else-if="setting_index == 1">
                <div class="">
                  <div class="">
                    <a-form-item :label="$t('competitions.type')" name="type">
                      <a-radio-group v-model:value="competition.type">
                        <a-radio value="I">{{ $t("competitions.individual") }}</a-radio>
                        <a-radio value="T">{{ $t("competitions.teams") }}</a-radio>
                      </a-radio-group>
                    </a-form-item>
                  </div>
                  <div class="">
                    <a-form-item :label="$t('gender')" name="gender">
                      <a-radio-group v-model:value="competition.gender">
                        <a-radio :value="2">male & female</a-radio>
                        <a-radio :value="1">male</a-radio>
                        <a-radio :value="0">female</a-radio>
                      </a-radio-group>
                    </a-form-item>
                  </div>
                  <div class="" v-if="competition.gender == 1 || competition.gender == 2">
                    <a-form-item :label="$t('male_categories')" name="categories_male">
                      <div
                        class=""
                        v-for="category in competition_categories"
                        :key="category.id"
                      >
                        <div class="">
                          {{ category.name }}
                        </div>
                        <div class="">
                          {{ changeWeightFormat(category.weights, "MW") }}
                        </div>
                      </div>
                    </a-form-item>
                  </div>
                  <div class="" v-if="competition.gender == 0 || competition.gender == 2">
                    <a-form-item :label="$t('female_categories')" name="categories_female">
                      <div
                        class=""
                        v-for="category in competition_categories"
                        :key="category.id"
                      >
                        <div class="">
                          {{ category.name }}
                        </div>
                        <div class="">
                          {{ changeWeightFormat(category.weights, "FW") }}
                        </div>
                      </div>
                    </a-form-item>
                  </div>
                </div>
                <div class="flex justify-end h-full gap-3">
                  <a-button
                    class="text-right"
                    @click="
                      () => {
                        this.$refs.formRef.validateFields().then(() => {
                          this.setting_index--;
                        });
                      }
                    "
                    >Last</a-button
                  >
                  <a-button
                    class="text-right"
                    @click="
                      () => {
                        this.$refs.formRef.validateFields().then(() => {
                          this.setting_index++;
                        });
                      }
                    "
                    >Next</a-button
                  >
                </div>
              </div>
              <div class="" v-if="setting_index == 2">
                <div class="">
                  <a-form-item label="Competition System" name="system">
                    <a-radio-group v-model:value="competition.system">
                      <a-radio value="Q">Quarter</a-radio>
                      <a-radio value="F" disabled>Full(coming soon)</a-radio>
                      <a-radio value="K">KO</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div class="">
                  <a-form-item label="Seeding" name="seeding">
                    <a-radio-group v-model:value="competition.seeding">
                      <a-radio :value="8">8 players</a-radio>
                      <a-radio :value="4">4 players</a-radio>
                      <a-radio :value="0">no player</a-radio>
                    </a-radio-group>
                  </a-form-item>
                </div>
                <div>
                  <a-form-item label="5人以下">
                    <div class="flex gap-3">
                      <div class="">2 players</div>
                      <a-radio-group v-model:value="competition.small_system[2]">
                        <a-radio :value="false">one Final</a-radio>
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">3 players</div>
                      <a-radio-group v-model:value="competition.small_system[3]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true" disabled
                          >Semi-Finals + Final(coming soon)</a-radio
                        >
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">4 players</div>
                      <a-radio-group v-model:value="competition.small_system[4]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true" disabled
                          >Semi-Finals + one Bronze + Final(coming soon)</a-radio
                        >
                      </a-radio-group>
                    </div>
                    <div class="flex gap-3">
                      <div class="">5 players</div>
                      <a-radio-group v-model:value="competition.small_system[5]">
                        <a-radio :value="false">Round Robin</a-radio>
                        <a-radio :value="true" disabled
                          >Pool with 2 and pool with 3 - best each in Final, second in one
                          Bronze(coming soon)</a-radio
                        >
                      </a-radio-group>
                    </div>
                  </a-form-item>
                </div>
                <div class="flex justify-end h-full gap-3">
                  <a-button
                    class="text-right"
                    @click="
                      () => {
                        this.$refs.formRef.validateFields().then(() => {
                          this.setting_index--;
                        });
                      }
                    "
                    >Last</a-button
                  >
                  <a-button
                    class="text-right"
                    @click="
                      () => {
                        this.$refs.formRef.validateFields().then(() => {
                          this.setting_index++;
                        });
                      }
                    "
                    >Next</a-button
                  >
                </div>
              </div>
              <div class="" v-if="setting_index == 3">
                <div class="">
                  <a-form-item
                    label="Open language secondary"
                    name="is_language_secondary_enabled"
                  >
                    <a-switch
                      v-model:checked="
                        competition.competition_type.is_language_secondary_enabled
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
                        <a-input type="input" v-model:value="competition.name" />
                      </a-form-item>
                    </div>
                    <div
                      class=""
                      v-if="
                        competition.competition_type.is_language_secondary_enabled == 1
                      "
                    >
                      <a-form-item
                        label="Competition Name Secondary"
                        name="name_secondary"
                      >
                        <a-input
                          type="input"
                          v-model:value="competition.name_secondary"
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
                          v-model:value="competition.competition_type.language"
                          :options="selectLanguage"
                          :fieldNames="{ value: 'value', label: 'name' }"
                        ></a-select>
                      </a-form-item>
                    </div>
                    <div
                      class=""
                      v-if="
                        competition.competition_type.is_language_secondary_enabled == 1
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
                          v-model:value="competition.competition_type.language_secondary"
                          :options="selectLanguage"
                          :fieldNames="{ value: 'value', label: 'name' }"
                        ></a-select>
                      </a-form-item>
                    </div>
                  </div>
                </div>
                <div class="flex justify-end h-full">
                  <a-button class="text-right" @click="onUpdate">Update</a-button>
                </div>
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
  props: ["competition", "competition_categories", "countries", "gameTypes", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      disabledDate: null,
      tmpContestTime: null,
      setting_index: 0,
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
        country: { required: true },
        name: { required: true },
        name_secondary: { required: true },
        date_start: { required: true },
        date_end: { required: true },
        days: { required: true },
        mat_number: { required: true },
        language: { required: true },
        language_secondary: { required: true },
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
          x.value == this.competition.competition_type.language ||
          x.value == this.competition.competition_type.language_secondary
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
      if (!this.competition.date_start && !this.competition.date_end) {
        return false;
      }
      return (
        current < moment(this.competition.date_start).valueOf() ||
        current > moment(this.competition.date_end).valueOf()
      );
    };
    this.endDateDisabled = (current) => {
      if (!this.competition.date_start) {
        return false;
      }
      return current < moment(this.competition.date_start).valueOf();
    };
  },
  methods: {
    addTimeToForm() {
      // should be unique
      if (this.competition.days.includes(this.tmpContestTime) || !this.tmpContestTime) {
        return;
      }

      this.competition.days.push(this.tmpContestTime);
    },
    removeTimeFromForm(time) {
      this.competition.days = this.competition.days.filter((t) => t !== time);
    },
    changeWeightFormat(weights, gender) {
      return weights
        .filter((x) => x.includes(gender))
        .map((weight) => {
          let weight_name = weight.replace(gender, "");
          if (weight_name.includes("-")) {
            weight_name = weight_name.replace("-", "");
            return `-${weight_name}kg`;
          } else if (weight_name.includes("+")) {
            weight_name = weight_name.replace("+", "");
            return `+${weight_name}kg`;
          } else {
            return weight_name;
          }
        })
        .join(",");
    },
    onUpdate() {
      this.$refs.formRef
        .validateFields()
        .then(() => {
          this.$inertia.put(
            route("manage.competitions.update", this.competition.id),
            this.competition,
            {
              onSuccess: (page) => {
                this.competition = {};
              },
              onError: (err) => {
                console.log(err);
              },
            }
          );
          console.log("values", this.competition, this.competition);
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
