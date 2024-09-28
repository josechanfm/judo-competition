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
      <a-card title="Create a new competition type" v-if="newGameType.isCreateing">
        <template #extra>
          <a-button @click="newGameType.isCreateing = false">Cancel</a-button>
        </template>
        <div class="flex gap-6 sm:flex-row flex-col">
          <div class="w-1/2">
            <a-form
              id="modalFrom"
              name="ModalForm"
              ref="formRef"
              :model="newGameType"
              layout="vertical"
              autocomplete="off"
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
              <p class="underline font-bold">Type information</p>
              <a-form-item name="name" label="Name">
                <a-input type="input" v-model:value="newGameType.name"></a-input>
              </a-form-item>
              <a-form-item name="name_secondary" label="Name Secondary">
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
                id="modalFrom"
                name="ModalForm"
                ref="formRef"
                :model="gameType"
                layout="vertical"
                autocomplete="off"
                :disabled="!gameType.isEditing"
              >
                <a-form-item name="language" label="Language">
                  <a-select
                    v-if="gameType.isEditing"
                    v-model:value="gameType.language"
                    :options="languages"
                    style="width: 200px"
                  ></a-select>
                  <p v-else>
                    {{ languages.find((l) => l.value == gameType.language).label }}
                  </p>
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
                <p class="underline font-bold">Type infomation</p>
                <a-form-item name="name" label="Name">
                  <a-input type="input" v-model:value="gameType.name"></a-input>
                </a-form-item>
                <a-form-item name="name_secondary" label="Name secondary">
                  <a-input type="input" v-model:value="gameType.name_secondary"></a-input>
                </a-form-item>
                <a-form-item name="code" label="Code">
                  <a-input type="input" v-model:value="gameType.code"></a-input>
                </a-form-item>
              </a-form>
            </div>
            <div class="flex flex-col gap-3 w-1/2">
              <div>Category and Weights</div>
              <div v-for="category in gameType.categories" :key="category.id">
                <div class="mb-2">
                  <a-tag>{{ category.code }}</a-tag>
                  {{ category.name }}
                  <template v-if="gameType.is_language_secondary_enabled">
                    / {{ category.name_secondary }}
                  </template>
                  <span class="pl-2 mt-2 text-sm text-blue-500"
                    ><ClockCircleOutlined /> {{ category.duration }}</span
                  >
                  <template v-if="gameType.isEditing">
                    <a-button
                      type="link"
                      @click="gameType.editCategory = null"
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
                    :class="gameType.editCategory == category.id ? 'block' : 'hidden'"
                  >
                    <div class="">
                      <div>Code</div>
                      <a-input type="input" v-model:value="category.code"></a-input>
                    </div>
                    <div class="">
                      <div>Name</div>
                      <a-input type="input" v-model:value="category.name"></a-input>
                    </div>
                    <div class="">
                      <div>Name Secondary</div>
                      <a-input
                        type="input"
                        v-model:value="category.name_secondary"
                      ></a-input>
                    </div>
                    <div class="">
                      <div>Category duration</div>
                      <a-time-picker
                        v-model:value="category.duration"
                        format="mm:ss"
                        value-format="mm:ss"
                      />
                    </div>
                  </div>
                  <div>
                    <a-space :size="[0, 'small']" wrap>
                      <template v-for="weight in category.weights" :key="weight">
                        <a-tag
                          :color="weight[0] == 'F' ? 'pink' : 'blue'"
                          :closable="gameType.isEditing"
                          @close="removeTag($event, category, weight)"
                        >
                          {{ weight }}
                        </a-tag>
                      </template>
                      <div v-if="gameType.isEditing">
                        <a-input
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
              <div v-if="gameType.isEditing == true">
                <a-button @click="addCategory(gameType)">Add category</a-button>
              </div>
            </div>
          </div>
          <div v-if="gameType.isEditing">
            <a-button type="primary" class="bg-blue-500" danger>Delete</a-button>
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
  ClockCircleOutlined,
} from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
dayjs.extend(duration);

export default {
  components: {
    AdminLayout,
    PlusOutlined,
    CloseCircleOutlined,
    ClockCircleOutlined,
  },
  props: ["gameTypes", "languages"],
  data() {
    return {
      dateFormat: "YYYY-MM-DD",
      isEditing: false,
      addWeight: "",
      removeCategories: "",
      newGameType: {
        is_language_secondary_enabled: 0,
      },
    };
  },
  created() {},
  methods: {
    reload() {
      this.$inertia.reload(["gameTypes"]);
    },
    createNewGameType() {
      this.newGameType = {
        is_language_secondary_enabled: 0,
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
      console.log(record);
      this.$inertia.post(route("manage.gameTypes.store"), record, {
        onSuccess(res) {
          console.log(res);
        },
        onError(errors) {
          console.log(errors);
        },
      });
    },
    addCategory(gameType) {
      gameType.categories.push({
        id: 100000000,
        code: "",
        game_type_id: gameType.id,
        name: "",
        weights: [],
        name_secondary: "",
        duartion: "",
      });
      gameType.editCategory = 100000000;
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
    confirmAddWeight(category) {
      if (
        this.addWeight.match(/^(FW|MW)(\d{1,3})(\+|-)$/) &&
        !category.weights.includes(this.addWeight)
      ) {
        category.weights.push(this.addWeight);
        this.addWeight = "";
        category.addWeight = false;
      } else {
        message.error("公斤格式錯誤或已有此公斤");
      }
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
