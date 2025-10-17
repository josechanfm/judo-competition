<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tournamentName }}</title>
    <style>
        /* 強制單頁顯示的關鍵CSS */
        @page {
            margin: 0;
            padding: 0;
            size: A4 landscape;
        }
        
        body {
            font-family: DejaVu Sans, SimHei, Microsoft JhengHei, sans-serif;
            margin: 0;
            padding: 10px;
            width: 100%;
            height: 100%;
            background: white;
            overflow: hidden;
        }

        /* 容器設定 - 確保內容在一頁內 */
        .tournament-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .tournament-header {
            text-align: center;
            padding: 10px;
            background: #8B4513;
            color: white;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .tournament-header h1 {
            font-size: 18px;
            margin: 0;
        }

        .bracket-wrapper {
            flex: 1;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }

        .bracket-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .round {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-height: 100%;
            padding: 0 5px;
        }

        .round-header {
            text-align: center;
            background: #666;
            color: white;
            padding: 6px;
            margin-bottom: 10px;
            font-size: 12px;
            border-radius: 3px;
            width: 100%;
        }

        .matches-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            position: relative;
        }

        .match-wrapper {
            position: relative;
            width: 100%;
            margin: 10px 0;
            display: flex;
            justify-content: center;
        }

        .match {
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 5px;
            width: 90%;
            min-height: 50px;
            position: relative;
            z-index: 2;
        }

        .player {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 5px;
            margin: 1px 0;
            border-radius: 2px;
            font-size: 11px;
            min-height: 20px;
            position: relative;
        }

        .player.winner {
            background: #d4edda;
            font-weight: bold;
            border-left: 3px solid #28a745;
        }

        .player.loser {
            background: #f8d7da;
            opacity: 0.8;
        }

        .player-score {
            background: #666;
            color: white;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 9px;
            min-width: 30px;
            text-align: center;
        }

        /* 連線樣式 */
        .connectors {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .horizontal-line {
            position: absolute;
            background: #8B4513;
            height: 1px;
            z-index: 1;
        }

        .vertical-line {
            position: absolute;
            background: #8B4513;
            width: 1px;
            z-index: 1;
        }

        /* 確保沒有元素超出頁面 */
        * {
            box-sizing: border-box;
        }

        /* 防止分頁 */
        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* 動態調整連線位置 */
        .connector-right {
            right: -15px;
            width: 15px;
        }

        .connector-vertical-middle {
            height: 60px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
    <div class="tournament-container no-break">
        <div class="tournament-header">
            <h1>{{ $tournamentName }}</h1>
            <div style="font-size: 12px;">{{ $weightClass }} - {{ $date }}</div>
        </div>

        <div class="bracket-wrapper">
            <div class="bracket-content">
                @foreach($rounds as $roundIndex => $round)
                <div class="round no-break">
                    <div class="round-header">{{ $round['name'] }}</div>
                    <div class="matches-container">
                        @foreach($round['matches'] as $matchIndex => $match)
                        <div class="match-wrapper">
                            <div class="match no-break">
                                <div class="player {{ $match['winner'] == 1 ? 'winner' : 'loser' }}">
                                    <span>{{ $match['player1'] }}</span>
                                    @if(isset($match['score1']) && $match['score1'])
                                        <span class="player-score">{{ $match['score1'] }}</span>
                                    @endif
                                </div>
                                
                                <div class="player {{ $match['winner'] == 2 ? 'winner' : 'loser' }}">
                                    <span>{{ $match['player2'] }}</span>
                                    @if(isset($match['score2']) && $match['score2'])
                                        <span class="player-score">{{ $match['score2'] }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- 連線 -->
                            @if($roundIndex < count($rounds) - 1)
                            <div class="connectors">
                                <!-- 向右的水平線 -->
                                <div class="horizontal-line connector-right" 
                                     style="top: 50%;"></div>
                                
                                <!-- 垂直連接線（連接到下一輪的對應位置） -->
                                @if($matchIndex % 2 == 0)
                                <div class="vertical-line connector-vertical-middle" 
                                     style="right: -15px; height: 60px;"></div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // 簡單的JavaScript來計算和設置連線位置（dompdf支持基本的JS）
        document.addEventListener('DOMContentLoaded', function() {
            // 這裡可以添加更精確的連線計算邏輯
            adjustConnectors();
        });

        function adjustConnectors() {
            // 根據實際佈局調整連線位置
            const matches = document.querySelectorAll('.match-wrapper');
            matches.forEach((matchWrapper, index) => {
                const connectors = matchWrapper.querySelector('.connectors');
                if (connectors) {
                    const horizontalLine = connectors.querySelector('.horizontal-line');
                    const verticalLine = connectors.querySelector('.vertical-line');
                    
                    if (horizontalLine) {
                        horizontalLine.style.top = '50%';
                    }
                    
                    if (verticalLine) {
                        // 根據比賽位置調整垂直線高度
                        verticalLine.style.height = '60px';
                        verticalLine.style.top = '50%';
                    }
                }
            });
        }
    </script>
</body>
</html>