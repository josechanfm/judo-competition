<template>
    <a-form layout="vertical" class="max-w-3xl">
        <div class="flex flex-col gap-4">
            <a-form-item :label="$t('contest_language_settings')" class="form-group">
                <a-form-item :label="$t('contests.lang_primary')">
                    <a-select class="!w-72" v-model:value="langForm.language">
                        <a-select-option v-for="lang in languages" :key="lang" :value="lang">
                            {{ $t('language.' + lang) }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <a-form-item :label="$t('contests.enable_secondary_lang')">
                    <a-switch v-model:checked="langForm.is_language_secondary_enabled" />
                </a-form-item>

                <a-form-item :label="$t('contests.lang_secondary')" v-if="langForm.is_language_secondary_enabled">
                    <a-select class="!w-72" v-model:value="langForm.language_secondary">
                        <a-select-option v-for="lang in languages" :key="lang" :value="lang">
                            {{ $t('language.' + lang) }}
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
import {inject} from "vue";
export default {
    name: "Language",
    props: {
        languages: {
            type: Array,
            required: true
        }
    },
    setup () {
        const contest = inject("contest");

        const langForm = useForm({
                language: contest.language,
                language_secondary: contest.language_secondary,
                is_language_secondary_enabled: contest.is_language_secondary_enabled,
        });
        return {
            contest,
            langForm
        }
    },
    methods: {
        saveLang () {
            this.langForm.submit('post', route('admin.contests.settings.update-language', {
                contest: this.contest.id
            }), {
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
