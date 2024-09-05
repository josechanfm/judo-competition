import{A as L}from"./AdminLayout-ab9d13cb.js";import{m as _,o as d,f as m,a as t,w as l,F as C,b as o,n as k,d as r,p as U,t as h,g}from"./app-d9f75028.js";import{h as S}from"./moment-a9aaa855.js";import{_ as R}from"./_plugin-vue_export-helper-c27b6911.js";import"./judoka-logo-eef90ab5.js";import"./VideoCameraOutlined-6422ac09.js";const M={components:{AdminLayout:L},props:["countries","gameTypes","languages"],data(){return{dateFormat:"YYYY-MM-DD",disabledDate:null,tmpContestTime:null,setting_index:0,gameCategories:[],selectCategories:[],create_competition:{system:"Q",type:"I",mat_number:1,section_number:1,referee_number:1,gender:2,days:[],seeding:8,language:"en",small_system:{2:!1,3:!1,4:!1,5:!1}},columns:[{title:"Name",dataIndex:"name"},{title:"Country",dataIndex:"country"},{title:"Date Start",dataIndex:"date_start"},{title:"Date End",dataIndex:"date_end"},{title:"Mat Number",dataIndex:"mat_number"},{title:"Section Number",dataIndex:"section_number"},{title:"Status",dataIndex:"status"},{title:"Operation",dataIndex:"operation"}],rules:{game_type_id:{required:!0},country:{required:!0},name:{required:!0},name_secondary:{required:!0},date_start:{required:!0},date_end:{required:!0},days:{required:!0},mat_number:{required:!0},categories:{required:!0},section_number:{required:!0}},validateMessages:{required:"${label} is required!",types:{email:"${label} is not a valid email!",number:"${label} is not a valid number!"},number:{range:"${label} must be between ${min} and ${max}"}}}},computed:{selectLanguage(){return this.languages.map(n=>{var a,v;return console.log(n),n.value==((a=this.create_competition)==null?void 0:a.language)||n.value==((v=this.create_competition)==null?void 0:v.language_secondary)?{...n,disabled:!0}:n})}},created(){this.disabledDate=n=>!this.create_competition.date_start&&!this.create_competition.date_end?!1:n<S(this.create_competition.date_start).valueOf()||n>S(this.create_competition.date_end).valueOf(),this.endDateDisabled=n=>this.create_competition.date_start?n<S(this.create_competition.date_start).valueOf():!1},methods:{addTimeToForm(){this.create_competition.days.includes(this.tmpContestTime)||!this.tmpContestTime||this.create_competition.days.push(this.tmpContestTime)},removeTimeFromForm(n){this.create_competition.days=this.create_competition.days.filter(a=>a!==n)},changeGameType(n){this.gameCategories=[],this.create_competition.competition_type=this.gameTypes.find(a=>a.id==n),this.gameCategories=this.create_competition.competition_type.categories},changeCategories(){this.create_competition.categories=this.create_competition.competition_type.categories.filter(n=>this.selectCategories.includes(n.id)),console.log(this.create_competition.categories)},changeWeightFormat(n,a){return n.filter(v=>v.includes(a)).map(v=>{let p=v.replace(a,"");return p.includes("-")?(p=p.replace("-",""),`-${p}kg`):p.includes("+")?(p=p.replace("+",""),`+${p}kg`):p}).join(",")},onCreate(){this.$refs.formRef.validateFields().then(()=>{this.$inertia.post(route("manage.competitions.store"),this.create_competition,{onSuccess:n=>{this.create_competition={}},onError:n=>{console.log(n)}}),console.log("values",this.create_competition,this.create_competition)}).catch(n=>{console.log("error",n)})},changeSettingIndex(n){this.$refs.formRef.validateFields().then(()=>{this.setting_index=n})}}},Y={class:"py-12 mx-8"},j=o("div",{class:"mb-8 flex justify-between"},[o("div",{class:"text-xl font-bold"},"Competition Create")],-1),V={class:"bg-white overflow-hidden shadow-sm sm:rounded-lg flex"},B={class:"border-r-2 border-gray-300 shrink-0 py-2 px-2"},O={class:"flex flex-col font-bold text-lg gap-2"},A={class:"w-full p-4"},W={class:"flex flex-col"},E={key:0,class:"flex flex-col"},z={class:"flex justify-between gap-3"},G={class:"w-1/2"},Q={class:"w-1/2"},K={class:"flex justify-between gap-3"},P={class:"w-full"},H={class:""},J={class:""},X={class:"flex justify-between gap-3"},Z={class:"w-full"},$={class:"w-full"},ee={class:"w-full"},te={class:"w-full"},ae={class:"w-full"},ie={class:"w-full flex-col"},le={class:"flex-1 font-bold"},oe={class:"flex gap-3 mb-2"},ne={class:""},se={key:1,class:"flex flex-col"},re={key:0,class:""},ue={class:""},de={class:""},me={key:0,class:""},ce={class:""},_e={class:""},pe={key:1,class:""},ve={class:""},fe={class:""},ge={key:2,class:""},ye={class:""},be={class:""},he={class:"flex gap-3"},xe=o("div",{class:""},"2 players",-1),Ce={class:"flex gap-3"},ke=o("div",{class:""},"3 players",-1),we={class:"flex gap-3"},Fe=o("div",{class:""},"4 players",-1),Ue={class:"flex gap-3"},Se=o("div",{class:""},"5 players",-1),Te={key:3,class:""},Ne={class:""},De={class:"flex justify-between gap-3"},qe={class:"w-full flex flex-col"},Ie={class:"w-full"},Le={key:0,class:""},Re={class:"w-full flex flex-col"},Me={class:""},Ye={key:0,class:""},je={class:"flex justify-end h-full"};function Ve(n,a,v,p,e,c){const T=_("inertia-head"),y=_("a-button"),b=_("a-select"),s=_("a-form-item"),x=_("a-input"),w=_("a-date-picker"),F=_("a-input-number"),N=_("a-textarea"),u=_("a-radio"),f=_("a-radio-group"),D=_("a-switch"),q=_("a-form"),I=_("AdminLayout");return d(),m(C,null,[t(T,{title:"Competition Create"}),t(I,null,{default:l(()=>[o("div",Y,[j,o("div",V,[o("div",B,[o("div",O,[o("div",null,[t(y,{type:"text",class:k(["text-base font-bold flex items-center py-4",e.setting_index==0?"bg-gray-300":""]),onClick:a[0]||(a[0]=i=>c.changeSettingIndex(0))},{default:l(()=>[r("Basie information")]),_:1},8,["class"])]),o("div",null,[t(y,{type:"text",class:k(["text-base font-bold flex items-center py-4",e.setting_index==1?"bg-gray-300":""]),onClick:a[1]||(a[1]=i=>c.changeSettingIndex(1))},{default:l(()=>[r("Category Setting")]),_:1},8,["class"])]),o("div",null,[t(y,{type:"text",class:k(["text-base font-bold flex items-center py-4",e.setting_index==2?"bg-gray-300":""]),onClick:a[2]||(a[2]=i=>c.changeSettingIndex(2))},{default:l(()=>[r("System Setting")]),_:1},8,["class"])]),o("div",null,[t(y,{type:"text",class:k(["text-base font-bold flex items-center py-4",e.setting_index==3?"bg-gray-300":""]),onClick:a[3]||(a[3]=i=>c.changeSettingIndex(3))},{default:l(()=>[r("Language Setting")]),_:1},8,["class"])])])]),o("div",A,[t(q,{name:"ModalForm",ref:"formRef",model:e.create_competition,layout:"vertical",autocomplete:"off",rules:e.rules,"validate-messages":e.validateMessages},{default:l(()=>[o("div",W,[e.setting_index==0?(d(),m("div",E,[o("div",z,[o("div",G,[t(s,{label:"Competition Type",name:"game_type_id"},{default:l(()=>[t(b,{onChange:c.changeGameType,type:"select",value:e.create_competition.game_type_id,"onUpdate:value":a[4]||(a[4]=i=>e.create_competition.game_type_id=i),"show-search":"",options:v.gameTypes,fieldNames:{value:"id",label:"name"}},null,8,["onChange","value","options"])]),_:1})]),o("div",Q,[t(s,{label:"Competition Categories",name:"categories"},{default:l(()=>[t(b,{onChange:c.changeCategories,value:e.selectCategories,"onUpdate:value":a[5]||(a[5]=i=>e.selectCategories=i),type:"select","show-search":"",mode:"multiple",options:e.gameCategories,fieldNames:{value:"id",label:"name"}},null,8,["onChange","value","options"])]),_:1})])]),o("div",K,[o("div",P,[t(s,{label:"Competition Name",name:"name"},{default:l(()=>[t(x,{type:"input",value:e.create_competition.name,"onUpdate:value":a[6]||(a[6]=i=>e.create_competition.name=i)},null,8,["value"])]),_:1})])]),o("div",H,[t(s,{label:"Country",name:"country"},{default:l(()=>[t(b,{value:e.create_competition.country,"onUpdate:value":a[7]||(a[7]=i=>e.create_competition.country=i),"show-search":"",options:v.countries,fieldNames:{value:"name",label:"name"}},null,8,["value","options"])]),_:1})]),o("div",J,[t(s,{label:"City",name:"city"},{default:l(()=>[t(x,{class:"",type:"input",value:e.create_competition.scale,"onUpdate:value":a[8]||(a[8]=i=>e.create_competition.scale=i)},null,8,["value"])]),_:1})]),o("div",X,[o("div",Z,[t(s,{label:"Start Date",name:"date_start"},{default:l(()=>[t(w,{value:e.create_competition.date_start,"onUpdate:value":a[9]||(a[9]=i=>e.create_competition.date_start=i),format:e.dateFormat,valueFormat:e.dateFormat},null,8,["value","format","valueFormat"])]),_:1})]),o("div",$,[t(s,{label:"End Date",name:"date_end"},{default:l(()=>[t(w,{value:e.create_competition.date_end,"onUpdate:value":a[10]||(a[10]=i=>e.create_competition.date_end=i),format:e.dateFormat,"disabled-date":n.endDateDisabled,valueFormat:e.dateFormat},null,8,["value","format","disabled-date","valueFormat"])]),_:1})]),o("div",ee,[t(s,{label:"Mat Number",name:"mat_number"},{default:l(()=>[t(F,{value:e.create_competition.mat_number,"onUpdate:value":a[11]||(a[11]=i=>e.create_competition.mat_number=i),style:{width:"150px"},min:1},null,8,["value"])]),_:1})]),o("div",te,[t(s,{label:"Section Number",name:"section_number"},{default:l(()=>[t(F,{value:e.create_competition.section_number,"onUpdate:value":a[12]||(a[12]=i=>e.create_competition.section_number=i),style:{width:"150px"},min:1},null,8,["value"])]),_:1})]),o("div",ae,[t(s,{label:"Referee Number",name:"referee_number"},{default:l(()=>[t(F,{vlaue:e.create_competition.referee_number,"onUpdate:vlaue":a[13]||(a[13]=i=>e.create_competition.referee_number=i),style:{width:"150px"},defaultValue:"1",min:1},null,8,["vlaue"])]),_:1})])]),o("div",ie,[t(s,{label:"Days",name:"days"},{default:l(()=>[(d(!0),m(C,null,U(e.create_competition.days,i=>(d(),m("div",{class:"rounded shadow border p-2 w-72 flex items-center mb-2",key:i},[o("div",le,h(i),1),o("div",null,[t(y,{type:"link",danger:"",onClick:Be=>c.removeTimeFromForm(i)},{default:l(()=>[r("Remove")]),_:2},1032,["onClick"])])]))),128))]),_:1}),o("div",oe,[t(w,{disabled:!e.create_competition.date_start||!e.create_competition.date_end,value:e.tmpContestTime,"onUpdate:value":a[14]||(a[14]=i=>e.tmpContestTime=i),"disabled-date":e.disabledDate,"value-format":"YYYY-MM-DD"},null,8,["disabled","value","disabled-date"]),t(y,{onClick:c.addTimeToForm},{default:l(()=>[r("Add")]),_:1},8,["onClick"])])]),o("div",ne,[t(s,{label:"Remark",name:"remark"},{default:l(()=>[t(N,{value:e.create_competition.remark,"onUpdate:value":a[15]||(a[15]=i=>e.create_competition.remark=i),rows:5},null,8,["value"])]),_:1})])])):e.setting_index==1?(d(),m("div",se,[e.create_competition.game_type_id!=null?(d(),m("div",re,[o("div",ue,[t(s,{label:"Type",name:"type"},{default:l(()=>[t(f,{value:e.create_competition.type,"onUpdate:value":a[16]||(a[16]=i=>e.create_competition.type=i)},{default:l(()=>[t(u,{value:"I"},{default:l(()=>[r("Individual")]),_:1}),t(u,{value:"T"},{default:l(()=>[r("Teams")]),_:1})]),_:1},8,["value"])]),_:1})]),o("div",de,[t(s,{label:"Gender",name:"gender"},{default:l(()=>[t(f,{value:e.create_competition.gender,"onUpdate:value":a[17]||(a[17]=i=>e.create_competition.gender=i)},{default:l(()=>[t(u,{value:2},{default:l(()=>[r("male & female")]),_:1}),t(u,{value:1},{default:l(()=>[r("male")]),_:1}),t(u,{value:0},{default:l(()=>[r("female")]),_:1})]),_:1},8,["value"])]),_:1})]),e.create_competition.gender==1||e.create_competition.gender==2?(d(),m("div",me,[t(s,{label:"Male Categories",name:"categories_male"},{default:l(()=>[(d(!0),m(C,null,U(e.create_competition.categories,i=>(d(),m("div",{class:"",key:i.id},[o("div",ce,h(i.name),1),o("div",_e,h(c.changeWeightFormat(i.weights,"MW")),1)]))),128))]),_:1})])):g("",!0),e.create_competition.gender==0||e.create_competition.gender==2?(d(),m("div",pe,[t(s,{label:"Female Categories",name:"categories_female"},{default:l(()=>[(d(!0),m(C,null,U(e.create_competition.categories,i=>(d(),m("div",{class:"",key:i.id},[o("div",ve,h(i.name),1),o("div",fe,h(c.changeWeightFormat(i.weights,"FW")),1)]))),128))]),_:1})])):g("",!0)])):g("",!0)])):g("",!0),e.setting_index==2?(d(),m("div",ge,[o("div",ye,[t(s,{label:"Competition System",name:"system"},{default:l(()=>[t(f,{value:e.create_competition.system,"onUpdate:value":a[18]||(a[18]=i=>e.create_competition.system=i)},{default:l(()=>[t(u,{value:"Q"},{default:l(()=>[r("Quarter")]),_:1}),t(u,{value:"F"},{default:l(()=>[r("Full")]),_:1}),t(u,{value:"K"},{default:l(()=>[r("KO")]),_:1})]),_:1},8,["value"])]),_:1})]),o("div",be,[t(s,{label:"Seeding",name:"seeding"},{default:l(()=>[t(f,{value:e.create_competition.seeding,"onUpdate:value":a[19]||(a[19]=i=>e.create_competition.seeding=i)},{default:l(()=>[t(u,{value:8},{default:l(()=>[r("8 players")]),_:1}),t(u,{value:4},{default:l(()=>[r("4 players")]),_:1}),t(u,{value:0},{default:l(()=>[r("no player")]),_:1})]),_:1},8,["value"])]),_:1})]),o("div",null,[t(s,{label:"Less than 5 people"},{default:l(()=>[o("div",he,[xe,t(f,{value:e.create_competition.small_system[2],"onUpdate:value":a[20]||(a[20]=i=>e.create_competition.small_system[2]=i)},{default:l(()=>[t(u,{value:!1},{default:l(()=>[r("one Final")]),_:1})]),_:1},8,["value"])]),o("div",Ce,[ke,t(f,{value:e.create_competition.small_system[3],"onUpdate:value":a[21]||(a[21]=i=>e.create_competition.small_system[3]=i)},{default:l(()=>[t(u,{value:!1},{default:l(()=>[r("Round Robin")]),_:1}),t(u,{value:!0},{default:l(()=>[r("Semi-Finals + Final")]),_:1})]),_:1},8,["value"])]),o("div",we,[Fe,t(f,{value:e.create_competition.small_system[4],"onUpdate:value":a[22]||(a[22]=i=>e.create_competition.small_system[4]=i)},{default:l(()=>[t(u,{value:!1},{default:l(()=>[r("Round Robin")]),_:1}),t(u,{value:!0},{default:l(()=>[r("Semi-Finals + one Bronze + Final")]),_:1})]),_:1},8,["value"])]),o("div",Ue,[Se,t(f,{value:e.create_competition.small_system[5],"onUpdate:value":a[23]||(a[23]=i=>e.create_competition.small_system[5]=i)},{default:l(()=>[t(u,{value:!1},{default:l(()=>[r("Round Robin")]),_:1}),t(u,{value:!0},{default:l(()=>[r("Pool with 2 and pool with 3 - best each in Final, second in one Bronze")]),_:1})]),_:1},8,["value"])])]),_:1})])])):g("",!0),e.setting_index==3?(d(),m("div",Te,[o("div",Ne,[t(s,{label:"Open language secondary",name:["competition_type","is_language_secondary_enabled"]},{default:l(()=>[t(D,{checked:e.create_competition.competition_type.is_language_secondary_enabled,"onUpdate:checked":a[24]||(a[24]=i=>e.create_competition.competition_type.is_language_secondary_enabled=i),unCheckedValue:0,checkedValue:1},null,8,["checked"])]),_:1})]),o("div",De,[o("div",qe,[o("div",Ie,[t(s,{label:"Name",name:"name"},{default:l(()=>[t(x,{type:"input",value:e.create_competition.name,"onUpdate:value":a[25]||(a[25]=i=>e.create_competition.name=i)},null,8,["value"])]),_:1})]),e.create_competition.competition_type.is_language_secondary_enabled==1?(d(),m("div",Le,[t(s,{label:"Competition Name Secondary",name:"name_secondary"},{default:l(()=>[t(x,{type:"input",value:e.create_competition.name_secondary,"onUpdate:value":a[26]||(a[26]=i=>e.create_competition.name_secondary=i)},null,8,["value"])]),_:1})])):g("",!0)]),o("div",Re,[o("div",Me,[t(s,{label:"Competition Language",name:["competition_type","language"],rules:[{required:!0,message:"Competition Language is required!"}]},{default:l(()=>[t(b,{value:e.create_competition.competition_type.language,"onUpdate:value":a[27]||(a[27]=i=>e.create_competition.competition_type.language=i),options:c.selectLanguage,fieldNames:{value:"value",label:"name"}},null,8,["value","options"])]),_:1})]),e.create_competition.competition_type.is_language_secondary_enabled==1?(d(),m("div",Ye,[t(s,{label:"Competition Language Secondary",name:["competition_type","language_secondary"],rules:[{required:!0,message:"Competition Language Secondary is required!"}]},{default:l(()=>[t(b,{value:e.create_competition.competition_type.language_secondary,"onUpdate:value":a[28]||(a[28]=i=>e.create_competition.competition_type.language_secondary=i),options:c.selectLanguage,fieldNames:{value:"value",label:"name"}},null,8,["value","options"])]),_:1})])):g("",!0)])])])):g("",!0),o("div",je,[t(s,null,{default:l(()=>[t(y,{class:"text-right",onClick:c.onCreate},{default:l(()=>[r("Create")]),_:1},8,["onClick"])]),_:1})])])]),_:1},8,["model","rules","validate-messages"])])])])]),_:1})],64)}const Qe=R(M,[["render",Ve]]);export{Qe as default};
