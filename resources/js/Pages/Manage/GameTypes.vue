<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
            
        </template>
        <div class="py-12">
            <template v-for="gameType in gameTypes">
                <a-card :title="gameType.name" :bordered="false" >
                    <template #extra>
                        <a-button v-if="gameType.isEditing" @click="gameType.isEditing=false">Cancel</a-button>
                        <a-button v-else @click="gameType.isEditing=true">Edit</a-button>
                    </template>
                    <a-row>
                        <a-col :span="12">
                            <a-form 
                                id="modalFrom"
                                name="ModalForm"
                                ref="formRef" 
                                :model="gameType" 
                                layout="vertical"
                                autocomplete="off"
                                :disabled="!gameType.isEditing"
                            >
                                <a-form-item name="language" label="Language">
                                    <a-select v-if="gameType.isEditing" v-model:value="gameType.language" :options="languages" style="width:200px"></a-select>
                                    <p v-else>{{ languages.find(l=>l.value==gameType.language).label }}</p>
                                </a-form-item>
                                <a-form-item name="language" label="Language Secondary">
                                    <a-select v-if="gameType.isEditing" v-model:value="gameType.language_secondary" :options="languages" style="width:200px"></a-select>
                                    <p v-else>
                                        <span v-if="languages.find(l=>l.value==gameType.language_secondary)">
                                            {{ languages.find(l=>l.value==gameType.language_secondary).label }}
                                        </span>
                                    </p>
                                </a-form-item>
                                <p class="underline font-bold">類型基本信息</p>
                                <a-form-item name="name" label="類型名稱(繁體中文)">
                                    <a-input v-model:value="gameType.name"></a-input>
                                </a-form-item>
                                <a-form-item name="name_secondary" label="類型名稱(Portguês)">
                                    <a-input v-model:value="gameType.name_secondary"></a-input>
                                </a-form-item>
                                <a-form-item name="code" label="類型代號">
                                    <a-input v-model:value="gameType.code"></a-input>
                                </a-form-item>
                            </a-form>
                        </a-col>
                        <a-col :span="12">
                            <div v-for="category in gameType.categories">
                                <p>{{category.code}} {{category.name}} {{category.name_secondary}}</p>
                                <a-space :size="[0, 'small']" wrap>
                                    <template v-for="weight in category.weights">
                                        <a-tag 
                                            abc="abc123"
                                            :color="weight[0]=='F'?'pink':'blue'" 
                                            closable 
                                            @close="removeTag($event, category, weight)"
                                        >
                                            {{ weight }}
                                        </a-tag>
                                    </template>
                                </a-space>
                            </div>
                            <br>
                            <div v-if="gameType.isEditing">
                                <a-input style="width:100px"/>
                                <button>Add</button>
                            </div>
                            <p>Card content</p>
                        </a-col>
                    </a-row>
                    <div v-if="gameType.isEditing">
                        <a-button type="primary" danger>刪除</a-button>
                        <a-button type="primary" @click="saveCompetitionType(gameType)">保存</a-button>
                    </div>
                </a-card>
            </template>
        </div>

        
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';

    export default {
        components: {
            AdminLayout,
        },
        props: ["gameTypes","gameCategories","languages"],
        data() {
            return{
                dateFormat:'YYYY-MM-DD',
                isEditing:false,
            }
        },
        created() {
        },
        methods: {
            saveCompetitionType(record){
                console.log(record);
            },
            removeTag(event, category, weight){
                event.preventDefault()
                console.log(category);
                console.log(weight);
            },

        }
    }

</script>

<style scoped>

#modalFrom input:disabled{
    border:none;
    color:rgba(0, 0, 0, 0.88);
    background: none;
}


</style>