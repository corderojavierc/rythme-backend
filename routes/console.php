<?php

declare(strict_types=1);

use App\Jobs\SnapshotMonthlyRankingJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SnapshotMonthlyRankingJob())
    ->monthlyOn(1, '00:05')
    ->withoutOverlapping();
