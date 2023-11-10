<template>
    <inertia-head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="mx-4 py-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            </div>
        </template>
        <div class="py-5">
            <div class="bg-white">
                <a-radio-group v-model:value="currentSection.id"  button-style="solid" @change=onSectionChange()>
                    <template v-for="section in competition.section_number" >
                        <a-radio-button :value="section">{{ section }}</a-radio-button>
                    </template>
                </a-radio-group>
                <a-row :gutter="50">
                    <a-col :span="12" v-for="mat in competition.mat_number">
                        <p>Section: {{ currentSection.mats[mat].program.section }} / Mat: {{ currentSection.mats[mat].program.mat }}</p>
                        <a-button @click="currentSection.mats[mat].sequence--;onMatChange()" >-</a-button>
                        <a-input v-model:value="currentSection.mats[mat].sequence" @change=onMatChange() style="width: 80px;text-align:center;"/>
                        <a-button @click="currentSection.mats[mat].sequence++;onMatChange()">+</a-button>
                        
                        <p>Category: {{ currentSection.mats[mat].program.category_group }}</p>
                        <p>Weight: {{ currentSection.mats[mat].program.weight_group }}</p>
                        <template v-if="currentSection.mats[mat].bout">
                            <a-typography-title :level="3">
                                <span v-if="currentSection.mats[mat].bout.white==0">---</span><span v-else>{{ currentSection.mats[mat].bout.white_player.name_display }}</span>
                                vs
                                <span v-if="currentSection.mats[mat].bout.white==0">---</span><span v-else>{{ currentSection.mats[mat].bout.blue_player.name_display }}</span>
                            </a-typography-title>
                        </template>
                    </a-col>
                </a-row>
            </div>  
        </div>
        <div class="py-5">
            <div class="bg-white">
                <a-radio-group v-model:value="selectSection"  button-style="solid">
                    <template v-for="section in competition.section_number">
                        <a-radio-button :value="section">{{ section }}</a-radio-button>
                    </template>
                </a-radio-group>
                <a-row :gutter="50">
                    <a-col :span="12" v-for="mat in competition.mat_number">
                        <p>Mat: {{ mat }}</p>
                        <div class="flex flex-col">
                            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 sm:lg:px-8">
                                <div class="overflow-hidden">
                                    <table class="min-w-full text-center text-sm font-light">
                                    <thead class="border-b bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600">
                                        <tr>
                                            <th class= "py-4 w-10">Program</th>
                                            <th class= "py-4 w-10">Sequence</th>
                                            <th class= "py-4 w-10">In Program</th>
                                            <th class= "py-4 w-10">Queue</th>
                                            <th class= "py-4 w-10">White</th>
                                            <th class= "py-4 w-10">Blue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="bout in competition.bouts">
                                            <tr v-if="bout.mat==mat && bout.section==selectSection"  
                                                class="border-b dark:border-neutral-500 dark:bg-neutral-600 odd:bg-neutral-100"
                                            >
                                                <td class= "py-4 w-10">{{ bout.program_id }}</td>
                                                <td class= "py-4 w-10">{{ bout.sequence }}</td>
                                                <td class= "py-4 w-10">{{ bout.in_program_sequence }}</td>
                                                <td class= "py-4 w-10">{{ bout.queue }}</td>
                                                <td class= "py-4 w-10">{{ bout.white_player.name_display }}</td>
                                                <td class= "py-4 w-10">{{ bout.blue_player.name_display }}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                </div>
                                </div>
                                </div>
                                </div>

                    </a-col>
                </a-row>
            </div>
        </div>

        
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { extractIdentifiers } from '@vue/compiler-core';

    export default {
        components: {
            AdminLayout,
        },
        props: ['competition'],
        data() {
            return{
                currentSection:{
                    id:1,
                    mats:{}
                },
                selectSection:1,
                dateFormat:'YYYY-MM-DD',
                columns:[
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
                        title:'White',
                        dataIndex:'white'
                    },{
                        title:'Blue',
                        dataIndex:'blue'
                    },{
                        title:'White from',
                        dataIndex:'white_rise_from'
                    },{
                        title:'Blue from',
                        dataIndex:'blue_rise_from'
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
            for(var m=1; m<=this.competition.mat_number; m++){
                this.currentSection.mats[m]={
                    sequence:1,
                    program:{}
                }
            }
            this.onMatChange();
        },
        methods: {
            onSectionChange(){
                Object.entries(this.currentSection.mats).forEach(([matId,mat])=>{
                    mat.bout={}
                    mat.program={}
                    mat.sequence=1
                })
                // this.onMatChange();
            },
            onMatChange(){
                Object.entries(this.currentSection.mats).forEach(([matId,mat])=>{
                    if(mat.sequence<1){
                        mat.sequence=1;
                        return true;
                    }
                    var tmpBout=this.competition.bouts.find(b=>
                        b.section==this.currentSection.id && 
                        b.mat==matId &&
                        b.sequence==mat.sequence
                    )
                    if(tmpBout!=undefined){
                        mat.bout=tmpBout
                        mat.program=this.competition.programs.find(p=>p.id==mat.bout.program_id);
                    }else{
                        mat.sequence=this.competition.bouts.filter(b=>b.section==this.currentSection.id && b.mat==matId).length;
                    }
                })
            }
        },
        watch:{
            "currentSection.id"(){
                console.log(this.currentSection);
            }
        }

    }

</script>



