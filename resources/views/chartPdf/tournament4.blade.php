<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>
    <table class="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >{{$bouts[0]->white_player->name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight "></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox">{{$bouts[0]->blue_player->name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignTopLeft" rowspan="2">
                <span class="circle">{{ $bouts[0]->circle }}</span>
            </td>
            <td class="bottomOnly" rowspan="2"></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">3</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">4</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[2]->circle }}</span></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <td class="playerBox">{{$bouts[1]->white_player->name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight "></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">6</td>
            <td class="playerBox">{{$bouts[1]->blue_player->name_display}}</td>
            <!-- <td></td> -->
            <td class="alignTopLeft"><span class="circle">{{ $bouts[1]->circle }}</span></td>
            <td></td>
        </tr>
    </table>
    </body>
</html>

<style>
table.tblTournament{
  border-spacing: 0;
  z-index:-1
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
    width:40px;
}
.topRight{
    border-top: 1px solid black!important;
    border-right: 1px solid black!important;
    border-top-right-radius: 5px;
}
.bottomOnly{
    border-bottom: 1px solid black!important;
    width:40px;
}
.bottomRight{
    border-bottom: 1px solid black!important;
    border-right: 1px solid black!important;
    border-bottom-right-radius: 5px;
}
.alignTopLeft{
    text-align:left;
    vertical-align: top;
}
.alignTopRight{
    text-align:right;
    vertical-align: top;
}
span.circle{
    background: #e3e3e3;
    border-radius: 50%;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    color: #6e6e6e;
    display: inline-block;
    font-weight: bold;
    line-height: 25px;
    height: 30px;
    width: 30px;
    margin-right: 5px;
    text-align: center;
    position:relative;
    top:-18px;
    left:-18px;
}
.innerTable{
    border-spacing: 0;  
    width:100%;
}
.innerTable td{
    height:40px!important

}
.innerTable .circle{
    top:0px!important;
    left:18px;
    z-index:100
}


</style>

</style>