<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\JawabanSiswa;
use Illuminate\Http\Request;
use App\Models\TerbitanTugas;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NilaiTugasController extends Controller
{
       public function showNilaiPartisipan(Tugas $tugas)
    {
        
        $terbitanIds = TerbitanTugas::where('tugas_id', $tugas->id)->pluck('id');

        
        $jawabanSiswa = JawabanSiswa::whereIn('terbitan_tugas_id', $terbitanIds)
            ->orderBy('student_name')
            ->get();

       
        return view('tugas.nilai-partisipan', [
            'tugas' => $tugas,
            'jawabanSiswa' => $jawabanSiswa,
        ]);
    }

    public function exportNilaiToCsv(Tugas $tugas): StreamedResponse
    {
        
        $terbitanIds = TerbitanTugas::where('tugas_id', $tugas->id)->pluck('id');
        $jawabanSiswa = JawabanSiswa::whereIn('terbitan_tugas_id', $terbitanIds)
                                     ->orderBy('student_name')
                                     ->get();

       
        $fileName = 'Nilai_' . $tugas->nama_tugas . '_' . $tugas->kelas->nama_kelas . '.csv';

        
        $response = new StreamedResponse(function() use ($jawabanSiswa) {
          
            $file = fopen('php://output', 'w');

          
            $delimiter = ','; 

           
            fputcsv($file, ['Nama Siswa', 'Nilai '], $delimiter);

            
            foreach ($jawabanSiswa as $jawaban) {
                fputcsv($file, [
                    $jawaban->student_name,
                    $jawaban->nilai_otomatis ?? 'N/A' 
                ], $delimiter);
            }

            
            fclose($file);
        });

       
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
