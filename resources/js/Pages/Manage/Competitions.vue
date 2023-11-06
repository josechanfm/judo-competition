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
                <a-button @click="createRecord">Create</a-button>
                <a-table :dataSource="competitions" :columns="columns" >
                    <template #bodyCell="{ column, record}">
                        <template v-if="column.dataIndex==='operation'">
                            <a-button @click="onEditRecord(record)">Edit</a-button>
                        </template>
                        <template v-else>
                                {{ record[column.dataIndex] }}
                        </template>
                    </template>
                </a-table>
            </div>
        </div>
        <a-modal v-model:open="modal.isOpen" width="1000px" :footer="null" title="Basic Modal">
            <a-form 
                    name="ModalForm"
                    ref="formRef" 
                    :model="modal.data" 
                    layout="vertical"
                    autocomplete="off"
                    :rules="rules" 
                    :validate-messages="validateMessages"
                >
                <a-row>
                    <a-col :span="8">
                        <a-form-item label="Type" name="competition_type_id">
                            <a-select v-model:value="modal.data.competition_type_id" show-search
                            :options="competitionTypes" :fieldNames="{value:'id',label:'name'}"/>
                        </a-form-item>
                    </a-col>
                    <a-col :span="8">
                        <a-form-item label="Country" name="country">
                            <a-select v-model:value="modal.data.country" show-search
                            :options="countries" :fieldNames="{value:'code',label:'name'}"/>
                        </a-form-item>
                    </a-col>
                    <a-col :span="8">
                        <a-form-item label="scale" name="scale">
                            <a-input v-model:value="modal.data.scale" />
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-form-item label="Title Name (English)" name="name_en">
                    <a-input v-model:value="modal.data.name_en" />
                </a-form-item>
                <a-form-item label="Title Name (Foreign)" name="name_fn">
                    <a-input v-model:value="modal.data.name_fn" />
                </a-form-item>
                <a-form-item label="Start Date" name="date_start">
                    <a-date-picker v-model:value="modal.data.date_start" :format="dateFormat" :valueFormat="dateFormat"/>
                </a-form-item>
                <a-form-item label="End Date" name="date_end">
                    <a-date-picker v-model:value="modal.data.date_end" :format="dateFormat" :valueFormat="dateFormat"/>
                </a-form-item>
                <a-form-item label="Days" name="days">
                    <a-input v-model:value="modal.data.days" />
                </a-form-item>
                <a-form-item label="Remark" name="remark">
                    <a-input v-model:value="modal.data.remark" />
                </a-form-item>
                <a-form-item label="Mat Number" name="mat_number">
                    <a-input v-model:value="modal.data.mat_number" />
                </a-form-item>
                <a-form-item label="Sectin Number" name="section_number">
                    <a-input v-model:value="modal.data.section_number" />
                </a-form-item>
                <a-form-item label="Status" name="status">
                    <a-input v-model:value="modal.data.status" />
                </a-form-item>
                <a-form-item>
                    <a-button type="primary" @click="onSubmit">Create</a-button>
                    <a-button style="margin-left: 10px" @click="resetForm">Reset</a-button>
                    <a-button style="margin-left: 10px" @click="this.modal.isOpen=false">Close</a-button>
                </a-form-item>
            </a-form>
        </a-modal>
        
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
    export default {
        components: {
            AdminLayout,
        },
        props: ["countries","competitionTypes","competitions"],
        data() {
            return{
                dateFormat:'YYYY-MM-DD',
                modal:{
                    isOpen:false,
                    title:'Record Modal',
                    data:{}
                },
                columns:[
                    {
                        title:'Type',
                        dataIndex:'competition_type_id'
                    },{
                        title:'Country',
                        dataIndex:'country'
                    },{
                        title:'Name',
                        dataIndex:'name'
                    },{
                        title:'Date Start',
                        dataIndex:'date_start'
                    },{
                        title:'Date End',
                        dataIndex:'date_end'
                    },{
                        title:'Mat Number',
                        dataIndex:'mat_number'
                    },{
                        title:'Section Number',
                        dataIndex:'section_number'
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
            createRecord(){
                this.modal.title="Create"
                this.modal.isOpen=true
            },
            handleOk(){
                console.log('modal ok');
            },
            onSubmit(){
                console.log('onSubmit');
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
            resetForm(){
                console.log('form Reset');
            },
            onEditRecord(record){
                this.modal.isOpen=true
                this.modal.title="Edit"
                this.modal.data={...record}
            }
        }
    }

</script>
