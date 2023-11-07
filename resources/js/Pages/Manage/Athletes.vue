<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
            
        </template>
        <div class="py-12">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <a-row :gutter="16">
                    <a-col>
                        <a-card>
                            <a-statistic title="Status" value="Ready to start"/>
                        </a-card>
                    </a-col>
                    <a-col>
                        <a-card>
                            <a-statistic title="Date" :value="competition.date_start+' ~ '+competition.date_start"/>
                        </a-card>
                    </a-col>
                    <a-col>
                        <a-card>
                            <a-statistic title="Programs" value="number of programs"/>
                        </a-card>
                    </a-col>
                    <a-col>
                        <a-card>
                            <a-statistic title="Atheles" value="number of altheles"/>
                        </a-card>
                    </a-col>
                </a-row>
                <p></p>
                <a-table :dataSource="competition.athletes" :columns="columns" >
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
        props: ["competition"],
        data() {
            return{
                dateFormat:'YYYY-MM-DD',
                modal:{
                        isOpen:false,
                    mode:null,
                    title:'Record Modal',
                    data:{}
                },
                columns:[
                    {
                        title:'Name zh',
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
