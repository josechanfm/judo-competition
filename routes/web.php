<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('language/{locale}', [App\Http\Controllers\LocaleController::class, 'index'])->name('app.locale.update');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
});
Route::get('default/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('manage/system/description', function () {
    return Inertia::render('System/Index');
})->name('manage.system.index');


Route::get('competition/{competition}/get_qr_code', [App\Http\Controllers\CompetitionController::class,'getQrCode'])->name('competition.getQrCode');
Route::get('competition/{competition}/show', [App\Http\Controllers\CompetitionController::class,'show'])->name('competition.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('manage/game_types', App\Http\Controllers\Manage\GameTypeController::class)->names('manage.gameTypes');
    Route::resource('manage/competitions', App\Http\Controllers\Manage\CompetitionController::class)->names('manage.competitions');
    Route::get('manage/competition/{competition}/qr_code', [App\Http\Controllers\Manage\CompetitionController::class, 'qrCode'])->name('manage.competition.qrCode');

    Route::resource('manage/competition/{competition}/programs', App\Http\Controllers\Manage\ProgramController::class)->names('manage.competition.programs');
    Route::resource('manage/competition/{competition}/teams', App\Http\Controllers\Manage\TeamController::class)->names('manage.competition.teams');
    Route::resource('manage/competition/{competition}/referees', App\Http\Controllers\Manage\RefereeController::class)->names('manage.competition.referees');
    Route::get('manage/competition/{competition}/athletes/generate-id-cards', [App\Http\Controllers\Manage\AthleteController::class, 'generateIdCards'])->name('athletes.generateIdCards');
    Route::get('manage/competition/{competition}/athletes/generate-weighIn-table', [App\Http\Controllers\Manage\AthleteController::class, 'generateAllWeighInTable'])->name('generate.all.weighIn.table');
    Route::get('manage/competition/{competition}/athletes/drawControl', [App\Http\Controllers\Manage\AthleteController::class, 'drawControl'])->name('manage.competition.athletes.drawControl');
    Route::get('manage/competition/{competition}/athletes/weights', [App\Http\Controllers\Manage\AthleteController::class, 'Weights'])->name('manage.competition.athletes.weights');
    Route::get('manage/competition/{competition}/drawScreen', [App\Http\Controllers\Manage\AthleteController::class, 'drawScreen'])->name('manage.competition.athletes.draw-screen');
    Route::get('manage/competition/{competition}/generate-all-online-table', [App\Http\Controllers\Manage\ProgramController::class, 'generateAllProgramsOnlineTable'])->name('manage.competition.generateAllProgramsOnlineTable');
    Route::resource('manage/competition/{competition}/athletes', App\Http\Controllers\Manage\AthleteController::class)->names('manage.competition.athletes');
    Route::post('manage/competition/{competition}/programsUpdate', [\App\Http\Controllers\Manage\ProgramController::class, 'programsUpdate'])->name('manage.competition.programs-update');
    Route::post('manage/competition/{competition}/program/{program}/draw', [App\Http\Controllers\Manage\ProgramController::class, 'draw'])->name('manage.competition.program.draw');
    Route::post('manage/competition/{competition}/program/{program}/reset-draw', [App\Http\Controllers\Manage\ProgramController::class, 'resetDraw'])->name('manage.competition.program.reset');
    Route::get('manage/competition/{competition}/program/{program}/generate-online-table', [App\Http\Controllers\Manage\ProgramController::class, 'generateOnlineTable'])->name('manage.competition.program.generateOnlineTable');
    Route::get('manage/competition/{competition}/program/{program}/athletes', [App\Http\Controllers\Manage\ProgramController::class, 'athletes'])->name('manage.competition.program.athletes');
    Route::post('manage/competition/{competition}/program/update-sequence', [App\Http\Controllers\Manage\ProgramController::class, 'updateSequence'])->name('manage.competition.program.sequence.update');
    Route::post('manage/competition/{competition}/program/lock', [App\Http\Controllers\Manage\ProgramController::class, 'lock'])->name('manage.competition.program.lock');
    Route::post('manage/competition/{competition}/program/lock-seat', [App\Http\Controllers\Manage\ProgramController::class, 'lockSeat'])->name('manage.competition.program.lock-seat');
    Route::post('manage/competition/{competition}/athletes/weights-lock', [App\Http\Controllers\Manage\AthleteController::class, 'weightsLock'])->name('manage.competition.athletes.weights.lock');
    Route::post('maange/competition/{competition}/athletes/weights-lock-cancel', [App\Http\Controllers\Manage\AthleteController::class, 'WeightsCancelLock'])->name('manage.competition.athletes.weights.cancelLock');
    Route::post('manage/competition/{competition}/athletes/import', [App\Http\Controllers\Manage\AthleteController::class, 'import'])->name('manage.competition.athletes.import');
    Route::post('manage/competition/{competition}/athletes/lock', [App\Http\Controllers\Manage\AthleteController::class, 'lock'])->name('manage.competition.athletes.lock');
    Route::post('manage/competition/{competition}/athletes/unlock', [App\Http\Controllers\Manage\AthleteController::class, 'unlock'])->name('manage.competition.athletes.unlock');
    Route::post('manage/competition/{competition}/programAthlete/{programAthlete}/weight_checked', [App\Http\Controllers\Manage\AthleteController::class, 'weightChecked'])->name('manage.competition.programAthlete.weightChecked');
    Route::get('manage/competition/{competition}/program/gen_bouts', [App\Http\Controllers\Manage\ProgramController::class, 'gen_bouts'])->name('manage.competition.program.gen_bouts');
    Route::get('manage/competition/{competition}/progress', [App\Http\Controllers\Manage\ProgramController::class, 'progress'])->name('manage.competition.progress');
    Route::get('manage/competition/{competition}/chart_pdf', [App\Http\Controllers\Manage\ProgramController::class, 'chartPdf'])->name('manage.competition.chartPdf');
    Route::get('manage/competition/{competition}/all_schedule', [App\Http\Controllers\Manage\Printer\ProgramScheduleController::class, 'printAllSchedule'])->name('manage.competition.allSchedule');
    Route::post('manage/program/{program}/athlete/{athlete}', [App\Http\Controllers\Manage\ProgramController::class, 'joinAthlete'])->name('manage.program.joinAthlete');
    Route::delete('manage/program/{program}/athlete/{athlete}', [App\Http\Controllers\Manage\ProgramController::class, 'removeAthlete'])->name('manage.program.removeAthlete');
    Route::get('manage/competition/{competition}/result-table/{blankMedals}', [App\Http\Controllers\Manage\CompetitionController::class, 'resultTable'])->name('manage.competition.result-table');
    // Route::get('/pdf/simple', [PdfController::class, 'generatePdfWithRoundedHeader'])->name('simplepdf');
    Route::get('importname',[App\Http\Controllers\Manage\AthleteController::class , 'importExcel']);
    Route::get('manage/competition/{competition}/export/medal_quantity', [App\Http\Controllers\Manage\ProgramController::class, 'medalQuantityExport'])->name('manage.competition.program.export.medal-quantity');    
    Route::get('manage/competition/{competition}/export/program_time', [App\Http\Controllers\Manage\ProgramController::class, 'programTimeExport'])->name('manage.competition.program.export.program-time');    
    Route::get('manage/print/demo', [App\Http\Controllers\Manage\Printer\PrinterController::class, 'demo'])->name('manage.print.demo');
    Route::get('manage/print/{competition}/programs', [App\Http\Controllers\Manage\Printer\PrinterController::class, 'programs'])->name('manage.print.programs');
    Route::get('manage/print/tournament_quarter', [App\Http\Controllers\Manage\Printer\TournamentQuarterController::class, 'printPdf'])->name('manage.print.tournament_quarter');
    Route::get('manage/print/tournament_double', [App\Http\Controllers\Manage\Printer\TournamentDoubleController::class, 'printPdf']);
    Route::get('manage/print/tournament_full', [App\Http\Controllers\Manage\Printer\TournamentFullController::class, 'printPdf']);
    Route::get('manage/print/round_robbin_option1', [App\Http\Controllers\Manage\Printer\RoundRobbinOption1Controller::class, 'printPdf'])->name('manage.print.round_robbin_option1');
    Route::get('manage/print/round_robbin_option2', [App\Http\Controllers\Manage\Printer\RoundRobbinOption2Controller::class, 'printPdf'])->name('manage.print.round_robbin_option2');
    Route::get('manage/print/tournament_knockout', [App\Http\Controllers\Manage\Printer\TournamentKnockoutController::class, 'printPdf'])->name('manage.print.tournament_knockout');;
    Route::get('manage/print/winners', [App\Http\Controllers\Manage\Printer\WinnerController::class, 'printPdf']);
    Route::get('manage/print/game_sheet', [App\Http\Controllers\Manage\Printer\PrinterController::class, 'gameSheet'])->name('name_sheet');
    Route::get('manage/print/program_schedule', [App\Http\Controllers\Manage\Printer\ProgramScheduleController::class, 'printPdf'])->name('manage.print.program_schedule');
    Route::get('manage/print/weight_in_list', [App\Http\Controllers\Manage\Printer\WeightInController::class, 'printPdf']);
    Route::get('manage/print/referee_list', [App\Http\Controllers\Manage\Printer\RefereeController::class, 'printPdf']);

    Route::get('competition/{competition}/reset',[App\Http\Controllers\Manage\AthleteController::class, 'resetBoutQuence']);
});




require __DIR__ . '/auth.php';
