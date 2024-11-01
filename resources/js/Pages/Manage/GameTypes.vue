<template>
  <inertia-head title="Game types" />

  <AdminLayout>
    <div class="py-12 mx-8 md:max-w-6xl xl:mx-auto">
      <div class="mb-8 flex justify-between flex-col md:flex-row">
        <div class="text-xl font-bold">Competition types</div>
        <div>
          <a-button
            type="primary"
            class="bg-blue-500"
            @click="createNewGameType"
            :disabled="newGameType.isCreateing"
            >Create competition type</a-button
          >
        </div>
      </div>
      <a-card
        class="mb-2"
        title="Create a new competition type"
        v-if="newGameType.isCreateing"
      >
        <template #extra>
          <a-button @click="newGameType.isCreateing = false">Cancel</a-button>
        </template>
        <div class="flex gap-6 sm:flex-row flex-col">
          <div class="w-1/2">
            <a-form
              ref="newGameTypeRef"
              :model="newGameType"
              layout="vertical"
              autocomplete="off"
              :rules="rules"
              :validate-messages="validateMessages"
            >
              <a-form-item name="language" label="Language">
                <a-select
                  v-model:value="newGameType.language"
                  :options="languages"
                  style="width: 200px"
                ></a-select>
              </a-form-item>
              <a-form-item label="Open secondary language">
                <a-switch
                  v-model:checked="newGameType.is_language_secondary_enabled"
                  :unCheckedValue="0"
                  :checkedValue="1"
                />
              </a-form-item>
              <a-form-item
                name="language"
                label="Language Secondary"
                v-if="newGameType.is_language_secondary_enabled == 1"
              >
                <a-select
                  v-model:value="newGameType.language_secondary"
                  :options="languages"
                  style="width: 200px"
                ></a-select>
              </a-form-item>
              <a-form-item name="awarding_methods">
                <template #label>
                  <div class="flex items-center gap-2">
                    <span>Awarding Methods</span>
                    <a-tooltip placement="right">
                      <template #title>
                        <span
                          >In judo competitions while involving 4 participants or fewer,
                          the handling of winning ranks typically varies based on the
                          event's regulations. Generally, there are two common approaches:
                          Method one awards all athletes a rank, while method two limits
                          the number of ranked to the total number of althlets minus
                          one.</span
                        >
                      </template>
                      <InfoCircleOutlined />
                    </a-tooltip>
                  </div>
                </template>
                <div class="flex items-center gap-3">
                  Method1
                  <a-switch
                    v-model:checked="newGameType.awarding_methods"
                    :unCheckedValue="0"
                    :checkedValue="1"
                  />
                  Method2
                </div>
              </a-form-item>
              <p class="underline font-bold">Type information</p>
              <a-form-item name="name" label="Name">
                <a-input type="input" v-model:value="newGameType.name"></a-input>
              </a-form-item>
              <a-form-item
                name="name_secondary"
                label="Name Secondary"
                v-if="newGameType.is_language_secondary_enabled"
              >
                <a-input
                  type="input"
                  v-model:value="newGameType.name_secondary"
                ></a-input>
              </a-form-item>
              <a-form-item name="code" label="Code">
                <a-input type="input" v-model:value="newGameType.code"></a-input>
              </a-form-item>
            </a-form>
          </div>
        </div>
        <div class="text-right">
          <a-button
            type="primary"
            class="bg-blue-500"
            @click="saveCompetitionType(newGameType)"
            >Save</a-button
          >
        </div>
      </a-card>
      <template v-for="gameType in gameTypes" :key="gameType.id">
        <a-card :title="gameType.name" :bordered="false" class="mb-4">
          <template #extra>
            <a-button v-if="gameType.isEditing" @click="reload">Cancel</a-button>
            <a-button v-else @click="gameType.isEditing = true">Edit</a-button>
          </template>
          <div class="flex gap-6 sm:flex-row flex-col">
            <div class="w-1/2">
              <a-form
                v-if="gameType.isEditing == true"
                ref="gameTypeRef"
                :model="gameType"
                layout="vertical"
                autocomplete="off"
                :disabled="!gameType.isEditing"
                :rules="rules"
                :validate-messages="validateMessages"
              >
                <a-form-item name="language" label="Language">
                  <a-select
                    v-model:value="gameType.language"
                    :options="languages"
                    style="width: 200px"
                  ></a-select>
                </a-form-item>
                <a-form-item label="Open secondary language" v-if="gameType.isEditing">
                  <a-switch
                    v-model:checked="gameType.is_language_secondary_enabled"
                    :unCheckedValue="0"
                    :checkedValue="1"
                  />
                </a-form-item>
                <a-form-item
                  name="language"
                  label="Language Secondary"
                  v-if="gameType.is_language_secondary_enabled == 1"
                >
                  <a-select
                    v-if="gameType.isEditing"
                    v-model:value="gameType.language_secondary"
                    :options="languages"
                    style="width: 200px"
                  ></a-select>
                  <p v-else>
                    <span
                      v-if="languages.find((l) => l.value == gameType.language_secondary)"
                    >
                      {{
                        languages.find((l) => l.value == gameType.language_secondary)
                          .label
                      }}
                    </span>
                  </p>
                </a-form-item>
                <a-form-item name="awarding_methods">
                  <template #label>
                    <div class="flex items-center gap-2">
                      <span>Awarding Methods</span>
                      <a-tooltip placement="right">
                        <template #title>
                          <span
                            >In judo competitions while involving 4 participants or fewer,
                            the handling of winning ranks typically varies based on the
                            event's regulations. Generally, there are two common
                            approaches: Method one awards all athletes a rank, while
                            method two limits the number of ranked to the total number of
                            althlets minus one.</span
                          >
                        </template>
                        <InfoCircleOutlined />
                      </a-tooltip>
                    </div>
                  </template>
                  <div class="flex items-center gap-3" v-if="gameType.isEditing">
                    Method1
                    <a-switch
                      v-model:checked="newGameType.awarding_methods"
                      :unCheckedValue="0"
                      :checkedValue="1"
                    />
                    Method2
                  </div>
                  <div class="flex items-center gap-3" v-else>
                    {{ newGameType.awarding_methods == 0 ? "Method1" : "Method2" }}
                  </div>
                </a-form-item>
                <p class="underline font-bold">Type infomation</p>
                <a-form-item name="name" label="Name">
                  <a-input
                    type="input"
                    v-model:value="gameType.name"
                    v-if="gameType.isEditing"
                  ></a-input>
                  <div v-else>{{ gameType.name }}</div>
                </a-form-item>
                <a-form-item
                  name="name_secondary"
                  label="Name secondary"
                  v-if="gameType.is_language_secondary_enabled"
                >
                  <a-input
                    type="input"
                    v-model:value="gameType.name_secondary"
                    v-if="gameType.isEditing"
                  ></a-input>
                  <div v-else>{{ gameType.name_secondary }}</div>
                </a-form-item>
                <a-form-item name="code" label="Code">
                  <a-input
                    type="input"
                    v-model:value="gameType.code"
                    v-if="gameType.isEditing"
                  ></a-input>
                  <div v-else>{{ gameType.code }}</div>
                </a-form-item>
              </a-form>
              <div v-else class="text-[14px] flex flex-col gap-3">
                <div class="">Language</div>
                <div class="mb-3">
                  {{ languages.find((l) => l.value == gameType.language).label }}
                </div>
                <div class="flex items-center gap-2">
                  Awarding Methods<a-tooltip placement="right">
                    <template #title>
                      <span
                        >In judo competitions while involving 4 participants or fewer, the
                        handling of winning ranks typically varies based on the event's
                        regulations. Generally, there are two common approaches: Method
                        one awards all athletes a rank, while method two limits the number
                        of ranked to the total number of althlets minus one.</span
                      >
                    </template>
                    <InfoCircleOutlined />
                  </a-tooltip>
                </div>
                <div class="mb-3">
                  {{ gameType.awarding_methods == 1 ? "Method2" : "Method1" }}
                </div>
                <div class="" v-if="gameType.is_language_secondary_enabled == 1">
                  Language secondary
                </div>
                <div class="mb-3" v-if="gameType.is_language_secondary_enabled == 1">
                  {{
                    languages.find((l) => l.value == gameType.language_secondary).label
                  }}
                </div>
                <p class="underline font-bold">Type infomation</p>
                <div class="">Name</div>
                <div class="mb-3">{{ gameType.name }}</div>
                <div class="">Name secondary</div>
                <div class="mb-3">{{ gameType.name_secondary }}</div>
                <div class="">Code</div>
                <div class="mb-3">{{ gameType.code }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-6 w-1/2">
              <div class="flex items-center gap-2">
                Category and Weights
                <a-tooltip placement="right">
                  <template #title>
                    <span
                      >Category and Weights code needs to be filled in with the
                      format<span class="text-red-400">(MW/FW(Weight)(+/-))</span></span
                    >
                  </template>
                  <InfoCircleOutlined />
                </a-tooltip>
              </div>
              <div v-for="category in gameType.categories" :key="category.id">
                <div class="flex items-center mb-2 gap-2">
                  <a-tag>{{ category.code }}</a-tag>
                  {{ category.name }}
                  <template v-if="gameType.is_language_secondary_enabled">
                    / {{ category.name_secondary }}
                  </template>
                  <span class="text-sm text-blue-500 flex items-center gap-1"
                    ><ClockCircleOutlined /> {{ category.duration }}</span
                  >
                  <template v-if="gameType.isEditing">
                    <a-button
                      type="link"
                      @click="confirmEditCategory(gameType)"
                      v-if="gameType.editCategory == category.id"
                    >
                      Confirm
                    </a-button>
                    <a-button
                      type="link"
                      @click="gameType.editCategory = category.id"
                      v-else
                    >
                      Edit
                    </a-button>
                    <a-button
                      type="link"
                      danger
                      @click="removeCategory(gameType, category)"
                    >
                      Remove
                    </a-button>
                  </template>
                </div>

                <div class="flex flex-col gap-3">
                  <div
                    class="flex flex-col gap-6"
                    v-if="gameType.editCategory == category.id"
                  >
                    <a-form
                      ref="gameCategoryRef"
                      :model="category"
                      layout="vertical"
                      autocomplete="off"
                      :disabled="!gameType.isEditing"
                      :rules="rules"
                      :validate-messages="validateMessages"
                    >
                      <a-form-item name="code" label="Code">
                        <a-input type="input" v-model:value="category.code"></a-input>
                      </a-form-item>
                      <a-form-item name="name" label="Name">
                        <a-input type="input" v-model:value="category.name"></a-input>
                      </a-form-item>
                      <a-form-item
                        name="name_secondary"
                        label="Name Secondary"
                        v-if="gameType.is_language_secondary_enabled"
                      >
                        <a-input
                          type="input"
                          v-model:value="category.name_secondary"
                        ></a-input>
                      </a-form-item>
                      <a-form-item name="duration" label="Category Duration">
                        <a-time-picker
                          v-model:value="category.duration"
                          format="mm:ss"
                          value-format="mm:ss"
                        />
                      </a-form-item>
                    </a-form>
                  </div>
                  <div class="flex items-center">
                    <a-space :size="[0, 'small']" wrap>
                      <template v-for="weight in category.weights" :key="weight">
                        <a-tag
                          class="flex items-center"
                          :color="weight[0] == 'F' ? 'pink' : 'blue'"
                          :closable="gameType.isEditing"
                          @close="removeTag($event, category, weight)"
                        >
                          {{ weight }}
                        </a-tag>
                      </template>
                      <div v-if="gameType.isEditing" class="flex items-center">
                        <a-input
                          type="input"
                          :class="category.addWeight == true ? 'block' : 'hidden'"
                          v-model:value="addWeight"
                          size="small"
                          class="w-28"
                          @pressEnter="confirmAddWeight(category)"
                          ><template #addonAfter>
                            <a @click="category.addWeight = false"
                              ><CloseCircleOutlined
                            /></a>
                          </template>
                        </a-input>
                        <a
                          class="flex items-center"
                          id="weightInput"
                          :class="category.addWeight != true ? 'block' : 'hidden'"
                          @click="category.addWeight = true"
                          ><PlusOutlined
                        /></a>
                      </div>
                    </a-space>
                  </div>
                </div>
              </div>
              <div v-if="gameType.isEditing == true" class="pt-3">
                <a-button @click="addCategory(gameType)">Add category</a-button>
              </div>
            </div>
          </div>
          <div v-if="gameType.isEditing" class="flex gap-2">
            <a-button
              type="primary"
              class="bg-blue-500"
              danger
              @click="deleteCompetitionType(gameType)"
              >Delete</a-button
            >
            <a-button
              type="primary"
              class="bg-blue-500"
              @click="saveCompetitionType(gameType)"
              >Save</a-button
            >
          </div>
        </a-card>
      </template>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
  PlusOutlined,
  CloseCircleOutlined,
  InfoCircleOutlined,
  ClockCircleOutlined,
  ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { ref, createVNode } from "vue";
import { Modal } from "ant-design-vue";
import { message } from "ant-design-vue";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
dayjs.extend(duration);

export default {
  components: {
    AdminLayout,
    PlusOutlined,
    InfoCircleOutlined,
    CloseCircleOutlined,
    ClockCircleOutlined,
    ExclamationCircleOutlined,
  },
  props: ["gameTypes", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      isEditing: false,
      addWeight: "",
      newCategoriesCount: 0,
      removeCategories: "",
      newGameType: {
        awarding_methods: 0,
        is_language_secondary_enabled: 0,
      },
      rules: {
        language: { required: true },
        language_secondary: { required: true },
        name: { required: true },
        code: { required: true },
        duration: { required: true },
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
  created() {
    window.app = this;
  },
  methods: {
    reload() {
      this.$inertia.reload(["gameTypes"]);
    },
    createNewGameType() {
      this.newGameType = {
        is_language_secondary_enabled: 0,
        awarding_methods: 0,
      };
      this.newGameType.isCreateing = true;
    },
    saveCompetitionType(record) {
      console.log(record);
      record.categories?.forEach(
        (x) =>
          (x.editDuration = dayjs
            .duration({
              minutes: x.duration.split(":")[0],
              seconds: x.duration.split(":")[1],
            })
            .asSeconds())
      );
      // console.log(record.categories);
      this.$refs.gameTypeRef[0].validateFields().then(() => {
        this.$inertia.post(route("manage.gameTypes.store"), record, {
          onSuccess: (res) => {
            this.newGameType.isCreateing = false;
            message.success("Creation successful");
          },
          onError: (errors) => {
            message.success("Creation failed");
          },
        });
      });
    },
    addCategory(gameType) {
      if (this.gameType?.editCategory) {
        this.$refs.newGameTypeRef.validateFields().then(() => {
          let id = 100000000 + this.newCategoriesCount;
          gameType.categories.push({
            id: id,
            code: "",
            game_type_id: gameType.id,
            name: "",
            weights: [],
            name_secondary: "",
            duartion: "",
          });
          gameType.editCategory = id;
          this.newCategoriesCount++;
        });
      } else {
        let id = 100000000 + this.newCategoriesCount;
        gameType.categories.push({
          id: id,
          code: "",
          game_type_id: gameType.id,
          name: "",
          weights: [],
          name_secondary: "",
          duartion: "",
        });
        gameType.editCategory = id;
        this.newCategoriesCount++;
      }
    },
    test() {
      console.log(this.gameTypes);
    },
    removeTag(event, category, weight) {
      category.weights = category.weights.filter(function (cweight) {
        return cweight !== weight;
      });
    },
    removeCategory(gameType, category) {
      if (!gameType.removeCategories) {
        gameType.removeCategories = [];
      }
      gameType.removeCategories.push(category);
      const index = gameType.categories.findIndex((c) => c.id === category.id);
      if (index >= 0) {
        gameType.categories.splice(index, 1);
      }
    },
    confirmEditCategory(gameType) {
      console.log("aaa", this.$refs?.gameCategoryRef ?? "error");
      this.$refs.gameCategoryRef[0].validateFields().then(() => {
        gameType.editCategory = null;
      });
    },
    confirmAddWeight(category) {
      if (
        this.addWeight.match(/^(FW|MW)((\d{1,3})(\+|-))|ULW$/) &&
        !category.weights.includes(this.addWeight)
      ) {
        category.weights.push(this.addWeight);
        this.addWeight = "";
        category.addWeight = false;
      } else {
        message.error("The Category and Weights format is wrong or already exists");
      }
    },
    deleteCompetitionType(record) {
      Modal.confirm({
        title: "Are you sure",
        icon: createVNode(ExclamationCircleOutlined),
        content: "Delete game type?",
        okText: "confirm",
        cancelText: "cancel",
        onOk: () => {
          this.$inertia.delete(route("manage.gameTypes.destroy", record.id), {
            onSuccess: (page) => {
              message.success("Deletion successful");
              console.log(page);
            },
            onError: (error) => {
              message.error("Deletion failed");
              console.log(error);
            },
          });
        },
      });
    },
  },
};
</script>

<style scoped>
#modalFrom input:disabled {
  border: none;
  color: rgba(0, 0, 0, 0.88);
  background: none;
}
</style>
