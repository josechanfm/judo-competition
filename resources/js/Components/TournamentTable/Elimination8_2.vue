<template>
    <div class="bg-white">
    <table id="tblTournament">
        <tr>
            <td class="playerBox" rowspan="2">{{players[0]}}</td>
            <td>wind 1</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="topRight win"></td>
            <td>wind 1/2</td>
            <td></td>
        </tr>
        <tr>
            <td class="gapHeight"></td>
            <td class="right" align="right">r1</td>
            <td class="topRight"></td>
            <td></td>
        </tr>
        <tr>
            <td class="playerBox" rowspan="2">{{players[1]}}</td>
            <td class="bottomRight">win2</td>
            <td class="right"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="right"></td>
            <td>wind final</td>
        </tr>
        <tr>
            <td class="gapHeight"></td>
            <td></td>
            <td class="right" align="right">r3</td>
            <td class="top">r4</td>
        </tr>
        <tr>
            <td class="playerBox" rowspan="2">{{players[2]}}</td>
            <td>wind 3</td>
            <td class="right"></td>
            <td></td>
        </tr>
        <tr>
            <td class="topRight win"></td>
            <td class="right"></td>
            <td></td>
        </tr>
        <tr>
            <td class="gapHeight"></td>
            <td class="right win" align="right">r2</td>
            <td class="bottomRight">wind 2/4</td>
            <td></td>
        </tr>
        <tr>
            <td class="playerBox" rowspan="2">{{players[3]}}</td>
            <td class="bottomRight">wind 4</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

</div>
</template>

<script>
export default {
    components: {
    },
    props: ["players"],
    data() {
        return{
            
        }
    }
}
</script>

<style scoped>
table#tblTournament {
  border-spacing: 0;
}
table#tblTournament td{
  width:80px;
  height:20px;
}
.playerBox{
    width:200px!important;
    border: 1px solid black;
    border-radius: 5px;
}
.topRight{
    border-top: 1px solid black;
    border-right: 1px solid black;
    border-top-right-radius: 5px;
}
.bottomRight{
    border-bottom: 1px solid black;
    border-right: 1px solid black;
    border-bottom-right-radius: 5px;
}
.right{
    border-right: 1px solid black
}
.bottomLeftCorner{
    border-bottom-left-radius: 5px
}
.topLeftCorner{
    border-top-left-radius: 5px
}
.top{
    border-top: 1px solid black;
    
}
.gapHeight{
    height:10px
}
.topRight.win{
    border-top: 3px solid red;
    border-right: 3px solid red;
}
.bottomRight.win{
    border-bottom: 3px solid red;
    border-right: 3px solid red;
}
.right.win{
    border-right: 3px solid red
}
.top.win{
    border-top: 3px solid red
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
</style>