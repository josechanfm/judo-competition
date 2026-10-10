<script setup>
import { computed, ref } from "vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import {
  EyeInvisibleOutlined,
  EyeOutlined,
  LoadingOutlined,
} from "@ant-design/icons-vue";
import { getActiveLanguage, loadLanguageAsync } from "laravel-vue-i18n";

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const showPassword = ref(false);

const passwordFieldType = computed(() =>
  showPassword.value ? "text" : "password"
);

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
  });
};

// 語系切換（慣例同 AdminLayout）：先寫入 session，再載入對應語言包
const currentLocale = ref(getActiveLanguage());
const switchingLang = ref(false);

const changeLang = async (locale) => {
  if (locale === currentLocale.value || switchingLang.value) return;

  switchingLang.value = true;
  try {
    // 先記住選擇，F5 後才能由 app.js 讀回，不受後端 session 是否保存影響
    localStorage.setItem("app-locale", locale);
    await window.axios.get(route("app.locale.update", { locale }));
    await loadLanguageAsync(locale);
    currentLocale.value = locale;
  } finally {
    switchingLang.value = false;
  }
};
</script>

<template>
  <AuthLayout
    :title="$t('welcome_back')"
    :description="$t('welcome_description')"
  >
    <Head :title="$t('login')" />

    <div
      v-if="status"
      class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
    >
      {{ status }}
    </div>

    <form class="space-y-5" @submit.prevent="submit">
      <div>
        <InputLabel for="email" :value="$t('email')" />

        <TextInput
          id="email"
          type="email"
          class="mt-1.5"
          v-model="form.email"
          placeholder="name@example.com"
          required
          autofocus
          autocomplete="username"
        />

        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div>
        <InputLabel for="password" :value="$t('password')" />

        <div class="relative mt-1.5">
          <TextInput
            id="password"
            :type="passwordFieldType"
            class="pr-11"
            v-model="form.password"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />

          <button
            type="button"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-gray-600 focus:outline-none focus-visible:text-indigo-600"
            :aria-label="
              showPassword ? $t('hide_password') : $t('show_password')
            "
            @click="showPassword = !showPassword"
          >
            <EyeInvisibleOutlined v-if="showPassword" />
            <EyeOutlined v-else />
          </button>
        </div>

        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="flex items-center justify-between">
        <label class="flex cursor-pointer items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ml-2 text-sm text-gray-600">{{ $t("remember_me") }}</span>
        </label>

        <!-- 需要「忘記密碼」功能時再取消註解，並補回 Link import：
        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-indigo-600 hover:text-indigo-500">
          {{ $t("forgot_password") }}
        </Link>
        -->
      </div>

      <button
        type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="form.processing"
      >
        <LoadingOutlined v-if="form.processing" class="animate-spin" />
        {{ $t("login") }}
      </button>
    </form>

    <template #footer>
      <div
        class="inline-flex items-center gap-1 rounded-full border border-white/60 bg-white/80 p-1 text-xs font-medium shadow-sm backdrop-blur-sm"
      >
        <button
          type="button"
          class="rounded-full px-3 py-1 transition-colors disabled:cursor-not-allowed"
          :class="
            currentLocale === 'zh_TW'
              ? 'bg-indigo-600 text-white'
              : 'text-gray-600 hover:text-gray-900'
          "
          :disabled="switchingLang"
          @click="changeLang('zh_TW')"
        >
          {{ $t("language.zh_TW") }}
        </button>
        <button
          type="button"
          class="rounded-full px-3 py-1 transition-colors disabled:cursor-not-allowed"
          :class="
            currentLocale === 'en'
              ? 'bg-indigo-600 text-white'
              : 'text-gray-600 hover:text-gray-900'
          "
          :disabled="switchingLang"
          @click="changeLang('en')"
        >
          {{ $t("language.en") }}
        </button>
      </div>
    </template>
  </AuthLayout>
</template>
