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
        <a-menu-item key="competition.programs.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <user-outlined />
              </div>
              <div v-if="!collapsed">Program</div>
            </div>
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.programs.index', competition.id)"
            >
            </inertia-link>
          </div>
        </a-menu-item>
        <a-sub-menu key="submenu1">
          <template #icon>
            <video-camera-outlined />
          </template>
          <template #title>Athletes</template>
          <a-menu-item key="competition.athletes.index">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.index', competition.id)"
            >
              Athletes List
            </inertia-link>
          </a-menu-item>
          <a-menu-item key="competition.athletes.drawControl">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.drawControl', competition.id)"
            >
              Athletes Draw
            </inertia-link>
          </a-menu-item>
          <a-menu-item key="competition.athletes.weights">
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.athletes.weights', competition.id)"
            >
              Athletes Weight
            </inertia-link>
          </a-menu-item>
        </a-sub-menu>
        <a-menu-item key="competition.progress">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <upload-outlined />
              </div>
              <div v-if="!collapsed">Progress</div>
            </div>
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.progress', competition.id)"
            >
            </inertia-link>
          </div>
        </a-menu-item>
        <a-menu-item key="competition.referees.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1"><flag-outlined /></div>
              <div v-if="!collapsed">Referees</div>
              <inertia-link
                class="mx-2"
                :href="route('manage.competition.referees.index', competition.id)"
              >
              </inertia-link>
            </div>
          </div>
        </a-menu-item>
        <a-menu-item key="competition.teams.index">
          <div class="flex items-center">
            <div class="flex items-center gap-2">
              <div class="pb-1">
                <team-outlined />
              </div>
              <div v-if="!collapsed">Teams</div>
            </div>
            <inertia-link
              class="mx-2"
              :href="route('manage.competition.teams.index', competition.id)"
            >
            </inertia-link>
          </div>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0">
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
import {
  MenuOutlined,
  UserOutlined,
  FlagOutlined,
  VideoCameraOutlined,
  UploadOutlined,
  MenuUnfoldOutlined,
  MenuFoldOutlined,
  TeamOutlined,
} from "@ant-design/icons-vue";
import JudokaLogo from "@/Svgs/judoka-logo.svg";

export default {
  props: ["competition"],
  components: {
    MenuOutlined,
    UserOutlined,
    FlagOutlined,
    VideoCameraOutlined,
    UploadOutlined,
    MenuUnfoldOutlined,
    MenuFoldOutlined,
    TeamOutlined,
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
    console.log(route().current().split(".").slice(1).join("."));
    this.selectedKeys.push(route().current().split(".").slice(1).join("."));
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
