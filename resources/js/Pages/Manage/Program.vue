<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
        </template>
        <a-button :href="route('manage.competition.program.gen_bouts',program.id)">Create Bouts</a-button>
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
                            <a-table :dataSource="program.bouts" :columns="boutColumns" >
                                <template #bodyCell="{ column, record}">
                                    <template v-if="column.dataIndex==='operation'">
                                        <a-button>Edit</a-button>
                                    </template>
                                    <template v-else>
                                            {{ record[column.dataIndex] }}
                                    </template>
                                </template>
                            </a-table>
                        </div>
                <hr>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <a-table :dataSource="program.athletes" :columns="athleteColumns" >
                                <template #bodyCell="{ column, record}">
                                    <template v-if="column.dataIndex==='operation'">
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
                        <component :is="tournamentTable" :players="playersList" />
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
            return{
                tournamentTable:'Tournament'+this.program.chart_size,
                dateFormat:'YYYY-MM-DD',
                playersList:[
                    {
                        name:"palyer 1",
                        win:[1,0]
                    },{
                        name:"palyer 2",
                        win:[0,0]
                    },{
                        name:"palyer 3",
                        win:[1,1]
                    },{
                        name:"palyer 4",
                        win:[0,0]
                    }
                ],
                modal:{
                    isOpen:false,
                    mode:null,
                    title:'Record Modal',
                    data:{}
                },
                boutColumns:[
                    {
                        title:'In program Sequence',
                        dataIndex:'in_program_sequence'
                    },{
                        title:'Sequence',
                        dataIndex:'sequence'
                    },{
                        title:'Queue',
                        dataIndex:'queue'
                    },{
                        title:'Round',
                        dataIndex:'round'
                    },{
                        title:'Operation',
                        dataIndex:'operation'
                    },
                ],
                athleteColumns:[
                    {
                        title:'Name',
                        dataIndex:'name_zh'
                    },{
                        title:'Gender',
                        dataIndex:'gender'
                    },{
                        title:'Operation',
                        dataIndex:'operation'
                    },
                ],
                rules: {
                    country: { required:true },
                    name: { required:true },
                    date_start: { required:true },
                    date_end: { required:true },
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
        },
        methods: {
            onCreateRecord(){
                this.modal.title="Create"
                this.modal.isOpen=true
                this.modal.mode="CREATE"
            },
            onEditRecord(record){
                this.modal.isOpen=true
                this.modal.title="Edit"
                this.modal.mode="EDIT"
                this.modal.data={...record}
            },
            onUpdate(){
                this.$refs.formRef
                    .validateFields()
                    .then(() => {
                        this.$inertia.put(route("manage.competitions.update",this.modal.data.id), this.modal.data, {
                            onSuccess: (page) => {
                            this.modal.data = {};
                            this.modal.isOpen = false;
                            },
                            onError: (err) => {
                            console.log(err);
                            },
                        });

                        console.log('values', this.modal.data, this.modal.data);
                    })
                    .catch(error => {
                        console.log('error', error);
                });
            },
            onCreate(){
                this.$refs.formRef
                    .validateFields()
                    .then(() => {
                        this.$inertia.post(route("manage.competitions.store"), this.modal.data, {
                            onSuccess: (page) => {
                            this.modal.data = {};
                            this.modal.isOpen = false;
                            },
                            onError: (err) => {
                            console.log(err);
                            },
                        });

                        console.log('values', this.modal.data, this.modal.data);
                    })
                    .catch(error => {
                        console.log('error', error);
                });
            },
        }
    }

</script>
<style scoped>
table#tblTournament {
  border-spacing: 0;
}
table#tblTournament td{
  width:80px;
  height:20px;
}
.playerBox{
    width:200px!important;
    border: 1px solid black;
    border-radius: 5px;
}
.topRight{
    border-top: 1px solid black;
    border-right: 1px solid black;
    border-top-right-radius: 5px;
}
.bottomRight{
    border-bottom: 1px solid black;
    border-right: 1px solid black;
    border-bottom-right-radius: 5px;
}
.right{
    border-right: 1px solid black
}
.bottomLeftCorner{
    border-bottom-left-radius: 5px
}
.topLeftCorner{
    border-top-left-radius: 5px
}
.top{
    border-top: 1px solid black;
    
}
.gapHeight{
    height:10px
}
.topRight.win{
    border-top: 3px solid red;
    border-right: 3px solid red;
}
.bottomRight.win{
    border-bottom: 3px solid red;
    border-right: 3px solid red;
}
.right.win{
    border-right: 3px solid red
}
.top.win{
    border-top: 3px solid red
}
span.circle {
        background: #e3e3e3;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        color: #6e6e6e;
        display: inline-block;
        font-weight: bold;
        line-height: 30px;
        width: 30px;
        margin-right: 5px;
        text-align: center;
        position:relative;
        top:20px;
        left:20px;
      }
</style>