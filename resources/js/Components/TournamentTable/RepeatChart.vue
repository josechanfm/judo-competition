<template>
        <table class="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr>
            <th colspan="5">
                <a-typography-title :level="3">Repeat Chart</a-typography-title>
            </th>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >player1</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{ bouts[boutSize-7].circle }}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox" >player2</td>
            <!-- <td>a</td> -->
            <td class="topRight alignRightBottom"><span class="circle">{{ bouts[boutSize-3].circle }}</span></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">3</td>
            <td></td>
            <td class="bottomRight" colspan="2" rowspan="2"></td>
            <!-- <td>a</td> -->
            <!-- <td>a</td> -->
            <td class="topOnly"></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">4</td>
            <td class="playerBox" rowspan="2">player2</td>
            <td colspan="2"></td>
            <!-- <td>a</td> -->
            <!-- <td>a</td> -->
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <!-- <td></td> -->
            <td colspan="2"></td>
            <!-- <td>a</td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">6</td>
            <td style="height:50px!important"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >player1</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{ bouts[boutSize-6].circle }}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox" >player2</td>
            <!-- <td>a</td> -->
            <td class="topRight alignRightBottom"><span class="circle">{{ bouts[boutSize-2].circle }}</span></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">3</td>
            <td></td>
            <td class="bottomRight" colspan="2" rowspan="2"></td>
            <!-- <td>a</td> -->
            <!-- <td>a</td> -->
            <td class="topOnly"></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">4</td>
            <td class="playerBox" rowspan="2">player2</td>
            <td colspan="2"></td>
            <!-- <td>a</td> -->
            <!-- <td>a</td> -->
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <!-- <td></td> -->
            <td colspan="2"></td>
            <!-- <td>a</td> -->
            <td></td>
        </tr>
    </table>

</template>
<script>
export default {
    components: {
    },
    props: ['bouts'],
    data() {
        return {
            showTableSeq:false,
            showTableGridLine:false,
            boutSize:this.bouts.length
        }
    },
    mounted(){
        console.log(this.boutSize);
    }
}
</script>

<style scoped>

table.tblTournament{
  border-spacing: 0;
}
table.tblTournament td{
  width:80px;
  height:0px;
}
table.gridLine td{
    border: 1px dotted lightgray;
}
.seq{
    height:5px!important;
    font-size:10px;
    width:10px!important
}.gapRow{
    height:10px!important
}
.playerBox{
    width:200px!important;
    height:40px!important;
    border: 1px solid black!important;
    border-radius: 5px;
}
.firstColumn{
    width:10px!important;
}
.topOnly{
    border-top: 1px solid black!important;
}.topRight{
    border-top: 1px solid black!important;
    border-right: 1px solid black!important;
    border-top-right-radius: 5px;
}
.bottomRight{
    border-bottom: 1px solid black!important;
    border-right: 1px solid black!important;
    border-bottom-right-radius: 5px;
}
.topOnly.win{
    border-top: 3px solid red!important;
}
.bottomOnly.win{
    border-bottom: 3px solid red!important;
    border-right: 1px solid black!important;
    border-bottom-right-radius: 5px;
}
.top.win{
    border-top: 3px solid red!important;
    border-right: 1px solid black!important;
    border-top-right-radius: 5px;
}
.topRight.win{
    border-top: 3px solid red!important;
    border-right: 3px solid red!important;
}.bottomRight.win{
    border-bottom: 3px solid red!important;
    border-right: 3px solid red!important;
}

.alignRightBottom{
    text-align:right;
    vertical-align: bottom;
}
span.circle {
    background: #e3e3e3;
    border-radius: 50%;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    color: #6e6e6e;
    display: inline-block;
    font-weight: bold;
    line-height: 30px;
    width: 30px;
    margin-right: 5px;
    text-align: center;
    position:relative;
    top:20px;
    left:20px;
}
.innerTable{
    border-spacing: 0;  
}
.innerTable td{
    height:20px!important
}
.innerTable .circle{
    top:0px!important;
    left:25px;
}

</style>
