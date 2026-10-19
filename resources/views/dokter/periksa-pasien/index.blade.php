<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">No Antrian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($daftarPasien as $item)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            {{-- ID --}}
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Nama Pasien --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $item->pasien->nama ?? $item->pasien->name ?? '-' }}
                            </td>

                            {{-- Keluhan --}}
                            <td class="px-6 py-4 text-slate-600">
                                {{ $item->keluhan ?? '-' }}
                            </td>

                            {{-- No Antrian --}}
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $item->no_antrian }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end">
                                    @if($item->periksas && $item->periksas->isNotEmpty())
                                        {{-- Status Sudah Diperiksa --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            Sudah Diperiksa
                                        </span>
                                    @else
                                        {{-- Button Periksa --}}
                                        <a href="{{ route('periksa-pasien.create', $item->id) }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                            <i class="fas fa-stethoscope text-xs"></i>
                                            Periksa
                                        </a>
                                    @endif
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-clipboard-user text-3xl"></i>
                                    <span>Belum ada antrian pasien</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>

