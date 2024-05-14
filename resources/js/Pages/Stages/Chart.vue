<template>
    <div class="flex w-full h-full justify-center">
        <div class="lhs relative" :class="size > 2 ? 'w-full' : 'w-1/2'">
            <athlete-node direction="left" :node="root.prev_white" class="root"
                          v-bind="nodeProps"
            >
            </athlete-node>
        </div>
        <div class="rhs w-full relative" v-if="size > 2">
            <athlete-node direction="right" :node="root.prev_blue" class="root"
                          v-bind="nodeProps"
            >
            </athlete-node>
        </div>
    </div>
</template>

<script>
import AthleteNode from '@/Components/AthleteNode.vue'
const ANIM_INTERVAL = 3000

export default {
    name: "Chart",
    components: {
        AthleteNode
    },
    provide () {
        return {
            renderer: this.$slots,
            size: this.size
        }
    },
    props: {
        athletes: {
            required: true,
            type: Array
        }
    },
    data () {
        return {
            cursor: 0,
            depth: 0,
            anim: null,
            root: {
                prev_white: {},
                prev_blue: {}
            }
        }
    },
    setup (props) {
        const size = Math.pow(2,
            Math.max((props.athletes.length - 1).toString(2).length, 1))
        return { size }
    },
    mounted () {
        // first, we determine the size of the chart
        // TODO: change athletes.length to chart size
        this.depth =
            Math.max((this.athletes.length - 1).toString(2).length, 2)

        console.debug('depth', this.depth)
        this.root = this.generateGraphDefinition()
    },
    computed: {
        nodeProps () {
            const depth = {
                3: {
                    nodeHeight: 96,
                    nodeGap: 16
                },
                4: {
                    nodeHeight: 68,
                    nodeGap: 8
                },
                5: {
                    nodeHeight: 32,
                    nodeGap: 4
                },
                6: {
                    nodeHeight: 32,
                    nodeGap: 4
                }
            }

            return depth[this.depth] ?? {
                nodeHeight: 96,
                nodeGap: 4,
                lineLength: 64,
                lineWidth: 2,
            }
        }
    },
    methods: {
        generateGraphDefinition () {
            const root = {}
            let nodes = [ root ]

            for (let i = 1; i <= this.depth; ++i) {
                const nextLevelNodes = []

                let seat = 1;
                nodes.forEach(node => {
                    node.prev_white = {}
                    node.prev_blue = {}
                    node.is_bout = true

                    if (i === this.depth) {
                        node.prev_white.seat = seat++
                        node.prev_blue.seat = seat++
                        node.prev_white.is_bout = false
                        node.prev_blue.is_bout = false
                    }
                    nextLevelNodes.push(node.prev_white, node.prev_blue)
                })
                nodes = nextLevelNodes
            }

            return root;
        },
        startAnim () {
            this.anim = setInterval(() => {
                this.stopAnimIfDone()
                this.assignAthlete()
            }, ANIM_INTERVAL)
        },
        assignAthlete () {
            // TODO: assign athlete to its seat
        },
        stopAnimIfDone () {
            if (this.cursor === this.athletes.length) {
                clearInterval(this.anim)
            }
        }
    }
}
</script>

<style lang="less">
.depth-4 > .wrapper {
    @apply flex;

    .depth-5 {
        padding: 0;

        & > .wrapper::after {
            border: none;
        }
    }
}

</style>
