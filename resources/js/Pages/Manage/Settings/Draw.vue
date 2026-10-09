<template>
  <a-form layout="vertical">
    <a-form-item :label="$t('draw_screen')" class="form-group">
      <div class="grid lg:grid-cols-2 gap-12">
        <a-form-item :label="$t('draw_background')" :help="$t('draw_background_help')">
          <div class="w-full aspect-video mb-3">
            <img class="w-full h-full object-cover" :src="draw.background" :alt="$t('draw_background')" />
          </div>
          <a-upload
            v-model:file-list="newBackground"
            :multiple="false"
            name="file"
            :action="route('manage.competition.setting.update-draw-background', [competition.id])"
            :headers="headers"
            @change="reload"
          >
            <a-button> {{ $t('draw_background_change') }} </a-button>
          </a-upload>
        </a-form-item>

        <a-form-item :label="$t('draw_cover')" :help="$t('draw_cover_help')">
          <div class="w-full aspect-video mb-3">
            <img class="w-full h-full object-cover" :src="draw.cover" :alt="$t('draw_cover')" />
          </div>

          <a-upload
            v-model:file-list="newCover"
            name="file"
            :multiple="false"
            :action="route('manage.competition.setting.update-draw-cover', [competition.id])"
            :headers="headers"
            @change="reload"
          >
            <a-button> {{ $t('draw_cover_change') }} </a-button>
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
  props: {
    competition: {
      type: Object,
      required: true,
    },
    draw: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      newCover: [],
      newBackground: [],
    };
  },
  setup() {
    const headers =  {
        'X-XSRF-TOKEN': Cookie.get('XSRF-TOKEN')
    }
    return { headers };
  },
  methods: {
    // 上傳完成後重載頁面資料，讓新圖立刻顯示
    reload(info) {
      if (info?.file?.status === 'done') {
        this.newBackground = [];
        this.newCover = [];
        this.$inertia.reload({ only: ['draw'] });
      }
    },
  },
};
</script>

<style scoped></style>
