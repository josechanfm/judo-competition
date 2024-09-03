<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SheetTournamentFullService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TournamentFullController extends Controller
{
    protected $gameSheet=null;
    protected $dummyData = null;

    public function printPdf(Request $request){
        $filePath=storage_path('setting/game_tournament_full.json');
        $settings = File::json($filePath);

        $dummyPath = storage_path('setting/game_tournament_full_dataset.json');
        $this->dummyData = File::json($dummyPath);

        $this->gameSheet=new SheetTournamentFullService($settings);
        $this->gameSheet->setFonts('times','cid0ct','times'); //times, courier, dejavusans, freemomo,freeserif, cid0ct,cid0cs, cid0kr, cid0jp, 
        $poolLable=[
            ['name'=>'分組 A'],
            ['name'=>'分組 B'],
            ['name'=>'分組 C'],
            ['name'=>'分組 D']
        ];
        $this->gameSheet->setPoolLabel($poolLable);
        if($request->winner_line){
            $this->gameSheet->setWinnerLine(true);
        }

        if(empty($request->size)){
            $this->players32();
            return true;
        }
        switch($request->size){
            case 4:
                //$this->players4();
            case 8:
                //$this->players8();
            case 16:
                $this->players16();
                break;
            case 32:
                $this->players32();
                break;
            case 64:
                $this->players64();
                break;
        }
    }


    private function players16(){
        foreach($this->dummyData[16] as $key=>$value){
            $$key=$value;
        }
        /*
        $players=[
            [
                'white'=>['name_display'=>'White player 1'],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ],[
                'white'=>['name_display'=>'White player 5'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ],[
                'white'=>['name_display'=>'White player 9'],
                'blue'=>['name_display'=>'Blue player 10'],
            ],[
                'white'=>['name_display'=>'White player 11'],
                'blue'=>['name_display'=>'Blue player 12'],
            ],[
                'white'=>['name_display'=>'White player 13'],
                'blue'=>['name_display'=>'Blue player 14'],
            ],[
                'white'=>['name_display'=>'White player 15'],
                'blue'=>['name_display'=>'Blue player 16'],
            ]
        ];
        $winners=[
            [1,2,1,2,1,2,1,2],
            [2,1,2,1],
            [1,2],
            [2],
            [2,2,1,1],
            [2,2,1,1],
            [2,1],
            [1,2],
            [2],
            [2],
        ];
        $repechageUpperPlayers=[
            [
                [
                    'white'=>['name_display'=>'White player (A1)r1','from'=>'5'],
                    'blue'=>['name_display'=>'Blue player (A2)r2','from'=>'6'],
                ],[
                    'white'=>['name_display'=>'White player (B1)r1','from'=>'7'],
                    'blue'=>['name_display'=>'Blue player (B2)r5','from'=>'8'],
                ],[
                    'white'=>['name_display'=>'White player (C1)r1','from'=>'1'],
                    'blue'=>['name_display'=>'Blue player (C2)r5','from'=>'2'],
                ],[
                    'white'=>['name_display'=>'White player (D1)r1','from'=>'3'],
                    'blue'=>['name_display'=>'Blue player (D2)r5','from'=>'4'],
                ]
            ],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'17']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'18']],
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'19']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'20']],
            ],
            [null,null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'42']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'41']],
            ],
            [null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'54']],
            ]
        ];
        $repechageLowerPlayers=[
            [
                [
                    'white'=>['name_display'=>'White player (A1)r1','from'=>'13'],
                    'blue'=>['name_display'=>'Blue player (A2)r2','from'=>'14'],
                ],[
                    'white'=>['name_display'=>'White player (B1)r1','from'=>'15'],
                    'blue'=>['name_display'=>'Blue player (B2)r5','from'=>'16'],
                ],[
                    'white'=>['name_display'=>'White player (C1)r1','from'=>'9'],
                    'blue'=>['name_display'=>'Blue player (C2)r5','from'=>'10'],
                ],[
                    'white'=>['name_display'=>'White player (D1)r1','from'=>'11'],
                    'blue'=>['name_display'=>'Blue player (D2)r5','from'=>'12'],
                ]
            ],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'21']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'22']],
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'23']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'24']],
            ],
            [null,null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'44']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'43']],
            ],
            [null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'53']],
            ]
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8],
            [41,42,43,44],
            [53,54],
            [59],
            [26,26,27,28],
            [33,34,35,36],
            [45,46],
            [49,50],
            [55],
            [57],
            [29,30,31,32],
            [37,38,39,40],
            [47,48],
            [51,52],
            [56],
            [58]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        */
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechageUpperPlayers, $repechageLowerPlayers);
    }
    private function players32(){
        foreach($this->dummyData[32] as $key=>$value){
            $$key=$value;
        }
        /*
        $players=[
            [
                'white'=>['name_display'=>'White player 1'],
                'blue'=>['name_display'=>'Blue player 2'],
            ],[
                'white'=>['name_display'=>'White player 3'],
                'blue'=>['name_display'=>'Blue player 4'],
            ],[
                'white'=>['name_display'=>'White player 5'],
                'blue'=>['name_display'=>'Blue player 6'],
            ],[
                'white'=>['name_display'=>'White player 7'],
                'blue'=>['name_display'=>'Blue player 8'],
            ],[
                'white'=>['name_display'=>'White player 9'],
                'blue'=>['name_display'=>'Blue player 10'],
            ],[
                'white'=>['name_display'=>'White player 11'],
                'blue'=>['name_display'=>'Blue player 12'],
            ],[
                'white'=>['name_display'=>'White player 13'],
                'blue'=>['name_display'=>'Blue player 14'],
            ],[
                'white'=>['name_display'=>'White player 15'],
                'blue'=>['name_display'=>'Blue player 16'],
            ],[
                'white'=>['name_display'=>'White player 17'],
                'blue'=>['name_display'=>'Blue player 18'],
            ],[
                'white'=>['name_display'=>'White player 19'],
                'blue'=>['name_display'=>'Blue player 20'],
            ],[
                'white'=>['name_display'=>'White player 21'],
                'blue'=>['name_display'=>'Blue player 22'],
            ],[
                'white'=>['name_display'=>'White player 23'],
                'blue'=>['name_display'=>'Blue player 24'],
            ],[
                'white'=>['name_display'=>'White player 25'],
                'blue'=>['name_display'=>'Blue player 26'],
            ],[
                'white'=>['name_display'=>'White player 27'],
                'blue'=>['name_display'=>'Blue player 28'],
            ],[
                'white'=>['name_display'=>'White player 29'],
                'blue'=>['name_display'=>'Blue player 30'],
            ],[
                'white'=>['name_display'=>'White player 31'],
                'blue'=>['name_display'=>'Blue player 32'],
            ]
        ];
        $winners=[
            [0,0,1,1,1,1,1,1,2,2,2,2,2,2,2,2],
            [0,0,1,1,2,2,2,2],
            [0,0,2,2],
            [1,2],
            [1],
            [2,2,2,2],
            [2,2,2,2],
            [2,2],
            [2,2],
        ];
        $repechageUpperPlayers=[
            [
                [
                    'white'=>['name_display'=>'White player (A1)r1','from'=>'5'],
                    'blue'=>['name_display'=>'Blue player (A2)r2','from'=>'6'],
                ],[
                    'white'=>['name_display'=>'White player (B1)r1','from'=>'7'],
                    'blue'=>['name_display'=>'Blue player (B2)r5','from'=>'8'],
                ],[
                    'white'=>['name_display'=>'White player (C1)r1','from'=>'1'],
                    'blue'=>['name_display'=>'Blue player (C2)r5','from'=>'2'],
                ],[
                    'white'=>['name_display'=>'White player (D1)r1','from'=>'3'],
                    'blue'=>['name_display'=>'Blue player (D2)r5','from'=>'4'],
                ]
            ],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'17']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'18']],
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'19']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'20']],
            ],
            [null,null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'42']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'41']],
            ],
            [null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'54']],
            ]
        ];
        $repechageLowerPlayers=[
            [
                [
                    'white'=>['name_display'=>'White player (A1)r1','from'=>'13'],
                    'blue'=>['name_display'=>'Blue player (A2)r2','from'=>'14'],
                ],[
                    'white'=>['name_display'=>'White player (B1)r1','from'=>'15'],
                    'blue'=>['name_display'=>'Blue player (B2)r5','from'=>'16'],
                ],[
                    'white'=>['name_display'=>'White player (C1)r1','from'=>'9'],
                    'blue'=>['name_display'=>'Blue player (C2)r5','from'=>'10'],
                ],[
                    'white'=>['name_display'=>'White player (D1)r1','from'=>'11'],
                    'blue'=>['name_display'=>'Blue player (D2)r5','from'=>'12'],
                ]
            ],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'21']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'22']],
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'23']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'24']],
            ],
            [null,null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'44']],
                ['blue'=>['name_display'=>'Blue player (17)r5','from'=>'43']],
            ],
            [null],
            [
                ['blue'=>['name_display'=>'Blue player (18)r5','from'=>'53']],
            ]
        ];
        $sequences=[
            [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
            [17,18,19,20,21,22,23,24],
            [24,26,27,28],
            [29,30],
            [31],
            [26,26,27,28],
            [33,34,35,36],
            [45,46],
            [49,50],
            [55],
            [57],
            [29,30,31,32],
            [37,38,39,40],
            [47,48],
            [51,52],
            [56],
            [58]
        ];
        $winnerList=[
            ['award'=>'Gold','name'=>'Place 1'],
            ['award'=>'Silver','name'=>'Place 2'],
            ['award'=>'Brown','name'=>'Place 3'],
            ['award'=>'Brown','name'=>'Place 4'],
        ];
        */
        $this->gameSheet->pdf($players, $winners, $sequences, $winnerList, $repechageUpperPlayers, $repechageLowerPlayers);
    }


    
}
