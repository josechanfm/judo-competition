<template>
    <a-button @click="showTableSeq=!showTableSeq">Sequence</a-button>
    <a-button @click="showTableGridLine=!showTableGridLine">Grid Line</a-button>
    <table id="tblTournament" ref="tblTournament" :class="showTableGridLine?'gridLine':''">
        <tr>
            <td v-if="showTableSeq" class="seq">1</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">1</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">17</span>
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
                <span class="circle">25</span>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">5</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">2</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">29</span>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">9</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">3</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">18</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">4</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">31</span>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">17</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">5</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">19</span>
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
                <span class="circle">26</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">21</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">6</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">7</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">20</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">8</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="topRight" rowspan="32"></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">33</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">9</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">21</span>
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
                <span class="circle">27</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">37</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">10</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">30</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">41</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">11</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">22</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">12</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">13</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">23</span>
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
                <span class="circle">28</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">53</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">14</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">15</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">24 </span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">16</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">1</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">17</span>
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
                <span class="circle">25</span>
            </td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">69</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">2</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">29</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">73</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">3</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">18</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">4</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">31</span>
            </td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">81</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">5</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">19</span>
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
                <span class="circle">26</span>
            </td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">85</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">6</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">7</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">20</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">8</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">9</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">21</span>
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
                <span class="circle">27</span>
            </td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">101</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">10</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
                <span class="circle">30</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">105</td>
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">11</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">22</span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">12</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">13</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">23</span>
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
                <span class="circle">28</span>
            </td>
            <!-- <td></td> -->
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td v-if="showTableSeq" class="seq">117</td>
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">14</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
            <td class="playerBox" >{{players[0].name}}</td>
            <td class="firstColumn" rowspan="2" >
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">15</span></td></tr>
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
            <td class="playerBox">{{players[1].name}}</td>
            <!-- <td></td> -->
            <td class="topRight alignRightBottom" rowspan="2">
                <span class="circle">24 </span>
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
            <td class="playerBox">{{players[2].name}}</td>
            <td class="firstColumn" rowspan="2">
                <table class="innerTable">
                    <tr><td class="topRight bottomRight alignRightBottom"><span class="circle">16</span></td></tr>
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
            <td class="playerBox">{{players[3].name}}</td>
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
    props: ["players"],
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
