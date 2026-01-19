<template>
  <a-form layout="vertical">
    <a-form-item label="抽籤大屏" class="form-group">
      <div class="grid lg:grid-cols-2 gap-12">
        <a-form-item label="抽籤背景圖片" help="抽籤時的背景圖片">
          <div class="w-full aspect-video mb-3">
            <img class="w-full h-full" :src="draw.background" />
          </div>
          <a-upload
            v-model:file-list="newBackground"
            :multiple="false"
            name="file"
            :action="route('manage.competition.setting.update-draw-background', [competition])"
            :headers="headers"
          >
            <a-button> 更換背景 </a-button>
          </a-upload>
        </a-form-item>

        <a-form-item label="抽籤封面" help="當抽籤大屏空閒時顯示的圖片">
          <div class="w-full aspect-video mb-3">
            <img class="w-full h-full" :src="draw.cover" />
          </div>

          <a-upload
            v-model:file-list="newCover"
            name="file"
            :multiple="false"
            :action="route('manage.competition.setting.update-draw-cover', [competition])"
            :headers="headers"
          >
            <a-button> 更換封面 </a-button>
          </a-upload>
        </a-form-item>
      </div>
    </a-form-item>
  </a-form>
</template>

<script>
import Cookie from 'js-cookie'

export default {
  name: "Draw",
  inject: ["competition"],
  props: {
    draw: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      newCover: null,
      newBackground: null,
    };
  },
  setup() {
    const headers =  {
        'X-XSRF-TOKEN': Cookie.get('XSRF-TOKEN')
    }
    return { headers };
  },
};
</script>

<style scoped></style>
