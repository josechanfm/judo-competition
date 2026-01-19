<template>
    <a-modal
        v-model:visible="modalVisible"
        title="Preview Logo"
        okText="Change Logo"
        @ok="changeLogo"
        :maskClosable="false"
    >
        <div class="mb-2 font-bold">PDF 頁眉</div>
        <div class="flex items-center bg-sky-200 border border-sky-300 rounded-lg font-sans p-1 w-full">
            <div class="w-1/8">
                <img :src="newAvatar" class="h-12 w-auto"/>
            </div>
            <div class="font-bold text-sm text-center w-3/4">
                <div>{{ contest.name }}</div>
                <div>{{ contest.name_secondary }}</div>
            </div>
            <div class="w-1/8">
                <div class="bg-blue-500 rounded text-xs font-bold whitespace-nowrap p-1 text-white text-center">
                    <div>男子 A 組</div>
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
        <img v-if="contest.logo_url" :src="contest.logo_url" alt="avatar"/>
        <div v-else>
            <loading-outlined v-if="loading"></loading-outlined>
            <plus-outlined v-else></plus-outlined>
            <div class="ant-upload-text">Upload</div>
        </div>
    </a-upload>
</template>

<script>
import {LoadingOutlined, PlusOutlined} from "@ant-design/icons-vue";
import {Cropper, CircleStencil, Preview} from 'vue-advanced-cropper';

import 'vue-advanced-cropper/dist/style.css';

export default {
    name: "AvatarCropper",
    components: {
        LoadingOutlined,
        PlusOutlined,
        Cropper,
        Preview,
        CircleStencil
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
    props: {
        contest: {
            type: Object,
            required: true
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
        },
        async changeLogo() {
            // TODO: change the avatar
            // const { canvas } = this.$refs.cropper.getResult()

            const logo = await this.dataUrlToBlob(this.newAvatar)

            this.$inertia.post(
                route('admin.contests.settings.update-logo', {
                    contest: this.contest.id
                }), {
                    _method: 'put',
                    logo: logo
                }, {
                    onSuccess: () => {
                        this.modalVisible = false
                        this.$message.success('Logo changed successfully.')
                    }
                })
        },
        async dataUrlToBlob(dataUrl) {
            const res = await fetch(dataUrl);
            return await res.blob();
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
