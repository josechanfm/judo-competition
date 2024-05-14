<template>
    <div class="node w-full relative" :class="`direction-${direction} ${isLeaf ? 'is-leaf' : ''} depth-${depth}`"
         :style="`--line-length: ${lineLength}px; --line-width: ${lineWidth}px`">
        <div class="w-full wrapper h-full" :class="`direction-${direction}`">
            <athlete-node
                v-if="node.prev_white"
                v-bind="$props"
                :direction="direction" :node="node.prev_white" class="lhs"
                :depth="depth + 1"
            >
            </athlete-node>

            <div
                v-if="node.in_program_sequence"
                class="
                absolute
                right-16
                top-1/2
                -m-3
                rounded-full
                bg-slate-300
                h-6
                w-6
                flex
                items-center
                justify-center
                font-bold
                text-sm
                z-10"
            >
                {{ node.in_program_sequence }}
            </div>

            <athlete-node v-if="node.prev_blue"
                          v-bind="$props"
                          :direction="direction" :node="node.prev_blue"
                          class="rhs"
                          :depth="depth + 1"
            >
            </athlete-node>

            <template v-if="isLeaf">
                <div
                    class="w-full h-full"
                    :style="`height: ${nodeHeight + nodeGap * 2}px; padding: ${nodeGap}px 0;`"
                >
                    <div
                        :id="`seat-${node.seat}`"
                        :class="`section-${size > 2 ? '' : '2-'}${section(node.seat)}`"
                        class="w-full h-full grid"
                    >
                        <slot-proxy :renderer="renderer"
                                    :node="node"
                                    v-if="renderer"
                        />
                        <slot v-else></slot>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import { inject, provide, defineComponent } from 'vue'
export default {
    name: "AthleteNode",
    setup (props, ctx) {
      const renderer = inject('renderer', null)
      const size = inject('size')

      if (renderer === null) {
        provide('renderer', ctx.slots)
      }

      return { renderer, size }
    },
    components: {
        slotProxy: {
            props: {
                renderer: {
                    required: true,
                    type: Object
                },
                node: {
                    required: true,
                    type: Object
                }
            },
            render () {
                return this.renderer.default(this.node)
            }
        }
    },
    props: {
        direction: {
            required: true,
            type: String
        },
        node: {
            required: true,
            type: Object
        },
        depth: {
            required: false,
            type: Number,
            default: 0
        },
        nodeWidth: {
            type: String,
            default: '100%'
        },
        nodeHeight: {
            type: Number,
            default: 68
        },
        nodeGap: {
            type: Number,
            default: 4
        },
        lineLength: {
            type: Number,
            default: 64
        },
        lineWidth: {
            type: Number,
            default: 1
        }
    },
    data () {
        return {
            athlete: null
        }
    },
    computed: {
        isLeaf () {
            if (this.node.is_bout) {
                return false
            }

            return true
        },
        section () {
            return seat => {
                return Math.ceil(seat / (this.size / 4))
            }
        }
    },
    methods: {
        $assignAthlete () {
            // TODO: place athlete
        }
    }
}
</script>

<style scoped lang="less">
.line {
    width: var(--line-length);
    border-top: var(--line-width) solid #000;
    position: absolute;
    top: 50%;
    height: 50%;
    content: '';
}

.node {
    position: relative;

    &.direction-left {
        padding-right: 64px;
        .wrapper::after {
            right: 0;
            border-right: 1px solid #000;

            .line();
        }

        .wrapper::after:last-child {
            border-right: none;
        }
    }

    &.depth-0.direction-left {
        & > .wrapper::after {
            border-right: none;
        }
    }

    &.direction-right {
        padding-left: 64px;
        .wrapper::after {
            left: 0;
            border-left: 1px solid #000;

            .line();
        }
    }
}

.rhs > .wrapper::after {
    top: unset !important;
    border-top: unset !important;
    border-bottom: 1px solid #000;
    bottom: 50%;
}

.root > .wrapper::after {
    border-left: unset !important;
    border-right: unset !important;
}

.is-leaf {
    & > div {
        height: 100%;
    }
}

.section {
    &-1 {
        @apply bg-green-300;
        @apply overflow-clip;
        @apply rounded-lg;
    }

    &-2 {
        @apply rounded-lg;
        @apply bg-blue-300;
        @apply overflow-clip;
    }

    &-3 {
        @apply bg-red-300;
        @apply rounded-lg;
        @apply overflow-clip;
    }

    &-4 {
        @apply bg-yellow-300;
        @apply rounded-lg;
        @apply overflow-clip;
    }

    &-2-2 {
        @apply bg-white;
        @apply rounded-lg;
        @apply overflow-clip;
    }

    &-2-4 {
        @apply bg-blue-500;
        @apply rounded-lg;
        @apply overflow-clip;
    }
}
</style>
