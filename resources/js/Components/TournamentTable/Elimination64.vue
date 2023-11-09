<template>
    <a-button @click="showTableSeq=!showTableSeq">Sequence</a-button>
    <a-button @click="showTableGridLine=!showTableGridLine">Grid Line</a-button>
    <table id="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">2</td>
            <td class="playerBox">{{bouts[0].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[32].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">4</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[48].circle}}</span>
            </td>
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">8</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[56].circle}}</span>
            </td>
            <td></td>
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">10</td>
            <td class="playerBox">{{bouts[2].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[33].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
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
                <span class="circle">{{bouts[60].circle}}</span>
            </td>
            <td></td>
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">18</td>
            <td class="playerBox">{{bouts[4].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[34].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
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
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">20</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[49].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
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
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">26</td>
            <td class="playerBox">{{bouts[6].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[35].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
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
            <td class="topRight alignRightBottom" rowspan="32"><span class="circle">{{bouts[62].circle}}</span></td>
            <td></td>
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">34</td>
            <td class="playerBox">{{bouts[8].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[36].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">36</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[50].circle}}</span>
            </td>
            <td></td>
            <!-- <td></td> -->
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
                <span class="circle">{{bouts[57].circle}}</span>
            </td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">42</td>
            <td class="playerBox">{{bouts[10].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[37].circle}}</span>
            </td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">50</td>
            <td class="playerBox">{{bouts[12].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[38].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">52</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[51].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">58</td>
            <td class="playerBox">{{bouts[14].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[39].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
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
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">63</td>
            <td></td>
            <td class="firstColumn"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">64</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="32"></td>
            <td class="topOnly"></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">65</td>
            <td class="playerBox" >{{bouts[16].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[16].circle}}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">66</td>
            <td class="playerBox">{{bouts[16].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[40].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">67</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">68</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[52].circle}}</span>
            </td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">69</td>
            <td class="playerBox">{{bouts[17].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[17].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">70</td>
            <td class="playerBox">{{bouts[17].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">71</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">72</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[58].circle}}</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">73</td>
            <td class="playerBox" >{{bouts[18].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[18].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">74</td>
            <td class="playerBox">{{bouts[18].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[41].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">75</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">76</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">77</td>
            <td class="playerBox">{{bouts[19].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[19].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">78</td>
            <td class="playerBox">{{bouts[19].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">79</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">80</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td></td>
            <td class="bottomRight" rowspan="8"></td>
            <td class="topRight alignRightBottom" rowspan="16">
                <span class="circle">{{bouts[61].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">81</td>
            <td class="playerBox" >{{bouts[20].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[20].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">82</td>
            <td class="playerBox">{{bouts[20].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[42].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">83</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">84</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[53].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">85</td>
            <td class="playerBox">{{bouts[21].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[21].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">86</td>
            <td class="playerBox">{{bouts[21].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">87</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">99</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">89</td>
            <td class="playerBox" >{{bouts[22].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[22].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">90</td>
            <td class="playerBox">{{bouts[22].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[43].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">91</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">92</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">93</td>
            <td class="playerBox">{{bouts[23].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[23].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">94</td>
            <td class="playerBox">{{bouts[23].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">95</td>
            <td></td>
            <td class="firstColumn"></td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">96</td>
            <td></td>
            <td class="firstColumn"></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="bottomRight" rowspan="16"></td>
            <td class="topOnly"></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">97</td>
            <td class="playerBox" >{{bouts[24].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[24].circle}}</span></td></tr>
                </table>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">98</td>
            <td class="playerBox">{{bouts[24].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[44].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">99</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">100</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[54].circle}}</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">101</td>
            <td class="playerBox">{{bouts[25].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[25].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">102</td>
            <td class="playerBox">{{bouts[25].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">103</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">104</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td class="topRight alignRightBottom" rowspan="8">
                <span class="circle">{{bouts[59].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">105</td>
            <td class="playerBox" >{{bouts[26].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[26].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">106</td>
            <td class="playerBox">{{bouts[26].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[45].circle}}</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">107</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">108</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">109</td>
            <td class="playerBox">{{bouts[27].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[27].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">110</td>
            <td class="playerBox">{{bouts[27].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">111</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">112</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td></td>
            <td class="bottomRight" rowspan="8"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">113</td>
            <td class="playerBox" >{{bouts[28].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[28].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">114</td>
            <td class="playerBox">{{bouts[28].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[46].circle}}</span>
            </td>
            <td rowspan="2"></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">115</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">116</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td class="topRight alignRightBottom" rowspan="4">
                <span class="circle">{{bouts[55].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">117</td>
            <td class="playerBox">{{bouts[29].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[29].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">118</td>
            <td class="playerBox">{{bouts[29].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">119</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">120</td>
            <td></td>
            <td class="firstColumn"></td>
            <td rowspan="2"></td>
            <td class="bottomRight" rowspan="4"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">121</td>
            <td class="playerBox" >{{bouts[30].white_name_display}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[30].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">122</td>
            <td class="playerBox">{{bouts[30].blue_name_display}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">{{bouts[47].circle}}</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">123</td>
            <td></td>
            <td class="firstColumn"></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="gapRow">
            <td v-if="showTableSeq" class="seq">124</td>
            <td></td>
            <td class="firstColumn"></td>
            <td class="bottomRight" rowspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">125</td>
            <td class="playerBox">{{bouts[31].white_name_display}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">{{bouts[31].circle}}</span></td></tr>
                </table>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">126</td>
            <td class="playerBox">{{bouts[31].blue_name_display}}</td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
        }
    },
    mounted(){
        
    }
}
</script>

<style scoped>

table#tblTournament{
  border-spacing: 0;
}
table#tblTournament td{
  width:60px;
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
    height:8px!important
}
.playerBox{
    width:200px!important;
    height:25px!important;
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
