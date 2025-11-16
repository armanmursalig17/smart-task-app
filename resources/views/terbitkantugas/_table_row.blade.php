

<tr>
    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
        {{ $terbitan->created_at->format('d M Y H:i') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">{{ $terbitan->kelas->nama_kelas }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">{{ $terbitan->tugas->nama_tugas }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">
        {{ $terbitan->token }}</td>
    <td class="px-6 py-4 whitespace-nowrap">
        {{ $terbitan->durasi_menit ? $terbitan->durasi_menit . ' Menit' : 'Tidak Dibatasi' }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $terbitan->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            @if ($terbitan->status === 'aktif')
                Active
            @elseif ($terbitan->status === 'ditutup')
                Expired
            @else
                {{ ucfirst($terbitan->status) }}
            @endif
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
       
        <button type="button" 
            class="open-detail-modal text-indigo-600 hover:text-indigo-900 font-semibold"
            data-id="{{ $terbitan->id }}"
            data-status="{{ $terbitan->status }}"
            data-tugas-nama="{{ $terbitan->tugas->nama_tugas }}">
            Detail
        </button>
    </td>
</tr>