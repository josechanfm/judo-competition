<?php

namespace App\Jobs;

use App\Models\Competition;
use App\Models\ProgramAthlete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\TestMail;
use App\Services\Printer\AthletePdfService;
use Illuminate\Support\Facades\Log;

class SendAthleteCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $athlete;
    protected $competition;

    public function __construct($athlete, Competition $competition)
    {
        $this->athlete = $athlete;
        $this->competition = $competition;
    }

    public function handle()
    {
        try {
            // 使用 AthletePdfService 生成運動員證
            $service = new AthletePdfService();

            $pdf = $service->generateOneIdCard($this->athlete);
            // 生成安全的檔案名稱
            $fileName = sprintf(
                '%s_%s_%s_%s.pdf',
                $this->athlete->id,
                preg_replace('/[^A-Za-z0-9]/', '', $this->athlete->name ?? 'athlete'),
                preg_replace('/[^A-Za-z0-9]/', '', $this->athlete->name_secondary ?? ''),
                time()
            );
            
            $path = 'public/pdf/athlete_cards/' . $fileName;
            Storage::put($path, $pdf);
            
            // 準備郵件數據
            $mailData = [
                'title' => '恭喜你已成功過磅',
                'subject' => $this->competition->name . ' - 運動員卡',
                'view_name' => 'mail.applicationMail',
                'file_path' => $path,
            ];
            
            // 獲取運動員郵箱
            $email = $this->athlete->email ?? null;

            if ($email) {
                Mail::to($email)->send(new TestMail($mailData));
                
                Log::info('運動員證件郵件發送成功', [
                    'email' => $email, 
                    'athlete_id' => $this->athlete->id,
                ]);
            } else {
                Log::warning('運動員郵件地址為空', [
                    'athlete_id' => $this->athlete->id,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('運動員證件發送失敗', [
                'athlete_id' => $this->athlete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 可以選擇重試
            $this->release(60); // 60秒後重試
        }
    }
}