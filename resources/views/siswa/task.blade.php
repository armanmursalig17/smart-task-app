<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $terbitanTugas->tugas->nama_tugas }} - Soal {{ $soalIndex }}</title>
    <link rel="icon" href="{{ asset('img/tutwuri.png') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans min-h-screen">

    <div class="container mx-auto px-4 py-8 md:py-12">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            {{ $terbitanTugas->tugas->nama_tugas }} - Kelas {{ $terbitanTugas->kelas->nama_kelas }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <div class="md:col-span-1">
                <div class="md:sticky md:top-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
                        <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-blue-600">Navigasi Soal</h3>

                        <div class="flex flex-wrap gap-2">
                            @for ($i = 1; $i <= $totalSoal; $i++)
                                <a href="{{ route('siswa.task', [$terbitanTugas->id, $i]) }}"
                                    class="w-10 h-10 flex items-center justify-center rounded-lg font-semibold transition-colors
                                @if ($i == $soalIndex) bg-blue-600 text-white shadow-md border-2 border-blue-800
                                @elseif ($session['jawaban'][$session['soal_ids'][$i - 1]])
                                    bg-green-500 text-white hover:bg-green-600
                                @else
                                    bg-gray-200 text-gray-700 hover:bg-gray-300 @endif">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>
                    </div>

                    @if ($terbitanTugas->durasi_menit > 0)
                        <div class="bg-red-50 p-6 rounded-xl shadow-lg border border-red-300 text-center">
                            <h3 class="text-lg font-semibold text-red-700">Waktu Tersisa:</h3>
                            <div id="countdown-timer" class="text-3xl font-bold text-red-900 mt-2">
                                {{ floor($remainingTime / 60) }}:{{ str_pad($remainingTime % 60, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="md:col-span-3">
                <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg">

                    <h2 class="text-xl font-bold mb-4 text-indigo-700">Soal Nomor {{ $soalIndex }} dari
                        {{ $totalSoal }}</h2>
                    <hr class="mb-6">



                    <div class="mb-16 prose max-w-none">
                        @if ($soal->keterangan_soal)
                            <p class="mt-8 mb-8 p-3 bg-gray-100 rounded-md text-sm italic">{!! $soal->keterangan_soal !!}</p>
                        @endif

                        @if ($soal->gambar_soal)
                            <img src="{{ Storage::url($soal->gambar_soal) }}" alt="Gambar Soal"
                                class="mt-2 rounded-lg w-full max-w-md mx-auto">
                        @endif

                        <div class="mt-6 mb-4">{!! $soal->pertanyaan !!}</div>

                    </div>


                    <form id="submit-answer-form" action="{{ route('siswa.submitAnswer', $terbitanTugas->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="soal_id" value="{{ $soal->id }}">

                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Pilihan Jawaban:</h3>

                        @if ($soal->tipe_soal_di_tugas == 'pilihan_ganda')
                            <div class="space-y-3">
                                @foreach ($soal->opsiJawabans as $index => $opsi)
                                    @php
                                        // Konversi index 0, 1, 2 menjadi A, B, C
                                        $label = chr(65 + $index);
                                    @endphp
                                    <label
                                        class="flex items-start p-4 rounded-lg border cursor-pointer 
                            
                            {{ $jawabanSiswa == $index ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500' : 'bg-white border-gray-200 hover:border-indigo-400' }}">
                                        <input type="radio" name="jawaban" value="{{ $index }}"
                                            class="mt-1 mr-3 text-indigo-600 focus:ring-indigo-500">

                                        <div class="flex-grow">
                                            <span class="font-bold text-indigo-700 mr-2">{{ $label }}.</span>
                                            <span class="text-gray-900">{!! $opsi->opsi_teks !!}</span>
                                            @if ($opsi->opsi_gambar)
                                                <img src="{{ Storage::url($opsi->opsi_gambar) }}" alt="Opsi Gambar"
                                                    class="mt-2 w-32 rounded">
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($soal->tipe_soal_di_tugas == 'uraian')
                            <textarea name="jawaban" id="uraian_jawaban" rows="6" placeholder="Tulis jawaban Anda di sini..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ $jawabanSiswa }}</textarea>
                        @else
                            <p class="text-red-500">Tipe soal tidak dikenali.</p>
                        @endif

                        <div class="mt-8 pt-4 border-t flex justify-between items-center">

                            @if ($soalIndex > 1)
                                <a href="{{ route('siswa.task', [$terbitanTugas->id, $soalIndex - 1]) }}"
                                    class="px-5 py-2 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                                    &larr; Soal Sebelumnya
                                </a>
                            @else
                                <span></span>
                            @endif

                            <button type="submit" name="action"
                                value="{{ $soalIndex < $totalSoal ? 'next' : 'finish' }}"
                                class="px-8 py-3 
                {{ $soalIndex < $totalSoal ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-green-600 hover:bg-green-700' }} 
                text-white rounded-lg font-semibold transition-colors">
                                {{ $soalIndex < $totalSoal ? 'Simpan & Selanjutnya &rarr;' : 'Selesai & Kumpulkan Jawaban' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('submit-answer-form').addEventListener('submit', function(event) {
            const form = event.currentTarget;

            const soalType = "{{ $soal->tipe_soal_di_tugas }}";
            let isAnswered = false;

            if (soalType === 'pilihan_ganda') {
                const radios = form.elements['jawaban'];
                for (let i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        isAnswered = true;
                        break;
                    }
                }
            } else if (soalType === 'uraian') {
                const textarea = document.getElementById('uraian_jawaban');
                if (textarea && textarea.value.trim() !== '') {
                    isAnswered = true;
                }
            }

            const action = form.elements['action'].value;


            if (!isAnswered && (action === 'next' || action === 'finish')) {
                event.preventDefault();
                alert("Anda harus memilih/mengisi jawaban sebelum Simpan & Lanjut atau Selesai.");
            }
        });


        const radioInputs = document.querySelectorAll('input[name="jawaban"]');

        const savedAnswer = "{{ $jawabanSiswa }}";

        radioInputs.forEach(input => {

            if (input.value == savedAnswer && savedAnswer !== '') {
                input.checked = true;
            }


            input.addEventListener('change', function() {

                document.querySelectorAll('.space-y-3 > label').forEach(label => {
                    label.classList.remove('bg-indigo-50', 'border-indigo-500', 'ring-2',
                        'ring-indigo-500');
                    label.classList.add('bg-white', 'border-gray-200', 'hover:border-indigo-400');
                });


                if (this.checked) {
                    const currentLabel = this.closest('label');
                    currentLabel.classList.add('bg-indigo-50', 'border-indigo-500', 'ring-2',
                        'ring-indigo-500');
                    currentLabel.classList.remove('bg-white', 'border-gray-200', 'hover:border-indigo-400');
                }
            });


            if (input.checked) {

                setTimeout(() => input.dispatchEvent(new Event('change')), 0);
            }
        });
    </script>

    @if ($terbitanTugas->durasi_menit > 0)
        <script>
            // Ambil sisa waktu dari Laravel (dalam detik)
            let timeRemaining = {{ $remainingTime }};
            const timerElement = document.getElementById('countdown-timer');
            const submitForm = document.getElementById('submit-answer-form');

            function updateTimer() {
                if (timeRemaining <= 0) {
                    timerElement.textContent = "Waktu Habis!";

                    clearInterval(timerInterval);

                    // Otomatis kumpulkan jawaban (submit form)
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'action';
                    hiddenInput.value = 'finish';
                    submitForm.appendChild(hiddenInput);

                    // Kirim form
                    submitForm.submit();

                    return;
                }

                // Hitungan Mundur
                const hours = Math.floor(timeRemaining / 3600);
                const minutes = Math.floor((timeRemaining % 3600) / 60);
                const seconds = timeRemaining % 60;

                const displayHours = hours > 0 ? hours.toString().padStart(2, '0') + ':' : '';
                const displayMinutes = minutes.toString().padStart(2, '0');
                const displaySeconds = seconds.toString().padStart(2, '0');

                timerElement.textContent = displayHours + displayMinutes + ':' + displaySeconds;

                // Peringatan waktu
                if (timeRemaining <= 300 && timeRemaining > 0) {
                    timerElement.classList.add('animate-pulse');
                } else {
                    timerElement.classList.remove('animate-pulse');
                }

                timeRemaining--;
            }

            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);
        </script>
    @endif


</body>

</html>
