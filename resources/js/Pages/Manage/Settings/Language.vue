<template>
    <a-form layout="vertical" class="max-w-3xl">
        <div class="flex flex-col gap-4">
            <a-form-item :label="$t('competition_language_settings')" class="form-group">
                <a-form-item :label="$t('competitions.lang_primary')">
                    <a-select class="w-full max-w-[288px]" v-model:value="langForm.language">
                        <a-select-option v-for="lang in languages" :key="lang.value" :value="lang.value">
                            {{ $t('language.' + lang.value) }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item :label="$t('competitions.enable_secondary_lang')">
                    <a-switch v-model:checked="langForm.is_language_secondary_enabled" />
                </a-form-item>

                <a-form-item :label="$t('competitions.lang_secondary')" v-if="langForm.is_language_secondary_enabled">
                    <a-select class="w-full max-w-[288px]" v-model:value="langForm.language_secondary">
                        <a-select-option v-for="lang in languages" :key="lang.value" :value="lang.value">
                            {{ $t('language.' + lang.value) }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item>
                    <a-button @click="saveLang">
                        {{ $t('save') }}
                    </a-button>
                </a-form-item>
            </a-form-item>

            <!-- TODO: next feature is here -->
            <!--                                <a-form-item label="賽事文件語言" class="form-group">-->
            <!--                                    <a-form-item label="主語言">-->
            <!--                                        <a-select class="!w-72"/>-->
            <!--                                    </a-form-item>-->

            <!--                                    <a-form-item label="使用第二語言">-->
            <!--                                        <a-switch />-->
            <!--                                    </a-form-item>-->

            <!--                                    <a-form-item label="第二語言">-->
            <!--                                        <a-select class="!w-72"/>-->
            <!--                                    </a-form-item>-->
            <!--                                </a-form-item>-->
        </div>
    </a-form>
</template>

<script>
import { useForm } from "@inertiajs/vue3";

export default {
    name: "Language",
    props: {
        competition: {
            type: Object,
            required: true,
        },
        languages: {
            type: Array,
            required: true,
        },
    },
    setup (props) {
        // 語言欄位存在 competition_type 上，不是在 competition
        const competitionType = props.competition?.competition_type ?? {};

        const langForm = useForm({
            language: competitionType.language ?? "en",
            language_secondary: competitionType.language_secondary ?? null,
            is_language_secondary_enabled: Boolean(competitionType.is_language_secondary_enabled),
        });

        return { langForm };
    },
    methods: {
        saveLang () {
            this.langForm.submit('post', route('manage.competition.setting.update-language', {
                competition: this.competition.id
            }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.$message.success(this.$t('saved'));
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
