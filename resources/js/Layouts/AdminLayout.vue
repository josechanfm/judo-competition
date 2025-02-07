<template>
  <a-layout style="min-height: 100vh">
    <a-layout-sider
      v-model:collapsed="collapsed"
      :trigger="null"
      collapsible
      theme="light"
      width="250px"
      class="shadow-md"
    >
      <div class="flex items-center">
        <div
          style="height: 64px"
          class="flex items-center justify-center"
          :style="collapsed ? 'width: 80px' : ''"
        >
          <menu-outlined class="trigger" @click="() => (collapsed = !collapsed)" />
        </div>
        <inertia-link :href="route('manage.competitions.index')" v-if="!collapsed">
          <div
            class="whitespace-nowrap text-white w-full font-medium text-lg flex items-center justify-center"
            style="height: 64px"
          >
            <div v-if="!collapsed" class="flex flex-col items-center">
              <judoka-logo class="inline-block h-6" />
            </div>
            <span v-else class="italic font-bold overflow-clip w-9">
              <judoka-logo class="inline-block h-6" />
            </span>
          </div>
        </inertia-link>
      </div>
      <a-menu v-model:selectedKeys="selectedKeys" theme="light" mode="inline">
        <a-menu-item key="competitions.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <user-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("layout.menu.home") }}</div>
            </div>
            <inertia-link class="mx-2" :href="route('manage.competitions.index')">
            </inertia-link>
          </div>
        </a-menu-item>
        <a-menu-item key="gameTypes.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <video-camera-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("layout.menu.competition_type") }}</div>
            </div>
            <inertia-link class="mx-2" :href="route('manage.gameTypes.index')">
            </inertia-link>
          </div>
        </a-menu-item>
        <a-menu-item key="print.demo">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <video-camera-outlined />
              </div>
              <div v-if="!collapsed">Print Out Demo</div>
            </div>
            <inertia-link class="mx-2" :href="route('manage.print.demo')"> </inertia-link>
          </div>
        </a-menu-item>
        <a-menu-item key="system.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <FileTextOutlined />
              </div>
              <div v-if="!collapsed">{{ $t("layout.menu.documentation") }}</div>
            </div>
            <inertia-link class="mx-2" :href="route('manage.system.index')">
            </inertia-link>
          </div>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0">
        <div class="flex justify-between items-center">
          <menu-unfold-outlined
            v-if="collapsed"
            class="trigger"
            @click="() => (collapsed = !collapsed)"
          />
          <menu-fold-outlined
            v-else
            class="trigger"
            @click="() => (collapsed = !collapsed)"
          />
          <div class="flex items-center gap-12 pr-4">
            <div class="group">
              <button class="text-xl flex items-center"><GlobalOutlined /></button>
              <div
                class="absolute grid group-hover:grid-rows-[1fr] grid-rows-[0fr] bg-white z-50 overflow-hidden duration-300 ease-in-out transition-all"
              >
                <div class="min-h-0">
                  <div class="whitespace-nowrap px-4 hover:text-blue-500 h-12">
                    <button @click="changeLang('en')">{{ $t("language.en") }}</button>
                  </div>
                  <div class="whitespace-nowrap px-4 hover:text-blue-500 h-12">
                    <button @click="changeLang('zh_TW')">
                      {{ $t("language.zh_TW") }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <button class="text-xl flex justify-center"><LogoutOutlined /></button>
          </div>
        </div>
      </a-layout-header>
      <a-layout-content>
        <template #header>
          <div>
            <slot name="header" />
          </div>
        </template>
        <div class="mx-2">
          <main>
            <slot />
          </main>
        </div>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>

<script>
import { ref } from "vue";
import { getActiveLanguage, loadLanguageAsync } from "laravel-vue-i18n";
import {
  MenuOutlined,
  UserOutlined,
  VideoCameraOutlined,
  GlobalOutlined,
  UploadOutlined,
  MenuUnfoldOutlined,
  FileTextOutlined,
  LogoutOutlined,
  MenuFoldOutlined,
} from "@ant-design/icons-vue";
import JudokaLogo from "@/Svgs/judoka-logo.svg";

export default {
  components: {
    MenuOutlined,
    UserOutlined,
    VideoCameraOutlined,
    UploadOutlined,
    MenuUnfoldOutlined,
    MenuFoldOutlined,
    FileTextOutlined,
    LogoutOutlined,
    GlobalOutlined,
    JudokaLogo,
  },
  setup() {
    const selectedKeys = ref(["1"]);
    const collapsed = ref(false);
    return {
      selectedKeys,
      collapsed,
    };
  },
  mounted() {
    console.log(getActiveLanguage());
    console.log(route().current().split(".").slice(1).join("."));
    this.selectedKeys.push(route().current().split(".").slice(1).join("."));
  },
  methods: {
    async changeLang(locale) {
      await window.axios.get(route("app.locale.update", { locale: locale }));
      await loadLanguageAsync(locale);
      console.log(getActiveLanguage());
    },
  },
};
</script>

<style>
#components-layout-demo-custom-trigger .trigger {
  font-size: 18px;
  line-height: 64px;
  padding: 0 24px;
  cursor: pointer;
  transition: color 0.3s;
}

#components-layout-demo-custom-trigger .trigger:hover {
  color: #1890ff;
}

#components-layout-demo-custom-trigger .logo {
  height: 32px;
  background: rgba(255, 255, 255, 0.3);
  margin: 16px;
}

.site-layout .site-layout-background {
  background: #fff;
}
#app .trigger {
  font-size: 18px;
  line-height: 64px;
  padding: 0 24px;
  cursor: pointer;
  transition: color 0.3s;
}
</style>
