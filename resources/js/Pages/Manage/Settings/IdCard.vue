<template>
  <a-form layout="vertical" class="max-w-3xl">
    <a-form-item
      :label="$t('id_card_background')"
      :help="$t('id_card_background_help')"
      class="form-group"
    >
      <div class="flex flex-wrap items-center gap-3">
        <a-upload
          v-model:file-list="backgroundFile"
          :multiple="false"
          name="file"
          accept="image/*"
          :action="route('manage.competition.setting.update-id-card-background', [competition.id])"
          :headers="headers"
          :show-upload-list="false"
          @change="onUploadChange"
        >
          <a-button>{{ $t("id_card_upload_background") }}</a-button>
        </a-upload>

        <span class="text-xs text-neutral-500">
          {{
            idCard.customUrl
              ? $t("id_card_custom_background")
              : $t("id_card_using_default_background")
          }}
        </span>
      </div>
    </a-form-item>

    <a-form-item :label="$t('id_card_fields')" :help="$t('id_card_fields_help')">
      <!-- 預覽：拖曳欄位即可調整位置（1 單位 = 1mm） -->
      <div
        ref="preview"
        class="relative w-full overflow-hidden rounded border border-neutral-300 bg-white select-none"
        :style="{
          aspectRatio: `${geometry.pageWidth} / ${geometry.cardHeight}`,
          fontSize: mmToPx + 'px',
        }"
      >
        <img
          class="absolute inset-0 w-full h-full object-fill pointer-events-none"
          :src="idCard.backgroundUrl"
          alt=""
        />

        <div
          v-for="field in previewFields"
          :key="field.key"
          class="absolute flex items-center justify-center text-center leading-none cursor-move whitespace-nowrap"
          :class="[
            field.classes,
            dragging === field.key
              ? 'ring-2 ring-blue-400 bg-blue-50/40'
              : 'hover:ring-1 hover:ring-blue-300',
          ]"
          :style="fieldStyle(field)"
          @mousedown.prevent="startDrag($event, field.key)"
        >
          {{ field.sample }}
        </div>
      </div>

      <!-- 顯示開關、位置、字級與文字顏色 -->
      <div class="flex flex-col gap-y-2 mt-4">
        <div v-for="field in fieldItems" :key="field.key" class="flex flex-wrap items-center gap-2">
          <a-switch
            v-model:checked="fields[field.key].visible"
            size="small"
            :checked-children="$t('id_card_field_visible')"
            :un-checked-children="$t('id_card_field_hidden')"
          />
          <div
            class="w-28 text-sm"
            :class="fields[field.key].visible ? '' : 'text-neutral-400'"
          >
            {{ field.label }}
          </div>
          <a-input-number
            v-model:value="fields[field.key].x"
            :step="0.5"
            :precision="1"
            size="small"
            addon-before="X"
            class="w-28"
          />
          <a-input-number
            v-model:value="fields[field.key].y"
            :step="0.5"
            :precision="1"
            size="small"
            addon-before="Y"
            class="w-28"
          />
          <a-input-number
            v-model:value="fields[field.key].size"
            :step="1"
            size="small"
            addon-after="pt"
            class="w-24"
          />
          <input
            type="color"
            class="h-7 w-9 shrink-0 cursor-pointer rounded border border-neutral-300 bg-white p-0.5 disabled:cursor-not-allowed disabled:opacity-40"
            :value="colorInputValue(field.key)"
            :disabled="isAutoColor(field.key)"
            :title="$t('id_card_field_color')"
            @input="fields[field.key].color = $event.target.value"
          />
          <a-checkbox
            class="text-xs"
            :checked="isAutoColor(field.key)"
            @change="setAutoColor(field.key, $event)"
          >
            {{ $t("id_card_field_color_auto") }}
          </a-checkbox>
        </div>
      </div>

      <div class="flex items-center gap-3 mt-4">
        <a-button type="primary" :loading="saving" @click="save">
          {{ $t("save") }}
        </a-button>
        <a-button :disabled="!isDirty" @click="resetToSaved">
          {{ $t("cancel") }}
        </a-button>
        <a
          class="text-blue-500 ml-2"
          target="_blank"
          :href="route('manage.competition.setting.id-card-preview', [competition.id])"
        >
          {{ $t("id_card_preview_pdf") }}
        </a>
      </div>
    </a-form-item>
  </a-form>
</template>

<script>
import Cookie from "js-cookie";

// 後端沒回傳某些欄位時的備用值（與 Competition::ID_CARD_DEFAULT_FIELD_SETTINGS 一致）
const FALLBACK_FIELDS = {
  name: { x: 10, y: 70, size: 18, visible: true, color: "#000000" },
  name_secondary: { x: 10, y: 80, size: 16, visible: true, color: "#000000" },
  category: { x: 7, y: 94, size: 22, visible: true, color: "auto" },
  team: { x: 6, y: 115, size: 18, visible: true, color: "#000000" },
};

export default {
  name: "IdCard",
  props: {
    competition: {
      type: Object,
      required: true,
    },
    // { settings: { fields }, backgroundUrl, customUrl, geometry: {...}, fields: [...] }
    idCard: {
      type: Object,
      required: true,
    },
  },
  data() {
    const fields = this.normalizeFields(this.idCard.settings?.fields);

    return {
      // 欄位位置（相對卡片左上角，單位 mm）、字級（pt）、是否顯示、文字顏色
      fields,
      savedFields: this.cloneFields(fields),
      backgroundFile: [],
      saving: false,
      dragging: null,
      mmToPx: 1,
      headers: {
        "X-XSRF-TOKEN": Cookie.get("XSRF-TOKEN"),
      },
    };
  },
  computed: {
    geometry() {
      return this.idCard.geometry ?? {};
    },
    // 拖曳時把文字框留在頁面內（輸入數字不受此限，可以自由輸入）
    maxX() {
      return Math.max(
        0,
        this.geometry.pageWidth - this.geometry.leftMargin - this.geometry.fieldWidth
      );
    },
    maxY() {
      return this.geometry.cardHeight;
    },
    // TCPDF Cell() 的最小高度＝字級 × 1.25，預覽要用同一比例才會對齊
    cellHeightRatio() {
      return Number(this.geometry.cellHeightRatio) || 1.25;
    },
    isDirty() {
      return JSON.stringify(this.fields) !== JSON.stringify(this.savedFields);
    },
    // 預覽只畫「顯示」的欄位
    previewFields() {
      return this.fieldItems.filter((field) => this.fields[field.key]?.visible);
    },
    fieldItems() {
      return [
        {
          key: "name",
          label: this.$t("id_card_field_name"),
          sample: "陳大文",
          classes: "font-bold underline",
        },
        {
          key: "name_secondary",
          label: this.$t("id_card_field_name_secondary"),
          sample: "CHAN TAI MAN",
          classes: "underline",
        },
        {
          key: "category",
          label: this.$t("id_card_field_category"),
          sample: "男子A組-60kg",
          classes: "font-bold",
        },
        {
          key: "team",
          label: this.$t("id_card_field_team"),
          sample: "範例學校",
          classes: "font-bold",
        },
      ];
    },
  },
  watch: {
    // 伺服器回傳新設定（例如上傳背景後）時同步
    idCard: {
      handler(value) {
        const fields = this.normalizeFields(value.settings?.fields);

        this.fields = this.cloneFields(fields);
        this.savedFields = this.cloneFields(fields);
      },
      deep: true,
    },
  },
  mounted() {
    this.measure();
    window.addEventListener("resize", this.measure);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.measure);
  },
  methods: {
    cloneFields(fields) {
      return JSON.parse(JSON.stringify(fields ?? {}));
    },
    // 確保每個欄位都有完整設定，不會因為缺 key 而壞掉
    normalizeFields(fields) {
      const source = fields ?? {};
      const result = {};

      Object.keys(FALLBACK_FIELDS).forEach((key) => {
        const fallback = FALLBACK_FIELDS[key];
        const value = source[key] ?? {};

        result[key] = {
          x: Number(value.x ?? fallback.x),
          y: Number(value.y ?? fallback.y),
          size: Number(value.size ?? fallback.size),
          visible: value.visible ?? fallback.visible,
          color: value.color ?? fallback.color,
        };
      });

      return result;
    },
    // 讓預覽 1em = 1mm，欄位字級（pt → mm）就能直接換算
    measure() {
      const preview = this.$refs.preview;

      if (preview) {
        this.mmToPx = preview.clientWidth / this.geometry.pageWidth;
      }
    },
    // auto ＝ 系統自動配色（組別依性別：男藍女紅；其他欄位黑色）。
    // 預覽的範例是男生，所以組別顯示藍色。
    autoColor(key) {
      return key === "category" ? "#0000ff" : "#000000";
    },
    isAutoColor(key) {
      return this.fields[key]?.color === "auto";
    },
    colorInputValue(key) {
      const color = this.fields[key]?.color;

      return color && color !== "auto" ? color : this.autoColor(key);
    },
    setAutoColor(key, event) {
      const checked = event?.target?.checked ?? !!event;

      if (checked) {
        this.fields[key].color = "auto";
      } else if (this.fields[key].color === "auto") {
        // 取消自動時，用自動色當起點，比較好接著調
        this.fields[key].color = this.autoColor(key);
      }
    },
    fieldStyle(field) {
      const position = this.fields[field.key] ?? {};
      const size = Number(position.size) || 12;

      // 預覽容器 1em = 1mm，所以 pt → mm（×0.3528）可直接當 em 用
      const fontSize = size * 0.3528;

      return {
        left: `${
          ((this.geometry.leftMargin + Number(position.x || 0)) / this.geometry.pageWidth) * 100
        }%`,
        top: `${(Number(position.y || 0) / this.geometry.cardHeight) * 100}%`,
        width: `${(this.geometry.fieldWidth / this.geometry.pageWidth) * 100}%`,
        // 和 TCPDF 一樣：文字在「字級 × 1.25」的框裡垂直居中
        height: `${this.cellHeightRatio}em`,
        fontSize: `${fontSize.toFixed(3)}em`,
        color: this.colorInputValue(field.key),
      };
    },
    startDrag(event, key) {
      const scale = this.mmToPx || 1;
      const startPointer = { x: event.clientX, y: event.clientY };
      const origin = {
        x: Number(this.fields[key].x) || 0,
        y: Number(this.fields[key].y) || 0,
      };

      this.dragging = key;

      const onMove = (moveEvent) => {
        const x = origin.x + (moveEvent.clientX - startPointer.x) / scale;
        const y = origin.y + (moveEvent.clientY - startPointer.y) / scale;

        // 一定要展開原本的設定，否則字級、顯示、顏色會被蓋掉
        this.fields[key] = {
          ...this.fields[key],
          x: this.clamp(Math.round(x * 2) / 2, 0, this.maxX),
          y: this.clamp(Math.round(y * 2) / 2, 0, this.maxY),
        };
      };

      const onUp = () => {
        window.removeEventListener("mousemove", onMove);
        window.removeEventListener("mouseup", onUp);
        this.dragging = null;
      };

      window.addEventListener("mousemove", onMove);
      window.addEventListener("mouseup", onUp);
    },
    clamp(value, min, max) {
      return Math.min(Math.max(value, min), max);
    },
    resetToSaved() {
      this.fields = this.cloneFields(this.savedFields);
    },
    save() {
      this.saving = true;

      this.$inertia.post(
        route("manage.competition.setting.update-id-card", {
          competition: this.competition.id,
        }),
        { fields: this.fields },
        {
          preserveScroll: true,
          onSuccess: () => {
            this.fields = this.normalizeFields(this.fields);
            this.savedFields = this.cloneFields(this.fields);
            this.$message.success(this.$t("saved"));
          },
          onError: (errors) => {
            const first = Object.values(errors ?? {})[0];

            this.$message.error(first ?? this.$t("save_failed"));
          },
          onFinish: () => {
            this.saving = false;
          },
        }
      );
    },
    onUploadChange(info) {
      if (info?.file?.status === "done") {
        this.backgroundFile = [];
        this.$inertia.reload({ only: ["idCard"] });
      } else if (info?.file?.status === "error") {
        this.$message.error(this.$t("upload_failed"));
      }
    },
  },
};
</script>

<style scoped></style>
