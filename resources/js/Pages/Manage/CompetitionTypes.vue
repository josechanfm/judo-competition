<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
            
        </template>
        <div class="py-12">
            <template v-for="competitionType in competitionTypes">
                <a-card :title="competitionType.name" :bordered="false" >
                    <template #extra>
                        <a-button v-if="competitionType.isEditing" @click="competitionType.isEditing=false">Cancel</a-button>
                        <a-button v-else @click="competitionType.isEditing=true">Edit</a-button>
                    </template>
                    <a-row>
                        <a-col :span="12">
                <a-form 
                    name="ModalForm"
                    ref="formRef" 
                    :model="modal.data" 
                    layout="vertical"
                    autocomplete="off"
                    :rules="rules" 
                    :validate-messages="validateMessages"
                >

                            {{competitionType.isEditing}}
                            <p>{{competitionType.language}},{{competitionType.language_secondary}}</p>
                            <p><strong><u>類型基本信息</u></strong></p>
                            <p>類型名稱(繁體中文)</p>
                            <p>{{competitionType.name}}</p>
                            <p>類型名稱(Portguês)</p>
                            <p>{{competitionType.name_secondary}}</p>
                            <p>類型代號</p>
                            <p>{{competitionType.code}}</p>
                        </a-col>
                        <a-col :span="12">
                            <div v-for="category in competitionType.categories">
                                <p>{{category.code}} {{category.name}} {{category.name_secondary}}</p>
                                <p v-for="weight in category.weights">{{weight}}</p>
                            </div>
                            <p>Card content</p>
                        </a-col>
                    </a-row>
                    <div v-if="competitionType.isEditing">
                        <a-button type="primary" danger>刪除</a-button>
                        <a-button type="primary">保存</a-button>
                    </div>
                </a-card>
            </template>
        </div>

        
    </AdminLayout>
</template>

<script>
    import AdminLayout from '@/Layouts/AdminLayout.vue';
import { compileTemplate } from '@vue/compiler-sfc';
    export default {
        components: {
            AdminLayout,
        },
        props: ["competitionTypes","competitionCategories"],
        data() {
            return{
                dateFormat:'YYYY-MM-DD',
                isEditing:false,
            }
        },
        created() {
        },
        methods: {
        }
    }

</script>
