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
      <div class="flex items-center justify-center">
        <inertia-link :href="route('manage.competitions.index')" v-if="!collapsed">
          <div
            class="whitespace-nowrap text-white w-full font-medium text-lg flex items-center justify-center"
            style="height: 64px"
          >
            <judoka-logo class="inline-block h-6" />
          </div>
        </inertia-link>
      </div>
      <a-menu v-model:selectedKeys="selectedKeys" theme="light" mode="inline">
        <a-menu-item key="competition.programs.index">
          <inertia-link
            class="flex items-center"
            :href="route('manage.competition.programs.index', competition.id)"
          >
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <appstore-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("menu.competitions.programs") }}</div>
            </div>
          </inertia-link>
        </a-menu-item>
        <a-sub-menu key="submenu1">
          <template #icon>
            <user-outlined />
          </template>
          <template #title>{{ $t("menu.competitions.athletes") }}</template>
          <a-menu-item key="competition.athletes.index">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.index', competition.id)"
            >
              {{ $t("menu.competitions.athletes_list") }}
            </inertia-link>
          </a-menu-item>
          <a-menu-item key="competition.athletes.drawControl">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.drawControl', competition.id)"
            >
              {{ $t("menu.competitions.athletes_draw") }}
            </inertia-link>
          </a-menu-item>
          <a-menu-item key="competition.athletes.weights">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.weights', competition.id)"
            >
              {{ $t("menu.competitions.athletes_weight_in") }}
            </inertia-link>
          </a-menu-item>
        </a-sub-menu>
        <a-menu-item key="competition.progress">
          <inertia-link
            class="flex items-center"
            :href="route('manage.competition.progress', competition.id)"
          >
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <line-chart-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("action.progress") }}</div>
            </div>
          </inertia-link>
        </a-menu-item>
        <a-menu-item key="competition.referees.index">
          <inertia-link
            class="flex items-center gap-2"
            :href="route('manage.competition.referees.index', competition.id)"
          >
            <div class="pb-1"><flag-outlined /></div>
            <div v-if="!collapsed">{{ $t("menu.competitions.referees") }}</div>
          </inertia-link>
        </a-menu-item>
        <a-menu-item key="competition.teams.index">
          <inertia-link
            class="flex items-center"
            :href="route('manage.competition.teams.index', competition.id)"
          >
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <team-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("menu.competitions.team") }}</div>
            </div>
          </inertia-link>
        </a-menu-item>
        <a-menu-item key="competition.setting.index">
          <inertia-link
            class="flex items-center"
            :href="route('manage.competition.setting.index', competition.id)"
          >
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <setting-outlined />
              </div>
              <div v-if="!collapsed">{{ $t("menu.competitions.settings") }}</div>
            </div>
          </inertia-link>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0">
        <div class="flex justify-between items-center">
          <div class="flex items-center">
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
            <span>
              {{ competition.name }}
            </span>
          </div>
          <div class="flex items-center gap-12 pr-4">
            <a-dropdown placement="bottomRight" :trigger="['click']">
              <button class="text-xl flex items-center">
                <GlobalOutlined />
              </button>
              <template #overlay>
                <a-menu>
                  <a-menu-item key="en" @click="changeLang('en')">
                    {{ $t("language.en") }}
                  </a-menu-item>
                  <a-menu-item key="zh_TW" @click="changeLang('zh_TW')">
                    {{ $t("language.zh_TW") }}
                  </a-menu-item>
                </a-menu>
              </template>
            </a-dropdown>
            <button class="text-xl flex justify-center" @click="logout">
              <LogoutOutlined />
            </button>
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
  AppstoreOutlined,
  LineChartOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  SettingOutlined,
  TeamOutlined,
  UserOutlined,
  FlagOutlined,
  GlobalOutlined,
  LogoutOutlined,
} from "@ant-design/icons-vue";
import JudokaLogo from "@/Svgs/judoka-logo.svg";

export default {
  props: ["competition"],
  components: {
    AppstoreOutlined,
    LineChartOutlined,
    MenuFoldOutlined,
    MenuUnfoldOutlined,
    SettingOutlined,
    TeamOutlined,
    UserOutlined,
    FlagOutlined,
    GlobalOutlined,
    LogoutOutlined,
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
      // 先記住選擇，F5 後才能由 app.js 讀回，不受後端 session 是否保存影響
      localStorage.setItem("app-locale", locale);
      await window.axios.get(route("app.locale.update", { locale: locale }));
      await loadLanguageAsync(locale);
      console.log(getActiveLanguage());
    },
    logout() {
      window.location.href = route("logout");
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
