<template>
    <a-form layout="vertical">

        <div class="grid md:grid-cols-2 gap-12">
            <a-form-item class="form-group" :label="$t('competitions.basic')">
                <a-form-item>
                    <template #label>
                        {{ $t('competitions.name') }}
                        <span v-if="primaryLanguageLabel" class="text-xs text-neutral-500">
                            ({{ primaryLanguageLabel }})
                        </span>
                    </template>
                    <a-input :value="competition.name" disabled/>
                </a-form-item>

                <a-form-item
                    v-if="competitionType?.is_language_secondary_enabled"
                >
                    <template #label>
                        {{ $t('competitions.name') }}
                        <span v-if="secondaryLanguageLabel" class="text-xs text-neutral-500">
                            ({{ secondaryLanguageLabel }})
                        </span>
                    </template>
                    <a-input :value="competition.name_secondary" disabled/>
                </a-form-item>

                <a-form-item :label="$t('competitions.type')">
                    <a-tag v-if="competitionType">
                        {{ competitionType.code }} - {{ competitionType.name }}
                    </a-tag>

                    <a-tag v-else>
                        {{ $t('competitions.no_type') }}
                    </a-tag>
                </a-form-item>

                <a-form-item :label="$t('competition_logo')">
                    <logo-cropper
                        :competition="competition"
                        :logo-url="logoUrl"
                    />
                </a-form-item>
            </a-form-item>

            <a-form-item
                class="form-group"
                :label="$t('competitions.categories_and_weight_groups')"
            >
                <a-empty
                    v-if="!categories.length"
                    :description="$t('no_category')"
                />

                <a-form-item
                    v-for="category in categories"
                    :key="category.id"
                >
                    <template #label>
                        <div>
                            <a-tag>{{ category.code }}</a-tag>
                            {{
                                [category.name, category.name_secondary]
                                    .filter(Boolean)
                                    .join(" / ")
                            }}
                        </div>
                    </template>

                    <div class="flex flex-wrap gap-1">
                        <a-tag v-for="weight in category.weights" :key="weight">
                            {{ weight }}
                        </a-tag>
                    </div>
                </a-form-item>
            </a-form-item>
        </div>
    </a-form>
</template>

<script>
import LogoCropper from "./LogoCropper.vue";

export default {
    name: "Info",
    components: {
        LogoCropper,
    },
    props: {
        competition: {
            type: Object,
            required: true,
        },
        logoUrl: {
            type: String,
            default: "",
        },
    },
    computed: {
        // 語言欄位實際存在 competition_type 上（competitions 沒有這些欄位）
        competitionType() {
            return this.competition?.competition_type ?? null;
        },
        primaryLanguageLabel() {
            return this.competitionType?.language
                ? this.$t("language." + this.competitionType.language)
                : "";
        },
        secondaryLanguageLabel() {
            return this.competitionType?.language_secondary
                ? this.$t("language." + this.competitionType.language_secondary)
                : "";
        },
        // 賽事組別（含公斤級）
        categories() {
            return this.competition?.categories ?? [];
        },
    },
};
</script>

<style scoped>
</style>
