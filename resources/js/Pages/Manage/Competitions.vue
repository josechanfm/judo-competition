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
                            {{ record }}
                        </template>
                        <template v-else>
                                {{ record[column.dataIndex] }}
                        </template>
                    </template>
                </a-table>
            </div>
        </div>
        {{ competitionCategories }}
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
                <a-form-item label="Type" name="category_id">
                    <a-select v-model:value="modal.data.competion_category_id" show-search
                    :options="competitionCategories" :fieldNames="{value:'id',label:'title'}"/>
                </a-form-item>
                <a-form-item label="Title Name (English)" name="name_en">
                    <a-input v-model:value="modal.data.name_en" />
                </a-form-item>
                <a-form-item label="Title Name (Foreign)" name="name_fn">
                    <a-input v-model:value="modal.data.name_fn" />
                </a-form-item>
                <a-form-item label="Start Date" name="start date">
                    <a-input v-model:value="modal.data.start_date" />
                </a-form-item>
                <a-form-item label="End Date" name="end_date">
                    <a-input v-model:value="modal.data.end_date" />
                </a-form-item>
                <a-form-item label="Country" name="country">
                    <a-select v-model:value="modal.data.country" show-search
                    :options="countries" :fieldNames="{value:'code',label:'name'}"/>
                </a-form-item>
                <a-form-item label="scale" name="scale">
                    <a-input v-model:value="modal.data.scale" />
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
import { compileTemplate } from '@vue/compiler-sfc';
    export default {
        components: {
            AdminLayout,
        },
        props: ["countries","competitionCategories","competitions"],
        data() {
            return{
                modal:{
                    isOpen:false,
                    title:'Record Modal',
                    data:{}
                },
                columns:[
                    {
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
                        title:'Operation',
                        dataIndex:'operation'
                    },
                ],
                rules: {
                    country: { required:true },
                    name: { required:true },
                    start_date: { required:true },
                    end_date: { required:true },
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
            }
        }
    }

</script>
