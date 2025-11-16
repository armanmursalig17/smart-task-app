<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\TerbitanTugas;
use Illuminate\Console\Command;

class CloseExpiredTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
//    public function handle()
//     {
//         // Hitung waktu 3 menit yang lalu
//         $threeMinutesAgo = Carbon::now()->subMinutes(3);

//         // Cari tugas yang statusnya 'aktif' DAN dibuat lebih dari 3 menit yang lalu
//         $expiredTasks = TerbitanTugas::where('status', 'aktif')
//                                     ->where('created_at', '<=', $threeMinutesAgo)
//                                     ->update(['status' => 'ditutup']);

//         $this->info("{$expiredTasks} tugas telah diperbarui menjadi 'ditutup'.");

//         return 0;
//     }
}
