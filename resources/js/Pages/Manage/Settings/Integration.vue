<template>
    <a-form layout="vertical" class="max-w-3xl">
            <a-alert
                type="info"
                message="使用記分牌以及其他設備"
                description="您可以將記分牌等設備與賽事系統相連接，以便自動更新比賽分數和比賽結果。"
                show-icon
            />
        <div class="grid grid-cols-1 gap-4 mt-6 w-full">
            <div>
                <a-form-item :label="$t('integration.secret')" class="form-group">
                    <span class="font-mono">{{ contest.token }}</span>
                    <a-button type="link" @click="copy">
                        <template #icon>
                            <CopyOutlined/>
                        </template>
                    </a-button>

                    <template #help>
                        {{ $t('integration.secret_help') }}
                    </template>
                </a-form-item>
            </div>
            <div>
                <a-form-item :label="$t('device_list')" class="form-group">
                    <div>
                        <template
                            v-if="contest.tokens?.length"
                        >
                        <div class="py-2 flex"
                             v-for="device in contest.tokens"
                             :key="device.id"
                        >
                            <div class="py-2 mr-4">
                                <one-to-one-outlined class="device-type" v-if="deviceType(device) === 0"/>
                                <desktop-outlined class="device-type" v-else-if="deviceType(device) === 1"/>
                                <api-outlined class="device-type" v-else-if="deviceType(device) === 9"/>
                                <warning-outlined class="device-type" v-else />
                            </div>
                            <div class="flex-1">
                                <div class="font-bold">{{ deviceTypeName(device) }}</div>
                                <div class="font-mono">{{ device.name }}</div>
                                <div>
                                    <template v-if="device.last_used_at === null">
                                        <span class="text-sm text-neutral-500">{{ $t('unused') }}</span>
                                    </template>
                                    <a-badge status="success" :text="$t('online')" v-else-if="dayjs().diff(device.last_used_at, 'm') < 5"/>
                                    <template v-else>
                                        <span class="text-sm text-neutral-500">{{ dayjs(device.last_used_at).fromNow() }}</span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex items-center">
<!--                                <a-button type="link">查看日誌</a-button>-->
                                <a-button type="link" danger @click="revoke(device.name)">{{ $t('remove') }}</a-button>
                            </div>
                        </div>
                        </template>
                        <div v-else class="p-4">
                        <a-empty>
                            <template #description>
                                {{ $t('no_device') }}
                            </template>
                        </a-empty>
                        </div>
                    </div>

                </a-form-item>
            </div>
        </div>
    </a-form>
</template>

<script>
import {
    CopyOutlined,
    OneToOneOutlined,
    DesktopOutlined,
    ApiOutlined,
    WarningOutlined,
} from '@ant-design/icons-vue';

import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime.js'
dayjs.extend(relativeTime)


export default {
    name: "Integration",
    inject: ['contest'],
    components: {
        CopyOutlined,
        OneToOneOutlined,
        DesktopOutlined,
        ApiOutlined,
        WarningOutlined,
    },
    setup () {
        return { dayjs }
    },
    computed: {
        deviceType () {
            return device => {
                const name = device.name

                if (name.charAt(1) !== ':') {
                    return -1;
                }

                return parseInt(name.charAt(0));
            }
        },
        deviceTypeName () {
            return device => {
                const type = this.deviceType(device)

                switch (type) {
                    case 0:
                        return '記分牌'
                    case 1:
                        return '場次顯示屏'
                    case 9:
                        return 'API 測試'
                    default:
                        return '未知'
                }
            }
        }
    },
    methods: {
        copy() {
            try {
                navigator.clipboard.writeText(this.contest.token);
                this.$message.success('已複製')
            } catch (e) {
                this.$message.error('複製失敗')
            }
        },
        revoke (uuid) {
            this.$inertia.delete(route('admin.contests.settings.remove-device', [this.contest.id, uuid]), {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    this.$message.success('移除成功')
                }
            })
        }
    }
}
</script>

<style scoped>
.device-type {
    @apply text-blue-500;
    @apply text-3xl;
}
</style>
