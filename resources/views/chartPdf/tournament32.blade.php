<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" border/>
    </head>
    <body>
    <?php $boutSize=sizeOf($bouts)?>
    <table class="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr class="gapRow">
            <td></td>
            <td style="border:1px dotted gray">Athletes</td>
            <td style="border:1px dotted gray">16</td>
            <td style="border:1px dotted gray">8</td>
            <td style="border:1px dotted gray">Semi-Final</td>
            <td style="border:1px dotted gray">Final</td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td class="seq"></td>
            <td></td>
            <td></td>
            <td rowspan="2"></td>
            <td rowspan="4"></td>
            <td rowspan="8"></td>
            <td rowspan="16"></td>
        </tr>
        <tr>
            <td class="seq">1</td>
            <td class="playerBox" >{{$bouts[0]->white_player->name_display}}</td>
            <td rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td>   --}}
            {{-- <td rowspan="3"></td> --}}
            {{-- <td rowspan="7"></td> --}}
            {{-- <td rowspan="15"></td> --}}
        </tr>
        <tr>
            <td class="seq">2</td>
            <td class="playerBox">{{$bouts[0]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="topRight bottomRight alignTopLeft" rowspan="4">
                <span class="circle">{{ $bouts[0]->circle }}</span>
            </td>
            {{-- <td rowspan="2"></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            <td class="topRight alignTopLeft" rowspan="4"><span class="circle">{{$bouts[8]->circle}}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">5</td>
            <td class="playerBox">{{$bouts[1]->white_player->name_display}}</td>
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">6</td>
            <td class="playerBox">{{$bouts[1]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[1]->circle }}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>

        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignTopLeft" rowspan="8"><span class="circle">{{$bouts[$boutSize-5]->circle}}</span></td>
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">9</td>
            <td class="playerBox" >{{$bouts[2]->white_player->name_display}}</td>
            <td rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">10</td>
            <td class="playerBox">{{$bouts[2]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="topRight alignTopLeft" rowspan="2">
                <span class="circle">{{ $bouts[2]->circle }}</span>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[9]->circle }}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">13</td>
            <td class="playerBox">{{$bouts[3]->white_player->name_display}}</td>
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">14</td>
            <td class="playerBox">{{$bouts[3]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[3]->circle }}</span></td>
            <td rowspan="2"></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="8"></td>
            <td class="alignTopLeft topOnly" rowspan="16"><span class="circle">{{ $bouts[$boutSize-1]->circle }}</span></td>
        </tr>
        <tr>
            <td class="seq">17</td>
            <td class="playerBox" >{{$bouts[4]->white_player->name_display}}</td>
            <td rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td>
            <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">18</td>
            <td class="playerBox">{{$bouts[4]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="topRight alignTopLeft" rowspan="2">
                <span class="circle">{{ $bouts[4]->circle }}</span>
            </td>
            <td rowspan="2"></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignTopLeft" rowspan="4"><span class="circle">{{$bouts[10]->circle}}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">21</td>
            <td class="playerBox">{{$bouts[5]->white_player->name_display}}</td>
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">22</td>
            <td class="playerBox">{{$bouts[5]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[5]->circle }}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>

        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="alignTopLeft" rowspan="8"><span class="circle">{{$bouts[$boutSize-4]->circle}}</span></td>
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">25</td>
            <td class="playerBox" >{{$bouts[6]->white_player->name_display}}</td>
            <td rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">26</td>
            <td class="playerBox">{{$bouts[6]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="topRight alignTopLeft" rowspan="2">
                <span class="circle">{{ $bouts[6]->circle }}</span>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[11]->circle }}</span></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">29</td>
            <td class="playerBox">{{$bouts[7]->white_player->name_display}}</td>
            <td rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight"></td></tr>
                </table>
            </td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td class="seq">30</td>
            <td class="playerBox">{{$bouts[7]->blue_player->name_display}}</td>
            {{-- <td></td> --}}
            <td class="alignTopLeft" rowspan="2"><span class="circle">{{ $bouts[7]->circle }}</span></td>
            <td rowspan="2"></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
        <tr class="gapRow">
            <td></td>
            <td></td>
            <td></td>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
        </tr>
    </table>
    <hr>
    @include('chartPdf.tournamentRepeatChart')

    </body>
</html>

<style>
table.tblTournament{
  border-spacing: 0;
}
table.tblTournament td{
  width:50px;
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
    height:0px!important
}
.playerBox{
    width:200px!important;
    height:20px!important;
    border: 1px solid black!important;
    border-radius: 5px;
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
    height:20px!important
}
.innerTable .circle{
    top:0px!important;
    left:18px;
    z-index:100
}

.innerTable.innertSingle tr:first-child{
    height:35px!important
}
.innerTable.innertSingle td{
    height:10px!important
}
</style>