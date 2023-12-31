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
                <a-button @click="onCreateRecord">Create</a-button>
                <a-table :dataSource="competitions" :columns="columns" >
                    <template #bodyCell="{ column, record}">
                        <template v-if="column.dataIndex==='operation'">
                            <a-button @click="onEditRecord(record)">Edit</a-button>
                            <a-button :href="route('manage.competition.athletes.index',record.id)">Athletes</a-button>
                            <a-button :href="route('manage.competition.programs.index',record.id)">Manage</a-button>
                            <a-button :href="route('manage.competition.progress',record.id)">Progress</a-button>
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
                <a-form-item label="Title Name (English)" name="name">
                    <a-input v-model:value="modal.data.name" />
                </a-form-item>
                <a-form-item label="Title Name (Foreign)" name="name_secondary">
                    <a-input v-model:value="modal.data.name_secondary" />
                </a-form-item>
                <a-row>
                    <a-col :span="6">
                        <a-form-item label="Start Date" name="date_start">
                            <a-date-picker v-model:value="modal.data.date_start" :format="dateFormat" :valueFormat="dateFormat"/>
                        </a-form-item>
                    </a-col>
                    <a-col :span="6">
                        <a-form-item label="End Date" name="date_end">
                            <a-date-picker v-model:value="modal.data.date_end" :format="dateFormat" :valueFormat="dateFormat"/>
                        </a-form-item>
                    </a-col>
                    <a-col :span="6">
                        <a-form-item label="Mat Number" name="mat_number">
                            <a-input v-model:value="modal.data.mat_number" style="width:150px"/>
                        </a-form-item>
                    </a-col>
                    <a-col :span="6">
                        <a-form-item label="Sectin Number" name="section_number">
                            <a-input v-model:value="modal.data.section_number" style="width:150px"/>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-form-item label="Days" name="days">
                    <a-input v-model:value="modal.data.days" />
                </a-form-item>
                <a-form-item label="Remark" name="remark">
                    <a-textarea v-model:value="modal.data.remark" :rows="5"/>
                </a-form-item>
                <a-form-item label="Status" name="status">
                    <a-switch v-model:checked="modal.data.status" unCheckedValue="0" checkedValue="1"/>
                </a-form-item>
                <a-form-item>
                    <a-button v-if="modal.mode=='CREATE'" type="primary" @click="onCreate">Create</a-button>
                    <a-button v-if="modal.mode=='EDIT'" type="primary" @click="onUpdate">Update</a-button>
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
                    mode:null,
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
                        title:'Status',
                        dataIndex:'status'
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
