<template>
    <!-- <a-button @click="showTableSeq=!showTableSeq">Sequence</a-button>
    <a-button @click="showTableGridLine=!showTableGridLine">Grid Line</a-button> -->
    <table class="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >{{bouts[0].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[0].circle}}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox">{{bouts[0].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[8].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">3</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">4</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[14].circle}}</span>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <td class="playerBox">{{bouts[1].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[1].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">6</td>
            <td class="playerBox">{{bouts[1].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">7</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">8</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[18].circle}}</span>
            </td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">9</td>
            <td class="playerBox" >{{bouts[2].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[2].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">10</td>
            <td class="playerBox">{{bouts[2].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[9].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">11</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">12</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">13</td>
            <td class="playerBox">{{bouts[3].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[3].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">14</td>
            <td class="playerBox">{{bouts[3].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">15</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">16</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td></td>
            <td class="bottomRight" rowspan="8"></td>
            <td class="topOnly"></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">17</td>
            <td class="playerBox" >{{bouts[4].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[4].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">18</td>
            <td class="playerBox">{{bouts[4].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[10].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">19</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr    >
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">20</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[15].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">21</td>
            <td class="playerBox">{{bouts[5].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[5].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">22</td>
            <td class="playerBox">{{bouts[5].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">23</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">24</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">25</td>
            <td class="playerBox" >{{bouts[6].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[6].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">26</td>
            <td class="playerBox">{{bouts[6].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[11].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">27</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">28</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">29</td>
            <td class="playerBox">{{bouts[7].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[7].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">30</td>
            <td class="playerBox">{{bouts[7].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <RepeatChart :bouts="bouts" v-if="contestSystem=='erm'" />

</template>

<script>
import RepeatChart from '@/Components/TournamentTable/RepeatChart.vue';
export default {
    components: {
        RepeatChart
    },
    props: ['contestSystem','bouts'],
    data() {
        return {
            showTableSeq:false,
            showTableGridLine:false,
        }
    },
    mounted(){
        
    }
}
</script>

<style scoped>
    @import "@/../css/tournamentChart.css";
</style>