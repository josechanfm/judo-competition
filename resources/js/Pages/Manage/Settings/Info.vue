<template>
    <a-form layout="vertical">

        <div class="grid md:grid-cols-2 gap-12">
            <a-form-item class="form-group" label="賽事基本資料">
                <a-form-item v-bind="form.validation.name">
                    <template #label>
                        {{ $t('contests.name') }}
                        <span class="text-xs text-neutral-500">
                            ({{ $t('language.' + contest.language) }})
                        </span>
                    </template>
                    <a-input v-model:value="form.name"/>
                </a-form-item>

                <a-form-item v-bind="form.validation.name_secondary"
                             v-if="contest.is_language_secondary_enabled"
                >
                    <template #label>
                        {{ $t('contests.name') }}
                        <span class="text-xs text-neutral-500">
                            ({{ $t('language.' + contest.language_secondary) }})
                        </span>
                    </template>
                    <a-input v-model:value="form.name_secondary"/>
                </a-form-item>

                <a-form-item label="類型代號" v-bind="form.validation.code">
                    <a-tag v-if="contest.from_type">
                        {{ contest.from_type.code }} -
                        {{ contest.from_type.name }}
                    </a-tag>

                    <a-tag v-else>
                        已刪除的類型
                    </a-tag>
                </a-form-item>

                <a-form-item label="賽事LOGO">
                    <logo-cropper :contest="contest"/>
                </a-form-item>
            </a-form-item>

            <a-form-item v-bind="form.validation.categories" label="組別及公斤級">
                <a-form-item
                    v-for="(category, i) in form.categories"
                    v-bind="form.validation[`categories.${i}.weights`]"
                    :key="category.id"
                >
                    <template #label>
                        <div>
                            <a-tag>{{ category.code }}</a-tag>
                            {{
                                [category.name, category.name_secondary].join(
                                    " / "
                                )
                            }}
                        </div>

                        <template v-if="editable">

                            <a-button
                                type="link"
                                @click="currentEditingCategory = null"
                                v-if="isCurrentEditingCategory(category)"
                            >
                                確定
                            </a-button>
                            <a-button
                                type="link"
                                @click="currentEditingCategory = category.id"
                                v-else
                            >
                                編輯組別
                            </a-button>
                            <a-button type="link" danger @click="removeCategory(category)">
                                移除組別
                            </a-button>

                        </template>
                    </template>

                    <template v-if="isCurrentEditingCategory(category)">
                        <div class="border rounded-md p-3">
                            <a-form layout="vertical">
                                <a-form-item
                                    label="字母代號"
                                    v-bind="form.validation[`categories.${i}.code`]"
                                >
                                    <a-input
                                        v-model:value="category.code"
                                        placeholder="字母代號"
                                    />
                                </a-form-item>

                                <a-form-item
                                    label="中文名稱"
                                    v-bind="form.validation[`categories.${i}.name`]"
                                >
                                    <a-input
                                        v-model:value="category.name"
                                        placeholder="中文名稱"
                                    />
                                </a-form-item>

                                <a-form-item
                                    label="葡文名稱"
                                    v-bind="form.validation[`categories.${i}.name_secondary`]"
                                >
                                    <a-input
                                        v-model:value="category.name_secondary"
                                        placeholder="葡文名稱"
                                    />
                                </a-form-item>

                                <a-form-item
                                    label="項目時長"
                                    v-bind="form.validation[`categories.${i}.name_en`]"
                                >
                                    <!-- Not implemented -->
                                    <a-time-picker
                                        v-model:value="category.duration"
                                        format="mm:ss"
                                    />
                                </a-form-item>
                            </a-form>
                        </div>
                    </template>
                    <template v-else>
                        <a-form-item>
                            <dynamic-tag-group v-model:tags="category.weights" :editable="editable">
                            </dynamic-tag-group>
                        </a-form-item>
                    </template>
                </a-form-item>
                <div class="mt-3 flex gap-3" v-if="editable">
                    <a-button @click="addNewCategory">添加組別</a-button>
                </div>
            </a-form-item>
        </div>

        <a-button @click="save" type="primary" v-if="editable">保存</a-button>
    </a-form>
</template>

<script>
import LogoCropper from "./LogoCropper.vue";
import {inject} from "vue";

export default {
    name: "Info",
    components: {
        LogoCropper,
        // DynamicTagGroup,
    },
    setup(props, ctx) {
        const contest = inject("contest");
        const form = useForm({
            name: contest.name,
            name_en: contest.name_en,
            name_secondary: contest.name_secondary,
            type: contest.from_type,
            categories: [...contest.opened_categories],
        });

        return {form, contest, CONTEST_STATUS};
    },
    data() {
        return {
            currentEditingCategory: null,
        };
    },
    computed: {
        isCurrentEditingCategory() {
            return (category) => {
                return this.currentEditingCategory === category.id;
            };
        },
        editable() {
            return false;
        }
    },
    methods: {
        addNewCategory() {
            this.form.categories.push({
                id: Date.now(),
                name: "未命名",
                name_secondary: "Untitled",
                name_en: "Untitled",
                code: "U",
                weights: [],
            });
        },
        save() {
            // handle 保存的時候
            this.form.submit("post", route("admin.settings.contest-types.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.$message.success("保存成功");
                    this.cancelEdit();
                },
            });
        },
        removeCategory(category) {
            this.form.categories = this.form.categories.filter(
                (c) => c.id !== category.id
            );
        },
        toggleEdit() {
            this.$emit("edit", this.contestType.id);
        },
        cancelEdit() {
            this.$emit("edit", null);
        },
    },
};
</script>

<style scoped>
</style>
