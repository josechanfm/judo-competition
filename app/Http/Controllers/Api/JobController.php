<?php

namespace Modules\Admin\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobStatus;

class JobController extends Controller
{
    public function dismissAllFinished()
    {
        $finishedJobIds = auth()->user()
                                ->jobs()
                                ->where('status', 'finished')
                                ->pluck('id');

        JobStatus::destroy($finishedJobIds);

        return response()->json([
            'dismissedJobIds' => $finishedJobIds
        ]);
    }

    public function jobStatuses()
    {
        $jobs = auth()->user()->jobs;

        return response()->json([
            'jobs' => $jobs
        ]);
    }

    public function remove(JobStatus $jobStatus)
    {
        if ($jobStatus->status === 'finished') {
            $jobStatus->delete();
        }
    }
}
