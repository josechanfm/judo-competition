import x from"./Chart-7d9f2f2d.js";import w from"./Athlete-a34bbeaf.js";import y from"./AthleteSm-33d97679.js";import{_ as $}from"./_plugin-vue_export-helper-c27b6911.js";import{$ as B,m as d,o,f as c,a as k,w as v,b as f,F as p,p as I,c as m}from"./app-d9f75028.js";const _=1e3,g=1e3,A={name:"Player",components:{Athlete:w,AthleteSm:y,Chart:x},props:{athletes:{required:!0,type:Array}},setup(a){},mounted(){B(()=>{const l=window.document.getElementById(`athlete-${this.athletes[0].seat}`).getBoundingClientRect();for(let t=0;t<this.athletes.length;++t)setTimeout(()=>{this.animateHandler(t,l)},g*t),setTimeout(()=>{this.athletes[t].placed=!0},g*t+_)})},computed:{pendingAthletes(){return this.athletes.filter(a=>!a.placed)},athleteInSeat(){return a=>{const l=this.athletes.find(t=>t.seat===a);if(l)return l.placed?l:null}},scale(){return this.athletes.length<=8?2:this.athletes.length>=32||this.athletes.length>=16?.5:1}},methods:{animateHandler(a,l){const t=window.document.getElementById(`athlete-${this.athletes[a].seat}`),u=window.document.getElementById(`seat-${this.athletes[a].seat}`);t.style.position="fixed",t.style.transformOrigin="center";const n=l,e=u.getBoundingClientRect();t.style.width=e.width+"px",this.athletes.length>32&&(t.style.width=e.width*2+"px");const r=window.innerWidth/2-n.x-e.width/2,i=window.innerHeight/2-n.y-e.height/2;console.log("src",n.x,n.y);const h=e.x-n.x,s=e.y-n.y;console.log("dest",e.x,e.y),console.log("delta",h,s),t.animate([{transform:"translate(0, 0)"},{transform:`translate(${r}px, ${i}px) scale(2)`},{transform:`translate(${r}px, ${i}px) scale(2)`},{transform:`translate(${r}px, ${i}px) scale(2)`},{transform:`translate(${h}px, ${s}px) scale(1)`}],{duration:_,easing:"ease-in-out",fill:"backwards"})}}},E={class:"flex flex-col h-full"},S={key:1,class:"w-full bg-white"},P={class:"overflow-clip flex items-center justify-center"},R={class:"h-24 overflow-clip grid grid-cols-1 gap-2 pending-athlete-list"};function C(a,l,t,u,n,e){const r=d("athlete"),i=d("athlete-sm"),h=d("chart");return o(),c("div",E,[k(h,{athletes:t.athletes,ref:"chart",class:"origin-top flex-1"},{default:v(s=>[f("div",null,[e.athleteInSeat(s.seat)?(o(),c(p,{key:0},[this.athletes.length<=16?(o(),m(r,{key:0,athlete:e.athleteInSeat(s.seat)},null,8,["athlete"])):(o(),m(i,{key:1,athlete:e.athleteInSeat(s.seat)},null,8,["athlete"]))],64)):(o(),c("div",S))])]),_:1},8,["athletes"]),f("div",P,[f("div",R,[(o(!0),c(p,null,I(e.pendingAthletes,s=>(o(),m(r,{athlete:s,class:"w-[600px] h-24 rounded-lg p-1",id:`athlete-${s.seat}`},null,8,["athlete","id"]))),256))])])])}const F=$(A,[["render",C]]);export{F as default};
