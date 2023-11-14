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
    <?php $boutSize=sizeOf($bouts)?>
    <table class="tblTournament">
        <tr>
            @for($i=1;$i<(log($program->chart_size,2)-2);$i++)
                <td></td>
            @endfor
            <td style="width:800px">
                <table class="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td rowspan="2"></td>
                        <td rowspan="3"></td>
                        <td rowspan="6"></td>
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R1</td>
                        <td class="playerBox" >{{$bouts[0]->white_player->name_display}}</td>
                        <td  rowspan="2" >
                            <table class="innerTable">
                                <tr><td class="topRight bottomRight"></td></tr>
                            </table>
                        </td>
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R2</td>
                        <td class="playerBox">{{$bouts[0]->blue_player->name_display}}</td>
                        <!-- <td></td> -->
                        <td class="topRight alignTopLeft">
                            <span class="circle">{{ $bouts[$boutSize-7]->circle }}</span>
                        </td>
                        {{-- <td rowspan="2"></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        <td rowspan="3" colspan="2"  style="vertical-align:top">
                            <table class="innerTable innertSingle">
                                <tr><td class="bottomRight"></td></tr>
                            </table>
                        </td>
                        <!-- <td></td> -->
                        {{-- <td></td> --}}
                        <td class="topOnly alignTopLeft" rowspan="3"  style="vertical-align:top"><span class="circle">{{$bouts[$boutSize-3]->circle}}</span></td>
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        {{-- <td ></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R5</td>
                        <td class="playerBox">{{$bouts[1]->white_player->name_display}}</td>
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td rowspan="2"></td>
                        <td rowspan="3"></td>
                        <td rowspan="6"></td>
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R8</td>
                        <td class="playerBox" >{{$bouts[0]->white_player->name_display}}</td>
                        <td  rowspan="2" >
                            <table class="innerTable">
                                <tr><td class="topRight bottomRight"></td></tr>
                            </table>
                        </td>
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R8</td>
                        <td class="playerBox">{{$bouts[0]->blue_player->name_display}}</td>
                        <!-- <td></td> -->
                        <td class="topRight alignTopLeft">
                            <span class="circle">{{ $bouts[$boutSize-6]->circle }}</span>
                        </td>
                        {{-- <td rowspan="2"></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        <td rowspan="3" colspan="2">
                            <table class="innerTable innertSingle">
                                <tr><td class="bottomRight"></td></tr>
                            </table>
                        </td>
                        <!-- <td></td> -->
                        {{-- <td></td> --}}
                        <td class="topOnly alignTopLeft" rowspan="3"><span class="circle">{{$bouts[$boutSize-2]->circle}}</span></td>
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        {{-- <td ></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr>
                        <td v-if="showTableSeq" class="seq">R12</td>
                        <td class="playerBox">{{$bouts[1]->white_player->name_display}}</td>
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                        {{-- <td></td> --}}
                    </tr>
                    <tr class="gapRow">
                        <td v-if="showTableSeq"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                </table>
            </td>
        </tr>
    </body>
</html>

<style scoped>
.innerTable.innertSingle tr:first-child{
    height:30px!important
}
.innerTable.innertSingle td{
    height:10px!important
}
</style>