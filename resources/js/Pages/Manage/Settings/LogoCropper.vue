<template>
    <a-modal
        v-model:open="modalVisible"
        :title="$t('logo_preview')"
        :ok-text="$t('logo_change')"
        :cancel-text="$t('action.cancel')"
        @ok="changeLogo"
        :mask-closable="false"
    >
        <div class="mb-2 font-bold">{{ $t('logo_pdf_header') }}</div>
        <div class="flex items-center bg-sky-200 border border-sky-300 rounded-lg font-sans p-1 w-full">
            <div class="w-1/8">
                <img :src="newAvatar" class="h-12 w-auto"/>
            </div>
            <div class="font-bold text-sm text-center w-3/4">
                <div>{{ competition.name }}</div>
                <div>{{ competition.name_secondary }}</div>
            </div>
            <div class="w-1/8">
                <div class="bg-blue-500 rounded text-xs font-bold whitespace-nowrap p-1 text-white text-center">
                    <div>{{ $t('logo_sample_group') }}</div>
                    <div class="text-base">-55KG</div>
                </div>
            </div>
        </div>
<!--        <div class="flex gap-6">-->
<!--            <div>-->
<!--                <cropper-->
<!--                    class="cropper"-->
<!--                    ref="cropper"-->
<!--                    background-class="cropper-bg"-->
<!--                    :canvas="{-->
<!--                        width: 256,-->
<!--                        height: 256,-->
<!--                    }"-->
<!--                    :src="newAvatar"-->
<!--                    @change="onChange"-->
<!--                    auto-zoom-->
<!--                />-->
<!--            </div>-->
<!--        </div>-->

    </a-modal>

    <a-upload
        v-model:file-list="avatar"
        name="avatar"
        list-type="picture-card"
        class="avatar-uploader"
        :show-upload-list="false"
        accept="image/png, image/jpeg"
        :before-upload="beforeUpload"
    >
        <img v-if="logoUrl" :src="logoUrl" alt="avatar"/>
        <div v-else>
            <loading-outlined v-if="loading"></loading-outlined>
            <plus-outlined v-else></plus-outlined>
            <div class="ant-upload-text">{{ $t('upload') }}</div>
        </div>
    </a-upload>
</template>

<script>
import {LoadingOutlined, PlusOutlined} from "@ant-design/icons-vue";
import {Cropper, CircleStencil, Preview} from 'vue-advanced-cropper';

import 'vue-advanced-cropper/dist/style.css';

export default {
    name: "LogoCropper",
    components: {
        LoadingOutlined,
        PlusOutlined,
        Cropper,
        Preview,
        CircleStencil
    },
    // 由 Index.vue -> Info.vue 傳入（不用 inject，因為 Inertia 局部重載不會更新 provide 的值）
    props: {
        competition: {
            type: Object,
            required: true
        },
        logoUrl: {
            type: String,
            default: ""
        }
    },
    data() {
        return {
            avatar: [],
            modalVisible: false,
            loading: false,
            newAvatar: null,
            result: {
                coordinates: null,
                image: null
            }
        }
    },
    methods: {
        onChange({coordinates, image}) {
            this.result = {
                coordinates,
                image
            };
        },
        blobToData(file) {
            return new Promise((resolve) => {
                const reader = new FileReader()
                reader.onloadend = () => resolve(reader.result)
                reader.readAsDataURL(file)
            })
        },
        async beforeUpload(file) {
            this.loading = true
            this.newAvatar = await this.blobToData(file)
            this.modalVisible = true
            this.loading = false
            // 回傳 false 阻止 a-upload 自動上傳，改由 changeLogo() 透過 Inertia 送出
            return false
        },
        async changeLogo() {
            const logo = await this.dataUrlToPngBlob(this.newAvatar)

            // 檔案上傳必須用 FormData 包裝；以 _method 模擬 PUT，對應 Route::put('/logo')
            const formData = new FormData();
            formData.append('_method', 'put');
            formData.append('logo', logo, 'logo.png');

            this.$inertia.post(
                route('manage.competition.setting.update-logo', {
                    competition: this.competition.id
                }),
                formData,
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.modalVisible = false
                        this.newAvatar = null
                        this.avatar = []
                        this.$message.success(this.$t('logo_changed'))
                        // 重載頁面資料，讓新 LOGO 立刻顯示
                        this.$inertia.reload({ only: ['logoUrl'] })
                    }
                }
            )
        },
        // 後端驗證只接受 png，所以一律轉成 PNG 後再上傳
        async dataUrlToPngBlob(dataUrl) {
            const image = await new Promise((resolve, reject) => {
                const img = new Image()
                img.onload = () => resolve(img)
                img.onerror = reject
                img.src = dataUrl
            })

            const canvas = document.createElement('canvas')
            canvas.width = image.naturalWidth
            canvas.height = image.naturalHeight
            canvas.getContext('2d').drawImage(image, 0, 0)

            return await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'))
        }
    }
}
</script>

<style>
.cropper {
    @apply w-48;
    @apply h-48;
    @apply md:w-80;
    @apply md:h-80;
}

.cropper-bg {
    background-color: white;
    background-image:
        linear-gradient(45deg, #ccc 25%, transparent 25%),
        linear-gradient(135deg, #ccc 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #ccc 75%),
        linear-gradient(135deg, transparent 75%, #ccc 75%);
    background-size:25px 25px; /* Must be a square */
    background-position:0 0, 12.5px 0, 12.5px -12.5px, 0px 12.5px; /* Must be half of one side of the square */
}
</style>
