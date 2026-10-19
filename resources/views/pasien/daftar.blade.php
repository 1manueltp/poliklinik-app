<x-layouts.app title="Daftar Poli">

    <div class="max-w-2xl mx-auto py-4">

        {{-- Alert Flash Message --}}
        @if (session('message'))
        <div class="alert alert-{{ session('type', 'success') }} mb-4 rounded-xl shadow-sm">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        {{-- Card Pendaftaran Poli --}}
        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-8 sm:p-10">

                {{-- Judul Form --}}
                <h2 class="text-xl font-bold text-slate-800 text-center mb-8 flex items-center justify-center gap-2">
                    <span>🗂️</span> Pendaftaran Poli
                </h2>

                <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                    @csrf

                    {{-- Hidden Input Pasien ID --}}
                    <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                    {{-- Nomor Rekam Medis --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nomor Rekam Medis
                        </label>
                        <input type="text" value="{{ $user->no_rm ?? '-' }}"
                            class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-700 cursor-not-allowed"
                            readonly>
                    </div>

                    {{-- Pilih Poli --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Pilih Poli
                        </label>
                        <select name="id_poli" id="id_poli"
                            class="select select-bordered border-2 w-full rounded-lg text-sm text-slate-700 focus:outline-none focus:border-primary @error('id_poli') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Poli --</option>
                            @foreach ($polis as $poli)
                                <option value="{{ $poli->id }}" {{ old('id_poli') == $poli->id ? 'selected' : '' }}>
                                    {{ $poli->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_poli')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Jadwal Periksa --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Pilih Jadwal Periksa
                        </label>
                        <select name="id_jadwal" id="id_jadwal"
                            class="select select-bordered border-2 w-full rounded-lg text-sm text-slate-700 focus:outline-none focus:border-primary @error('id_jadwal') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach ($jadwals as $jadwal)
                                <option value="{{ $jadwal->id }}"
                                    data-poli="{{ $jadwal->dokter->id_poli ?? '' }}"
                                    {{ old('id_jadwal') == $jadwal->id ? 'selected' : '' }}>
                                    {{ $jadwal->dokter->nama ?? 'Dokter' }} - {{ $jadwal->hari }} ({{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_jadwal')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keluhan --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Keluhan
                        </label>
                        <textarea name="keluhan" id="keluhan" rows="4" placeholder="Tulis keluhan anda..."
                            class="textarea textarea-bordered border-2 w-full rounded-lg text-sm focus:outline-none focus:border-primary @error('keluhan') border-red-500 @enderror">{{ old('keluhan') }}</textarea>
                        @error('keluhan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                            Daftar Poli
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- Script Filter Jadwal Berdasarkan Poli yang Dipilih --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectPoli = document.getElementById('id_poli');
            const selectJadwal = document.getElementById('id_jadwal');
            const allJadwalOptions = Array.from(selectJadwal.querySelectorAll('option:not([value=""])'));

            function filterJadwal() {
                const selectedPoliId = selectPoli.value;
                const currentJadwalValue = selectJadwal.value;

                selectJadwal.innerHTML = '<option value="">-- Pilih Jadwal --</option>';

                allJadwalOptions.forEach(option => {
                    const poliId = option.getAttribute('data-poli');
                    if (!selectedPoliId || poliId === selectedPoliId) {
                        selectJadwal.appendChild(option.cloneNode(true));
                    }
                });

                if (currentJadwalValue && selectJadwal.querySelector(`option[value="${currentJadwalValue}"]`)) {
                    selectJadwal.value = currentJadwalValue;
                }
            }

            selectPoli.addEventListener('change', function () {
                selectJadwal.value = '';
                filterJadwal();
            });

            if (selectPoli.value) {
                filterJadwal();
            }
        });
    </script>

</x-layouts.app>
