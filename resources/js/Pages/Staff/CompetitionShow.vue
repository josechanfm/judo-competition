<template>
    <inertia-head :title="$t('competitions.manage')" />
  
    <GuestLayout>
      <template #header>
        <div class="mx-4 py-4">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $t("competitions.manage") }}
          </h2>
        </div>
      </template>
      <div class="py-12 mx-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div><a-button>Current Only</a-button></div>
          <a-radio-group v-model:value="selected.date" button-style="solid">
            <a-radio-button v-for="day in competition.days" :value="day">{{ day }}</a-radio-button>
          </a-radio-group>
          <hr>
          <a-radio-group v-model:value="selected.section" button-style="solid">
            <a-radio-button v-for="section in competition.section_number" :value="section">Section: {{ section }}</a-radio-button>
          </a-radio-group>

          <a-tabs v-model:activeKey="selected.mat" type="card">
            <a-tab-pane v-for="mat in competition.mat_number" :key="mat" :tab="'Mat: '+mat">
              <a-list :data-source="currentBouts">
                <template #renderItem="{ item }">
                  <a-list-item>
                    {{ item.program?.weight_code??'' }}::
                    {{ item.queue }}
                    <!-- {{ item.white_player.programs }} -->
                    {{ item.white_player.name_display }} / {{ item.blue_player.name_display }}
                  </a-list-item>
                </template>
              </a-list>

            </a-tab-pane>
          </a-tabs>
          <div>
            <ol>
              <li>主檢錄</li>
              <li>檢錄員</li>
              <li>頒獎台</li>
              <li>司儀MC</li>
              <li>裁判</li>
            </ol>
          </div>
        </div>
      </div>
    </GuestLayout>
  </template>
  
  <script>
  import GuestLayout from "@/Layouts/GuestLayout.vue";
  export default {
    components: {
      GuestLayout,
    },
    props: ["competition","bouts"],
    data() {
      return {
        selected:{
          date:this.competition.days[0],
          section:1,
          mat:1,
          bouts:[]
        }
      };
    },
    mounted(){
      window.app=this
    },
    computed: {
      currentBouts(){
        return this.bouts.filter(b=>
          b.date==this.selected.date && 
          b.section==this.selected.section && 
          b.mat==this.selected.mat
        );
      }
    },
    created() {
    },
    methods: {
    },
  };
  </script>
  