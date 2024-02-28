<template>
  <inertia-head title="Dashboard" />

  <AdminLayout>
    <template #header>
      <div class="mx-4 py-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
      </div>
    </template>
    <div class="py-12 mx-auto max-w-6xl">
      <div class="mb-8 flex justify-between">
        <div class="text-xl font-bold">賽事類型設定</div>
        <div>
          <a-button
            type="primary"
            @click="createNewGameType"
            :disabled="newGameType.isCreateing"
            >創建新的類型</a-button
          >
        </div>
      </div>
      <a-card title="創建新的賽事類型" v-if="newGameType.isCreateing">
        <template #extra>
          <a-button @click="newGameType.isCreateing = false">取消</a-button>
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
              <a-form-item name="language" label="Language Secondary">
                <a-select
                  v-model:value="newGameType.language_secondary"
                  :options="languages"
                  style="width: 200px"
                ></a-select>
              </a-form-item>
              <p class="underline font-bold">類型基本信息</p>
              <a-form-item name="name" label="類型名稱(繁體中文)">
                <a-input v-model:value="newGameType.name"></a-input>
              </a-form-item>
              <a-form-item name="name_secondary" label="類型名稱(Portguês)">
                <a-input v-model:value="newGameType.name_secondary"></a-input>
              </a-form-item>
              <a-form-item name="code" label="類型代號">
                <a-input v-model:value="newGameType.code"></a-input>
              </a-form-item>
            </a-form>
          </div>
        </div>
        <div class="text-right">
          <a-button type="primary" @click="saveCompetitionType(newGameType)"
            >保存</a-button
          >
        </div>
      </a-card>
      <template v-for="gameType in gameTypes" :key="gameType.id">
        <a-card :title="gameType.name" :bordered="false">
          <template #extra>
            <a-button v-if="gameType.isEditing" @click="gameType.isEditing = false"
              >Cancel</a-button
            >
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
                <a-form-item name="language" label="Language Secondary">
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
                <p class="underline font-bold">類型基本信息</p>
                <a-form-item name="name" label="類型名稱(繁體中文)">
                  <a-input v-model:value="gameType.name"></a-input>
                </a-form-item>
                <a-form-item name="name_secondary" label="類型名稱(Portguês)">
                  <a-input v-model:value="gameType.name_secondary"></a-input>
                </a-form-item>
                <a-form-item name="code" label="類型代號">
                  <a-input v-model:value="gameType.code"></a-input>
                </a-form-item>
              </a-form>
            </div>
            <div class="flex flex-col gap-3 w-1/2">
              <div>允許的組別及公斤</div>
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
                      確認
                    </a-button>
                    <a-button
                      type="link"
                      @click="gameType.editCategory = category.id"
                      v-else
                    >
                      編輯
                    </a-button>
                    <a-button type="link" danger @click="removeCategory(category)">
                      移除
                    </a-button>
                  </template>
                </div>

                <div class="flex flex-col gap-3">
                  <div
                    class="flex flex-col gap-6"
                    :class="gameType.editCategory == category.id ? 'block' : 'hidden'"
                  >
                    <div class="">
                      <div>組別代號</div>
                      <a-input v-model:value="category.code"></a-input>
                    </div>
                    <div class="">
                      <div>組別名稱</div>
                      <a-input v-model:value="category.name"></a-input>
                    </div>
                    <div class="">
                      <div>組別名稱(外文)</div>
                      <a-input v-model:value="category.name_secondary"></a-input>
                    </div>
                    <div class="">
                      <div>項目時長</div>
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
            </div>
          </div>
          <div v-if="gameType.isEditing">
            <a-button type="primary" danger>刪除</a-button>
            <a-button type="primary" @click="saveCompetitionType(gameType)"
              >保存</a-button
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
  setup(props) {
    console.log(props.gameTypes);
    // const formatTime = (gameCategories) => {
    //   gameCategories.forEach((category) => {
    //     category.duration = dayjs.duration(category.duration, "s").format("mm:ss");
    //   });
    // };
    // props.gameTypes.forEach((type) => {
    //   formatTime(type.categories);
    // });
  },
  created() {},
  methods: {
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
      this.$inertia.post(route("manage.gameTypes.store"), record, {
        onSuccess(res) {},
      });
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
      gameType.removeCategorys.push(category);
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
