<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST" id="formPeriksa">
                @csrf

                {{-- Hidden Inputs --}}
                <input type="hidden" name="id_daftar_poli" value="{{ $id }}">
                <input type="hidden" name="obat_json" id="obat_json" value="[]">
                <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">

                {{-- Pilih Obat --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Pilih Obat <span class="text-red-500">*</span>
                    </label>
                    <select id="select_obat"
                        class="select select-bordered border-2 w-full rounded-lg text-sm text-slate-700 focus:outline-none focus:border-primary">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}" data-nama="{{ $obat->nama_obat }}"
                                data-harga="{{ $obat->harga }}" data-kemasan="{{ $obat->kemasan }}">
                                {{ $obat->nama_obat }} - {{ $obat->kemasan }} (Rp {{ number_format($obat->harga, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('obat_json')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Obat Terpilih --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Obat Terpilih
                    </label>
                    <div id="container_obat_terpilih"
                        class="flex flex-wrap gap-2 min-h-[48px] p-3 bg-slate-50 border border-slate-200 rounded-lg items-center">
                        <span id="placeholder_kosong" class="text-sm text-slate-400 italic">
                            Belum ada obat yang dipilih.
                        </span>
                    </div>
                </div>

                {{-- Total Harga --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Total Harga
                    </label>
                    <input type="text" id="display_total_harga" value="Rp 0"
                        class="input input-bordered w-full bg-slate-100 text-slate-800 font-semibold text-sm rounded-lg cursor-not-allowed"
                        readonly>
                </div>

                {{-- Catatan (Opsional) --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="catatan" id="catatan" rows="4" placeholder="Masukkan catatan..."
                        class="textarea textarea-bordered border-2 w-full rounded-lg text-sm focus:outline-none focus:border-primary @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-sm font-semibold transition shadow-sm">
                        <i class="fas fa-save text-xs"></i>
                        Simpan
                    </button>

                    <a href="{{ route('periksa-pasien.index') }}"
                        class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold transition">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let selectedObats = [];

            const selectObat = document.getElementById('select_obat');
            const containerObat = document.getElementById('container_obat_terpilih');
            const placeholderKosong = document.getElementById('placeholder_kosong');
            const displayTotalHarga = document.getElementById('display_total_harga');
            const inputObatJson = document.getElementById('obat_json');
            const inputBiayaPeriksa = document.getElementById('biaya_periksa');
            const formPeriksa = document.getElementById('formPeriksa');

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            // Tambah Obat saat dropdown dipilih
            selectObat.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const obatId = parseInt(this.value);

                if (!obatId) return;

                // Cek apakah obat sudah ada di daftar
                const isExisting = selectedObats.some(item => item.id === obatId);
                if (isExisting) {
                    alert('Obat ini sudah ditambahkan.');
                    this.value = '';
                    return;
                }

                const nama = selectedOption.getAttribute('data-nama');
                const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;
                const kemasan = selectedOption.getAttribute('data-kemasan') || '-';

                selectedObats.push({
                    id: obatId,
                    nama: nama,
                    harga: harga,
                    kemasan: kemasan
                });

                // Reset dropdown ke default
                this.value = '';

                renderObatTerpilih();
            });

            // Hapus Obat dari daftar
            window.hapusObat = function (id) {
                selectedObats = selectedObats.filter(item => item.id !== id);
                renderObatTerpilih();
            };

            // Render daftar obat & hitung total harga
            function renderObatTerpilih() {
                containerObat.innerHTML = '';

                if (selectedObats.length === 0) {
                    containerObat.appendChild(placeholderKosong);
                    displayTotalHarga.value = 'Rp 0';
                    inputObatJson.value = '[]';
                    inputBiayaPeriksa.value = 0;
                    return;
                }

                let total = 0;

                selectedObats.forEach(obat => {
                    total += obat.harga;

                    const chip = document.createElement('div');
                    chip.className = 'inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-xs font-semibold shadow-sm';
                    chip.innerHTML = `
                        <span>${obat.nama} - ${obat.kemasan} (Rp ${formatRupiah(obat.harga)})</span>
                        <button type="button" class="text-blue-400 hover:text-red-600 transition ml-1" onclick="hapusObat(${obat.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    containerObat.appendChild(chip);
                });

                displayTotalHarga.value = 'Rp ' + formatRupiah(total);
                inputObatJson.value = JSON.stringify(selectedObats.map(o => o.id));
                inputBiayaPeriksa.value = total;
            }

            // Validasi sebelum submit
            formPeriksa.addEventListener('submit', function (e) {
                if (selectedObats.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu obat untuk pasien.');
                }
            });
        });
    </script>

</x-layouts.app>

