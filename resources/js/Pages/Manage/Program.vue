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
                <table id="tblTournament" >
                    <tr>
                        <td class="playerBox" >{{player[0]}}</td>
                        <td>wind 1</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="playerBox">{{player[1]}}</td>
                        <td class="topRight win"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="gapHeight"></td>
                        <td class="right win" align="right">r1</td>
                        <td class="">final</td>
                    </tr>
                    <tr>
                        <td class="gapHeight"></td>
                        <td class="right wind" align="right"></td>
                        <td class="top win"></td>
                    </tr>
                    <tr>
                        <td class="playerBox">{{player[2]}}</td>
                        <td class="bottomRight">win2</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="playerBox">{{player[3]}}</td>
                        <td></td>
                        <td></td>
                    </tr>

                </table>

            </div>

    <hr>

            <div class="bg-white">
                <table id="tblTournament">
                    <tr>
                        <td class="playerBox" rowspan="2">{{player[0]}}</td>
                        <td>wind 1</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="topRight win"></td>
                        <td>wind 1/2</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="gapHeight"></td>
                        <td class="right" align="right">r1</td>
                        <td class="topRight"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="playerBox" rowspan="2">{{player[1]}}</td>
                        <td class="bottomRight">win2</td>
                        <td class="right"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="right"></td>
                        <td>wind final</td>
                    </tr>
                    <tr>
                        <td class="gapHeight"></td>
                        <td></td>
                        <td class="right" align="right">r3</td>
                        <td class="top">r4</td>
                    </tr>
                    <tr>
                        <td class="playerBox" rowspan="2">{{player[2]}}</td>
                        <td>wind 3</td>
                        <td class="right"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="topRight win"></td>
                        <td class="right"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="gapHeight"></td>
                        <td class="right win" align="right">r2</td>
                        <td class="bottomRight">wind 2/4</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="playerBox" rowspan="2">{{player[3]}}</td>
                        <td class="bottomRight">wind 4</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <a-table :dataSource="program.bouts" :columns="columns" >
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
        </div>

        
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
    export default {
        components: {
            AdminLayout,
        },
        props: ["program"],
        data() {
            return{
                dateFormat:'YYYY-MM-DD',
                player:["Player 1","Player 2","Player 3","Player 4"],
                modal:{
                    isOpen:false,
                    mode:null,
                    title:'Record Modal',
                    data:{}
                },
                columns:[
                    {
                        title:'In program Sequence',
                        dataIndex:'in_program_sequence'
                    },{
                        title:'Sequence',
                        dataIndex:'sequence'
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

</style>