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
        <ol>
        @foreach($bouts as $b)
            <li>{{ $b}}</li>
        @endforeach
        </ol>
    
    <table class="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr>
            <td class="seq"></td>
            <td></td>
            <td></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
            <td></td>
            <td></td>
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >0{{$bouts[0]->white_player->name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight "></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topLeft bottomLeft"></td></tr>
                </table>
            </td>
            <td class="playerBox" >1{{$bouts[1]->white_player->name_display}}</td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox">4{{$bouts[4]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="topRight alignTopLeft" rowspan="2">
                <span class="circle">{{ $bouts[0]->circle }}</span>
            </td>
            <td class="rightOnly" rowspan="2"></td>
            <td rowspan="2"></td>
            <td class="topLeft alignTopRight" rowspan="2">
                <span class="circle">{{ $bouts[1]->circle }}</span>
            </td>
            <td class="playerBox">5{{$bouts[5]->white_player->name_display}}</td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">3</td>
            <td></td>
            <td class="firstColumn"></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">4</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="alignTopLeft topOnly" rowspan="2"><span class="circle">{{ $bouts[4]->circle }}</span></td>
            <td class="alignTopRight topOnly"rowspan="2"><span class="circle">{{ $bouts[5]->circle }}</span></td>
            <td class="bottomLeft" rowspan="2"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <td class="playerBox">2{{$bouts[2]->white_player->name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight "></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topLeft bottomLeft"></td></tr>
                </table>

            </td>
            <td class="playerBox" >3{{$bouts[3]->white_player->name_display}}</td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">6</td>
            <td class="playerBox">6{{$bouts[6]->blue_player->name_display}}</td>
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[2]->circle }}</span></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
            <td class="alignTopRight" rowspan="2"><span class="circle">{{ $bouts[3]->circle }}</span></td>
            <td class="playerBox" >7{{$bouts[7]->white_player->name_display}}</td>
        </tr>
        <tr>
            <td class="seq"></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td></td>
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
.topLeft{
    border-top: 1px solid black!important;
    border-left: 1px solid black!important;
    border-top-left-radius: 5px;
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
.bottomLeft{
    border-bottom: 1px solid black!important;
    border-left: 1px solid black!important;
    border-bottom-left-radius: 5px;
}
.rightOnly{
    border-right: 1px solid black!important;
}
.alignTopLeft{
    text-align:left;
    vertical-align: top;
}
.alignTopRight{
    text-align:right;
    vertical-align: top;
}
.alignTopLeft .circle{
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
.alignTopRight .circle{
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
    left:18px;
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
