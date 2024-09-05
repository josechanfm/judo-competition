import{P as Z}from"./ProgramLayout-1d440752.js";import{a as i,A as K,C as A,K as D,L as $,D as ee,G as te,M as q,H as oe,m,o as r,f as c,w as n,F as p,b as l,d as u,t as h,c as k,g as y,p as x,n as se,q as ae,s as ie}from"./app-d9f75028.js";import{U as ne,A as re,H as le,L as ce,S as de,E as me,V as ue}from"./vue-draggable-next.esm-bundler-67c52cbc.js";import{d as he}from"./duration-526ae087.js";import{_ as _e}from"./_plugin-vue_export-helper-c27b6911.js";import{M as ge}from"./MoreOutlined-36102af1.js";import"./judoka-logo-eef90ab5.js";import"./VideoCameraOutlined-6422ac09.js";var pe={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M893.3 293.3L730.7 130.7c-7.5-7.5-16.7-13-26.7-16V112H144c-17.7 0-32 14.3-32 32v736c0 17.7 14.3 32 32 32h736c17.7 0 32-14.3 32-32V338.5c0-17-6.7-33.2-18.7-45.2zM384 184h256v104H384V184zm456 656H184V184h136v136c0 17.7 14.3 32 32 32h320c17.7 0 32-14.3 32-32V205.8l136 136V840zM512 442c-79.5 0-144 64.5-144 144s64.5 144 144 144 144-64.5 144-144-64.5-144-144-144zm0 224c-44.2 0-80-35.8-80-80s35.8-80 80-80 80 35.8 80 80-35.8 80-80 80z"}}]},name:"save",theme:"outlined"};const fe=pe;function L(e){for(var o=1;o<arguments.length;o++){var t=arguments[o]!=null?Object(arguments[o]):{},d=Object.keys(t);typeof Object.getOwnPropertySymbols=="function"&&(d=d.concat(Object.getOwnPropertySymbols(t).filter(function(s){return Object.getOwnPropertyDescriptor(t,s).enumerable}))),d.forEach(function(s){ve(e,s,t[s])})}return e}function ve(e,o,t){return o in e?Object.defineProperty(e,o,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[o]=t,e}var F=function(o,t){var d=L({},o,t.attrs);return i(K,L({},d,{icon:fe}),null)};F.displayName="SaveOutlined";F.inheritAttrs=!1;const be=F;A.extend(he);const ye={components:{ProgramLayout:Z,UnorderedListOutlined:ne,ExclamationCircleOutlined:D,AppstoreOutlined:re,HolderOutlined:le,EditOutlined:$,LockOutlined:ce,ScheduleOutlined:de,EnvironmentOutlined:me,DownloadOutlined:ee,SaveOutlined:be,ClockCircleOutlined:te,MoreOutlined:ge,draggable:ue},props:["competition","programs","athletes"],data(){return{view:"list",dateFormat:"YYYY-MM-DD",editDraggable:!1,multipleMove:!1,selectedPrograms:[],edit:!1,partitionedPrograms:{},batchMoveForm:{from:{day:null,section:null,mat:null},day:this.competition.days[0],section:1,mat:1},modal:{isOpen:!1,mode:null,title:"Record Modal",data:{}},columns:[{title:"Seq.",dataIndex:"sequence"},{title:"Date",dataIndex:"date"},{title:"Category",dataIndex:"category_group"},{title:"Weight Group",dataIndex:"weight_code"},{title:"Mat",dataIndex:"mat"},{title:"Section",dataIndex:"section"},{title:"Contest System",dataIndex:"contest_system"},{title:"Duration",dataIndex:"duration_formatted"},{title:"Athletes",dataIndex:"athletes"},{title:"Operation",dataIndex:"operation"}],rules:{country:{required:!0},name:{required:!0},date_start:{required:!0},date_end:{required:!0}},validateMessages:{required:"${label} is required!",types:{email:"${label} is not a valid email!",number:"${label} is not a valid number!"},number:{range:"${label} must be between ${min} and ${max}"}}}},computed:{matSecMaxTimeEst(){return(e,o,t)=>{const d=this.partitionedPrograms[e][o][t].reduce((s,_)=>s+_.duration*_.bouts_count,0);return A.duration(d,"seconds").format("HH:mm:ss")}},matSecProgramsCount(){return(e,o,t)=>this.partitionedPrograms[e][o][t].reduce((d,s)=>d+s.bouts_count,0)},isProgramChecked(){return e=>this.selectedPrograms.includes(e.id)}},watch:{view(e){e==="grid"&&this.getPartitionedPrograms(),this.selectedPrograms=[],this.$inertia.reload({preserveScroll:!0})},isBatchEditing(e){e||(this.selectedPrograms=[])}},created(){},methods:{onCreateRecord(){this.modal.title="Create",this.modal.isOpen=!0,this.modal.mode="CREATE"},onEditRecord(e){this.modal.isOpen=!0,this.modal.title="Edit",this.modal.mode="EDIT",this.modal.data={...e}},onUpdate(){this.$refs.formRef.validateFields().then(()=>{this.$inertia.put(route("manage.competitions.update",this.modal.data.id),this.modal.data,{onSuccess:e=>{this.modal.data={},this.modal.isOpen=!1},onError:e=>{console.log(e)}}),console.log("values",this.modal.data,this.modal.data)}).catch(e=>{console.log("error",e)})},confirmLockAthlets(){q.confirm({title:"Do you want to lock list of athletes?",icon:i(D),style:"top:20vh",onOk:()=>{this.lockAthletes()},onCancel(){console.log("Cancel")},class:"test"})},confirmLockSequences(){q.confirm({title:"Do you want to lock list of sequences?",icon:i(D),style:"top:20vh",onOk:()=>{this.confirmProgramArrangement()},onCancel(){console.log("Cancel")},class:"test"})},onCreate(){this.$refs.formRef.validateFields().then(()=>{this.$inertia.post(route("manage.competitions.store"),this.modal.data,{onSuccess:e=>{this.modal.data={},this.modal.isOpen=!1},onError:e=>{console.log(e)}}),console.log("values",this.modal.data,this.modal.data)}).catch(e=>{oe.error(e)})},getPartitionedPrograms(){this.initializing=!0,this.competition.days.forEach(e=>{this.partitionedPrograms[e]={};for(let o=0;o!=this.competition.section_number;o++){this.partitionedPrograms[e][o+1]={};for(let t=0;t!=this.competition.mat_number;t++)this.partitionedPrograms[e][o+1][t+1]=this.getProgramByDSM(e,o+1,t+1),this.partitionedPrograms[e][o+1][t+1].sort((d,s)=>d.sequence-s.sequence)}}),console.log(this.partitionedPrograms),this.initializing=!1},getProgramByDSM(e,o,t){return this.programs.filter(d=>d.date===e&&d.section===o&&d.mat===t)??[]},lockAthletes(){this.$inertia.post(route("manage.competition.athletes.lock",this.competition.id),"",{onSuccess:e=>{console.log(e)},onError:e=>{console.log(e)}})},toggleProgramChecked(e){console.debug("toggleProgramChecked",e),console.debug("batchMoveForm",this.batchMoveForm);const o=e.date===this.batchMoveForm.from.day&&e.section===this.batchMoveForm.from.section&&e.mat===this.batchMoveForm.from.mat;console.debug("isSameFromGroup",o),(!this.selectedPrograms.length||!o)&&(this.selectedPrograms=[],this.batchMoveForm.from.day=e.date,this.batchMoveForm.from.section=e.section,this.batchMoveForm.from.mat=e.mat),this.selectedPrograms.includes(e.id)?this.selectedPrograms=this.selectedPrograms.filter(t=>t!==e.id):this.selectedPrograms.push(e.id)},onDragEnd(e,o,t){this.partitionedPrograms[e][o][t].forEach((d,s)=>{d.sequence=s+1})},saveDrag(){const e=[];this.competition.days.forEach(o=>{for(let t=0;t!=this.competition.section_number;t++)for(let d=0;d!=this.competition.mat_number;d++)this.partitionedPrograms[o][t+1][d+1].forEach((s,_)=>{s.sequence=_+1,e.push(s)})}),this.$inertia.post(route("manage.competition.program.sequence.update",this.competition.id),e,{onSuccess:o=>{this.editDraggable=!1,this.multipleMove=!1,this.$message.success("移動成功")}})},cancelDrag(){this.editDraggable=!1,this.getPartitionedPrograms()},cancelMovePrograms(){this.selectedPrograms=[],this.multipleMove=!1},batchMovePrograms(){const e=this.batchMoveForm.day,o=this.batchMoveForm.section,t=this.batchMoveForm.mat;console.debug("batchMoveForm",this.batchMoveForm);const d=this.partitionedPrograms[this.batchMoveForm.from.day][this.batchMoveForm.from.section][this.batchMoveForm.from.mat],s=this.partitionedPrograms[e][o][t];this.selectedPrograms.forEach(_=>{const P=d.splice(d.findIndex(M=>M.id===_),1)[0];P.date=e,P.section=o,P.mat=t,s.push(P)}),this.selectedPrograms=[],this.saveDrag()},confirmProgramArrangement(){this.$inertia.post(route("manage.competition.program.lock",this.competition),null,{preserveScroll:!0,onSuccess:()=>{this.$message.success("已確認比賽安排"),this.$inertia.reload({preserveScroll:!0,only:["programs"]})}})}}},V=e=>(ae("data-v-99e86715"),e=e(),ie(),e),ke={class:"flex items-center gap-1"},xe={class:"flex items-center gap-1"},Pe={key:1},Me={key:3,class:"text-blue-500"},Se={class:"mx-6"},Ce={class:"overflow-hidden flex flex-col gap-3"},Oe={class:"flex w-full gap-6"},we={class:"flex flex-1 flex-col"},De={class:"flex justify-between"},Fe={class:"text-xl font-bold mt-6 mb-2"},Ee={key:0,class:"my-2 shadow-lg bg-white rounded-lg"},Ie={class:"pb-2 mx-4 mt-2 flex justify-between"},qe=V(()=>l("div",{class:"text-xl font-bold"},"All Categories",-1)),Le={class:""},Ae={class:"flex items-center gap-2"},Ve={key:2},je={key:0,class:"pr-4 py-2 flex justify-between bg-white shadow-md rounded-sm"},Be={class:"flex justify-start"},He={key:0},Ue={key:0},ze={key:1,class:"flex gap-2 items-center pl-4"},Re={class:"flex justify-end"},Ne={key:0},Te={class:"text-2xl font-medium mb-6"},Ge={class:"font-bold text-lg mb-3"},Ye={class:"grid grid-cols-2 gap-3 mb-6"},We=["href"],Je=["href"],Qe=["href"],Xe={key:0,class:"w-8"},Ze={class:"flex-1 flex items-center gap-3"},Ke={class:"mb-2"},$e={class:"text-sm text-neutral-500"},et=V(()=>l("div",{class:"w-72 flex flex-col"},[l("div",{class:""},[l("h3",{class:"font-bold text-lg mb-3"},"More function"),l("div",{class:"flex flex-col gap-2"},[l("a",null,"View Bouts")])]),l("div",{class:""},[l("h3",{class:"font-bold text-lg mb-3"},"Pritn Files")])],-1));function tt(e,o,t,d,s,_){const P=m("inertia-head"),M=m("AppstoreOutlined"),S=m("a-tag"),j=m("ScheduleOutlined"),f=m("a-button"),B=m("a-page-header"),H=m("UnorderedListOutlined"),E=m("a-radio-button"),U=m("a-radio-group"),I=m("DownloadOutlined"),z=m("a-table"),C=m("a-select-option"),O=m("a-select"),R=m("MoreOutlined"),w=m("a-menu-item"),N=m("a-menu-divider"),T=m("a-menu"),G=m("a-dropdown"),Y=m("a-checkbox"),W=m("HolderOutlined"),J=m("draggable"),Q=m("a-card"),X=m("ProgramLayout");return r(),c(p,null,[i(P,{title:"Competition Program"}),i(X,{competitionId:t.competition.id},{default:n(()=>[i(B,{title:"Competition Program Manage"},{tags:n(()=>[i(S,{color:"processing"},{default:n(()=>[l("div",ke,[i(M),u(" "+h(t.competition.mat_number)+" Mats ",1)])]),_:1}),i(S,{color:"processing"},{default:n(()=>[l("div",xe,[i(j),u(" "+h(t.competition.section_number)+" Sections ",1)])]),_:1})]),extra:n(()=>[t.competition.status===0?(r(),k(f,{key:0,type:"primary",class:"bg-blue-500",onClick:_.confirmLockAthlets},{default:n(()=>[u("Lock list of athletes")]),_:1},8,["onClick"])):y("",!0),t.competition.status<1?(r(),c("span",Pe,"Unlocked list of athletes")):t.competition.status===1?(r(),k(f,{key:2,type:"primary",class:"bg-blue-500",onClick:_.confirmLockSequences},{default:n(()=>[u("Lock sequence")]),_:1},8,["onClick"])):(r(),c("span",Me,"Sequence already Lock"))]),_:1}),l("div",Se,[l("div",Ce,[l("div",Oe,[l("div",we,[l("div",De,[l("div",Fe," Totol "+h(t.programs.length)+" Programs ",1),i(U,{"option-type":"button",value:s.view,"onUpdate:value":o[0]||(o[0]=a=>s.view=a)},{default:n(()=>[i(E,{value:"list"},{default:n(()=>[i(H)]),_:1}),i(E,{value:"grid"},{default:n(()=>[i(M)]),_:1})]),_:1},8,["value"])]),s.view==="list"?(r(),c("div",Ee,[l("div",Ie,[qe,l("div",Le,[i(f,{type:"link"},{default:n(()=>[l("div",Ae,[i(I),u("Print pdf ")])]),_:1})])]),i(z,{dataSource:t.programs,columns:s.columns},{bodyCell:n(({column:a,record:g})=>[a.dataIndex==="category_group"?(r(),c(p,{key:0},[u(h(g.competition_category.name),1)],64)):y("",!0),a.dataIndex==="operation"?(r(),k(f,{key:1,href:e.route("manage.competition.programs.show",[g.competition_category.competition_id,g.id])},{default:n(()=>[u(" View ")]),_:2},1032,["href"])):y("",!0),a.dataIndex==="athletes"?(r(),c("span",Ve,h(g.athletes_count),1)):y("",!0),a.dataIndex==="contest_system"?(r(),c(p,{key:3},[s.edit==!1?(r(),c(p,{key:0},[u(h(g.contest_system),1)],64)):y("",!0)],64)):(r(),c(p,{key:4},[u(h(g[a.dataIndex]),1)],64))]),_:1},8,["dataSource","columns"])])):(r(),c(p,{key:1},[t.competition.status===1?(r(),c("div",je,[l("div",Be,[s.editDraggable?y("",!0):(r(),c("div",He,[s.multipleMove?(r(),c("div",ze,[l("span",null," Move "+h(s.selectedPrograms.length)+" items to: ",1),i(O,{class:"w-32",value:s.batchMoveForm.day,"onUpdate:value":o[2]||(o[2]=a=>s.batchMoveForm.day=a),name:"day"},{default:n(()=>[(r(!0),c(p,null,x(t.competition.days,a=>(r(),k(C,{id:`opt-day-${a}`,key:a,value:a},{default:n(()=>[u(h(a),1)]),_:2},1032,["id","value"]))),128))]),_:1},8,["value"]),i(O,{class:"w-24",value:s.batchMoveForm.section,"onUpdate:value":o[3]||(o[3]=a=>s.batchMoveForm.section=a),name:"section"},{default:n(()=>[(r(!0),c(p,null,x(t.competition.section_number,a=>(r(),k(C,{id:`opt-section-${a}`,key:a,value:a},{default:n(()=>[u("Section "+h(a),1)]),_:2},1032,["id","value"]))),128))]),_:1},8,["value"]),i(O,{class:"w-24",value:s.batchMoveForm.mat,"onUpdate:value":o[4]||(o[4]=a=>s.batchMoveForm.mat=a),name:"mat"},{default:n(()=>[(r(!0),c(p,null,x(t.competition.mat_number,a=>(r(),k(C,{id:`opt-mat-${a}`,key:a,value:a},{default:n(()=>[u("Mat "+h(a),1)]),_:2},1032,["id","value"]))),128))]),_:1},8,["value"]),i(f,{onClick:_.batchMovePrograms},{default:n(()=>[u("Move and save")]),_:1},8,["onClick"]),i(f,{onClick:_.cancelMovePrograms},{default:n(()=>[u("Cancel")]),_:1},8,["onClick"])])):(r(),c("div",Ue,[i(f,{type:"link",onClick:o[1]||(o[1]=a=>s.multipleMove=!s.multipleMove)},{default:n(()=>[u("Batch move(mat/section)")]),_:1})]))]))]),l("div",Re,[s.multipleMove?y("",!0):(r(),c("div",Ne,[s.editDraggable?(r(),c(p,{key:1},[i(f,{type:"link",onClick:_.saveDrag},{default:n(()=>[u("Save")]),_:1},8,["onClick"]),i(f,{type:"link",onClick:_.cancelDrag},{default:n(()=>[u("Cancel")]),_:1},8,["onClick"])],64)):(r(),k(f,{key:0,type:"link",onClick:o[5]||(o[5]=a=>s.editDraggable=!s.editDraggable)},{default:n(()=>[u("Edit")]),_:1}))])),i(f,{type:"link"},{icon:n(()=>[i(I)]),default:n(()=>[u("Print pdf")]),_:1})])])):y("",!0),(r(!0),c(p,null,x(t.competition.days,a=>(r(),c("div",{class:"mb-6",key:a},[l("div",Te,h(a),1),(r(!0),c(p,null,x(t.competition.section_number,g=>(r(),c("div",{key:g},[l("div",Ge,"Section "+h(g),1),l("div",Ye,[(r(!0),c(p,null,x(t.competition.mat_number,b=>(r(),k(Q,{title:`場地 ${b}`,key:b,class:"borderless",style:{"min-height":"300px"}},{extra:n(()=>[u(h(_.matSecProgramsCount(a,g,b))+"Mat, "+h(_.matSecMaxTimeEst(a,g,b))+" ",1),i(G,{class:"ml-3",placement:"bottomRight"},{overlay:n(()=>[i(T,null,{default:n(()=>[i(w,null,{default:n(()=>[l("a",{href:e.route("admin.contests.programs.pdf-mat-brackets",{competition:t.competition,date:a,section:g,mat:b}),target:"_blank"},"下載上線表",8,We)]),_:2},1024),i(w,null,{default:n(()=>[l("a",{href:e.route("admin.contests.programs.pdf-mat-brackets",{competition:t.competition,date:a,section:g,mat:b,show_sequence:!0}),target:"_blank"},"下載上線表（場次安排次序）",8,Je)]),_:2},1024),i(N),i(w,null,{default:n(()=>[l("a",{href:e.route("admin.contests.programs.pdf-mat-bouts",{competition:t.competition,date:a,section:g,mat:b,show_sequence:!0}),target:"_blank"},"下載場次表",8,Qe)]),_:2},1024)]),_:2},1024)]),default:n(()=>[i(f,{type:"text"},{icon:n(()=>[i(R)]),_:1})]),_:2},1024)]),default:n(()=>[i(J,{class:"py-2",disabled:!s.editDraggable,onEnd:v=>_.onDragEnd(a,g,b),list:s.partitionedPrograms[a][g][b]},{default:n(()=>[(r(!0),c(p,null,x(s.partitionedPrograms[a][g][b],v=>(r(),c("div",{class:se([s.editDraggable?"cursor-grab":"","flex items-center px-4 pt-2"]),key:v.name},[s.multipleMove?(r(),c("div",Xe,[i(Y,{value:v.id,checked:_.isProgramChecked(v),id:`chk-${v.id}`,onChange:ot=>_.toggleProgramChecked(v)},null,8,["value","checked","id","onChange"])])):y("",!0),l("div",Ze,[s.editDraggable?(r(),k(W,{key:0})):y("",!0),l("div",null,[l("div",Ke,[i(S,null,{default:n(()=>[u(h(v.competition_category.name),1)]),_:2},1024),i(S,{class:"uppercase"},{default:n(()=>[u(h(v.contest_system),1)]),_:2},1024),u(" "+h(v.weight_code),1)]),l("div",$e," 共 "+h(v.athletes_count)+" 人，"+h(v.bouts_count)+" 場次 ",1)])])],2))),128))]),_:2},1032,["disabled","onEnd","list"])]),_:2},1032,["title"]))),128))])]))),128))]))),128))],64))]),et])])])]),_:1},8,["competitionId"])],64)}const mt=_e(ye,[["render",tt],["__scopeId","data-v-99e86715"]]);export{mt as default};
