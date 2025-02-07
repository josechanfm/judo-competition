<?php

namespace App\Http\Controllers\Api\Competition\V2;

use App\Http\Controllers\Controller;
use App\Models\Bout;
use App\Models\BoutResult;
use App\Models\Competition;
use App\Models\LinkedBout;
use App\Services\BoutGenerationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BoutController extends Controller
{
    public function __construct()
    {
        // dd(request()->user('sanctum'));
        if (request()->user('sanctum')) {
            if (!self::isContestInProgress(request()->user('sanctum'))) {
                abort(400, 'Contest is not in-progress');
            }
        }
        // dd('aa');

    }

    public function index(Request $request)
    {
        // dd('aaa');
        $competition = $request->user();
        // $competition = Competition::where('id', 3)->first();

        $limit = $request->query('limit', null);

        $ongoingBouts = null;
        $upcomingBouts = null;

        $bouts = QueryBuilder::for($competition->bouts())->with([
            'program',
            'blueAthlete.athlete.team',
            'whiteAthlete.athlete.team',
            'blueAthlete.athlete',
            'whiteAthlete.athlete'
        ])->orderBy('queue')
            ->where('queue', '>', '0')
            ->allowedFilters([
                AllowedFilter::callback('recent', function (Builder $query, $value) use (&$ongoingBouts, $request, &$upcomingBouts) {
                    // get all ongoing bouts
                    if (!$value) return $query;

                    $query2 = $query->clone();

                    if (!$request->query('filter')) {
                        abort(400, 'Missing filter');
                    };

                    if (isset($request->query('filter')['date'])) {
                        $query2->where('date', $request->query('filter')['date']);
                        $date = $request->query('filter')['date'];
                    }

                    if (isset($request->query('filter')['mat'])) {
                        $query2->where('mat', $request->query('filter')['mat']);
                        $mat = $request->query('filter')['mat'];
                    }

                    if (isset($request->query('filter')['section'])) {
                        $query2->where('section', $request->query('filter')['section']);
                        $section = $request->query('filter')['section'];
                    }

                    $ongoingBouts = $query2->clone()
                        ->where(function ($q) {
                            return
                                $q->where('status', Bout::STATUS_STARTED)
                                ->orWhere(function ($qry) {
                                    return $qry->where('status', Bout::STATUS_PENDING)
                                        ->where('queue', 1);
                                });
                        })->pluck('queue', 'mat');

                    $upcomingBouts = $query2->clone()->toBase()
                        ->addSelect([DB::raw('MIN(queue) as queue'), 'mat'])
                        ->where('status', Bout::STATUS_PENDING)
                        ->groupBy('mat')
                        ->pluck('queue', 'mat');

                    $query->where(function ($qry) use ($upcomingBouts, $date, $section) {
                        $qry->whereIn('queue', []);

                        foreach ($upcomingBouts as $mat => $queue) {
                            $qry->orWhere(function ($q) use ($mat, $date, $section, $queue) {
                                return $q->where('mat', $mat)
                                    ->where('section', $section)
                                    ->where('date', $date)
                                    ->where(function ($q2) use ($queue) {
                                        return $q2
                                            ->whereBetween('queue', [max(1, $queue - 2), $queue + 11])
                                            ->orWhere('status', Bout::STATUS_STARTED);
                                    });
                            });
                        }
                    });

                    return $query;
                })->default(false),
                'date',
                'mat',
                'section'
            ]);

        return response()->json([
            'bouts' => $bouts->get(),
            'ongoingBouts' => $ongoingBouts,
            'upcomingBouts' => $upcomingBouts
        ]);
    }


    public function start(Bout $bout)
    {
        if ($bout->status === Bout::STATUS_PENDING) {
            $bout->start();
        }

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function reset(Bout $bout)
    {
        if ($bout->status === Bout::STATUS_STARTED) {
            $bout->reset();
        }

        return response()->json([
            'message' => 'success'
        ]);
    }

    private static function isContestInProgress(Competition $contest)
    {
        return Competition::STATUS_PROGRAM_STARTED === $contest->status;
    }

    public function result(Request $request, LinkedBout $bout)
    {
        $validated = $request->validate([
            'status' => 'required|integer',
            'time' => 'nullable|integer',
            'b_ippon' => 'nullable|integer',
            'b_wazari' => 'nullable|integer',
            'b_shido' => 'nullable|integer',
            'b_hansoko_make' => 'nullable|boolean',
            'w_ippon' => 'nullable|integer',
            'w_wazari' => 'nullable|integer',
            'w_shido' => 'nullable|integer',
            'w_hansoko_make' => 'nullable|boolean',
            'actions' => 'nullable|array',
            'actions.*.time' => 'required|integer',
            'actions.*.action' => 'required|string',
        ]);

        if ($bout->status === Bout::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'bout is cancelled'
            ], 400);
        }

        //        if ($bout->status === Bout::STATUS_FINISHED) {
        //            return response()->json([
        //                'message' => 'bout already has result'
        //            ], 419);
        //        }

        $prevBlueBout = $bout->getPrevBlueAttribute();

        if ($prevBlueBout instanceof LinkedBout) {
            if ($prevBlueBout->status !== Bout::STATUS_FINISHED && $prevBlueBout->status !== Bout::STATUS_CANCELLED) {
                return response()->json([
                    'message' => 'Previous blue bout have not finished yet'
                ], 422);
            }
        }

        $prevWhiteBout = $bout->getPrevWhiteAttribute();

        if ($prevWhiteBout instanceof LinkedBout) {
            if ($prevWhiteBout->status !== Bout::STATUS_FINISHED && $prevWhiteBout->status !== Bout::STATUS_CANCELLED) {
                return response()->json([
                    'message' => 'Previous white bout have not finished yet'
                ], 422);
            }
        }

        $bout->uploadResult(
            (int)$validated['status'],
            new BoutResult(
                array_merge($validated, [
                    'device_uuid' => request()->user()->currentAccessToken()->name,
                ])
            )
        );

        return response()->json([
            'message' => 'success'
        ]);
    }
}
