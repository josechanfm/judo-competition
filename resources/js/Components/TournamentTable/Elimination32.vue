<template>
    <a-button @click="showTableSeq=!showTableSeq">Sequence</a-button>
    <a-button @click="showTableGridLine=!showTableGridLine">Grid Line</a-button>
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox">{{bouts[0].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[16].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">4</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[24].circle}}</span>
            </td>
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">8</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[30].circle}}</span>
            </td>
            <td></td>
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">10</td>
            <td class="playerBox">{{bouts[2].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[17].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">16</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td></td>
            <td class="bottomRight" rowspan="8"></td>
            <td class="topRight alignRightBottom" rowspan="16">
                <span class="circle">{{bouts[34].circle}}</span>
            </td>
            <td></td>
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">18</td>
            <td class="playerBox">{{bouts[4].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[18].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">20</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[25].circle}}</span>
            </td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">26</td>
            <td class="playerBox">{{bouts[6].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[19].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">27</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">28</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">30</td>
            <td class="playerBox">{{bouts[7].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">31</td>
            <td></td>
            <td class="firstColumn"></td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">32</td>
            <td></td>
            <td class="firstColumn"></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="16"></td>
            <td class="topOnly"></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">33</td>
            <td class="playerBox" >{{bouts[8].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[8].circle}}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">34</td>
            <td class="playerBox">{{bouts[8].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[20].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">35</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">36</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[26].circle}}</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">37</td>
            <td class="playerBox">{{bouts[9].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[9].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">38</td>
            <td class="playerBox">{{bouts[9].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">39</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">40</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[31].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">41</td>
            <td class="playerBox" >{{bouts[10].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[10].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">42</td>
            <td class="playerBox">{{bouts[10].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[21].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">43</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">44</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">45</td>
            <td class="playerBox">{{bouts[11].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[11].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">46</td>
            <td class="playerBox">{{bouts[11].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">47</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">48</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td></td>
            <td class="bottomRight" rowspan="8"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">49</td>
            <td class="playerBox" >{{bouts[12].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[12].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">50</td>
            <td class="playerBox">{{bouts[12].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[22].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">51</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">52</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[27].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">53</td>
            <td class="playerBox">{{bouts[13].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[13].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">54</td>
            <td class="playerBox">{{bouts[13].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">55</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">56</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">57</td>
            <td class="playerBox" >{{bouts[14].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[14].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">58</td>
            <td class="playerBox">{{bouts[14].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[23].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">59</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">60</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">61</td>
            <td class="playerBox">{{bouts[15].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[15].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">62</td>
            <td class="playerBox">{{bouts[15].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
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