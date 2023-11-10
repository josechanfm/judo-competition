<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
        </template>

        Section, Mat sequences
        <a-switch v-model:checked="masterSequence" @change="rebuildBouts" />
        <br>
        <a-button :href="route('manage.competition.program.gen_bouts', program.id)">Create Bouts</a-button>
        <div class="py-12">
            <div class="bg-white">
                <div>
                    <p>Category: {{ program.category_group }}</p>
                    <p>Weight: {{ program.weight_group }}</p>
                    <p>Mat: {{ program.mat }}</p>
                    <p>Section: {{ program.section }}</p>
                    <p>System: {{ program.contest_system }}</p>
                    <p>Size: {{ program.chart_size }}</p>
                </div>
                <a-row :gutter="64">
                    <a-col :span="12">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <a-table :dataSource="program.bouts" :columns="boutColumns">
                                <template #bodyCell="{ column, record }">
                                    {{ record[column.dataIndex] }}
                                </template>
                            </a-table>
                        </div>
                        <hr>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <a-table :dataSource="program.athletes" :columns="athleteColumns">
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'operation'">
                                        <a-button>Edit</a-button>
                                    </template>
                                    <template v-else>
                                        {{ record[column.dataIndex] }}
                                    </template>
                                </template>
                            </a-table>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <component :is="tournamentTable" :contestSystem="program.contest_system" :bouts="bouts" />
                    </a-col>
                </a-row>
            </div>
        </div>


    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Tournament4 from '@/Components/TournamentTable/Elimination4.vue';
import Tournament8 from '@/Components/TournamentTable/Elimination8.vue';
import Tournament16 from '@/Components/TournamentTable/Elimination16.vue';
import Tournament32 from '@/Components/TournamentTable/Elimination32.vue';
import Tournament64 from '@/Components/TournamentTable/Elimination64.vue';

export default {
    components: {
        AdminLayout,
        Tournament4,
        Tournament8,
        Tournament16,
        Tournament32,
        Tournament64,
    },
    props: ["program"],
    data() {
        return {
            masterSequence: false,
            bouts: [],
            tournamentTable: 'Tournament' + this.program.chart_size,
            dateFormat: 'YYYY-MM-DD',
            playersList: [
                {
                    name: "palyer 1",
                    win: [1, 0]
                }, {
                    name: "palyer 2",
                    win: [0, 0]
                }, {
                    name: "palyer 3",
                    win: [1, 1]
                }, {
                    name: "palyer 4",
                    win: [0, 0]
                }
            ],
            modal: {
                isOpen: false,
                mode: null,
                title: 'Record Modal',
                data: {}
            },
            boutColumns: [
                {
                    title: 'In program Sequence',
                    dataIndex: 'in_program_sequence'
                }, {
                    title: 'Sequence',
                    dataIndex: 'sequence'
                }, {
                    title: 'Queue',
                    dataIndex: 'queue'
                }, {
                    title: 'Round',
                    dataIndex: 'round'
                }, {
                    title: 'White',
                    dataIndex: 'white'
                }, {
                    title: 'Blue',
                    dataIndex: 'blue'
                }, {
                    title: 'White from',
                    dataIndex: 'white_rise_from'
                }, {
                    title: 'Blue from',
                    dataIndex: 'blue_rise_from'
                },
            ],
            athleteColumns: [
                {
                    title: 'Name',
                    dataIndex: 'name_zh'
                }, {
                    title: 'Gender',
                    dataIndex: 'gender'
                }, {
                    title: 'Operation',
                    dataIndex: 'operation'
                },
            ],
            rules: {
                country: { required: true },
                name: { required: true },
                date_start: { required: true },
                date_end: { required: true },
            },
            validateMessages: {
                required: "${label} is required!",
                types: {
                    email: "${label} is not a valid email!",
                    number: "${label} is not a valid number!",
                },
                number: {
                    range: "${label} must be between ${min} and ${max}",
                },
            },
        }
    },
    created() {
        this.rebuildBouts();
    },
    methods: {
        rebuildBouts() {
            this.bouts=[];
            this.program.bouts.forEach(b => {
                b.white_name_display = b.white_player.name_display;
                b.blue_name_display = b.blue_player.name_display;
                if (this.masterSequence) {
                    b.circle = b.sequence
                } else {
                    b.circle = b.in_program_sequence
                }
                this.bouts.push(b)
            })
            if (this.program.contest_system == 'kos') {
                this.bouts.splice(this.program.chart_size - 2, 0, '')
                this.bouts.splice(this.program.chart_size - 1, 0, '')
                this.bouts.splice(this.program.chart_size - 4, 0, '')
                this.bouts.splice(this.program.chart_size - 3, 0, '')
            }
        },
    }
}

</script>
