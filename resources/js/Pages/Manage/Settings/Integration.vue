<template>
    <a-form layout="vertical" class="max-w-3xl">
            <a-alert
                type="info"
                :message="$t('integration.usage')"
                :description="$t('integration.usage_help')"
                show-icon
            />
        <div class="grid grid-cols-1 gap-4 mt-6 w-full">
            <div>
                <a-form-item :label="$t('integration.secret')" class="form-group">
                    <span class="font-mono">{{ competition.token }}</span>
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
                            v-if="competition.tokens?.length"
                        >
                        <div class="py-2 flex"
                             v-for="device in competition.tokens"
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
    props: {
        competition: {
            type: Object,
            required: true,
        },
    },
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
                        return this.$t('device.type.scoreboard')
                    case 1:
                        return this.$t('device.type.display')
                    case 9:
                        return this.$t('device.type.api_test')
                    default:
                        return this.$t('device.type.unknown')
                }
            }
        }
    },
    methods: {
        copy() {
            try {
                navigator.clipboard.writeText(this.competition.token);
                this.$message.success(this.$t('copied'))
            } catch (e) {
                this.$message.error(this.$t('copy_failed'))
            }
        },
        revoke (uuid) {
            this.$inertia.delete(route('manage.competition.setting.remove-device', [this.competition.id, uuid]), {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    this.$message.success(this.$t('action.remove') + ' ✓')
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
