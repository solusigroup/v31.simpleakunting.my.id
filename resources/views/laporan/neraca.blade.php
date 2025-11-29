@extends('layouts.app')

@section('title', 'Neraca - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Neraca</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.neraca') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="per_tanggal" class="form-label">Per Tanggal</label>
                    <input type="date" class="form-control" id="per_tanggal" name="per_tanggal" value="{{ $perTanggal }}">
                </div>
                <div class="col-md-4">
                    <label for="banding_tanggal" class="form-label">Bandingkan Dengan (Opsional)</label>
                    <input type="date" class="form-control" id="banding_tanggal" name="banding_tanggal" value="{{ $bandingTanggal }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.neraca') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-4">
        <h3>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan Belum Diset' }}</h3>
        <h4>Neraca</h4>
        <p class="text-muted">
            Per Tanggal {{ \Carbon\Carbon::parse($perTanggal)->format('d F Y') }}
            @if($bandingTanggal)
                vs {{ \Carbon\Carbon::parse($bandingTanggal)->format('d F Y') }}
            @endif
        </p>
    </div>

    <!-- Report Content -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50%">Keterangan</th>
                            <th class="text-end">{{ \Carbon\Carbon::parse($perTanggal)->format('d M Y') }}</th>
                            @if($bandingTanggal)
                                <th class="text-end">{{ \Carbon\Carbon::parse($bandingTanggal)->format('d M Y') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ASET -->
                        <tr class="fw-bold table-primary"><td colspan="{{ $bandingTanggal ? 3 : 2 }}">ASET</td></tr>
                        
                        <!-- Aset Lancar -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4">Aset Lancar</td></tr>
                        @foreach($asetLancar as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Aset Lancar</td>
                            <td class="text-end">Rp {{ number_format($asetLancar->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- Aset Tetap -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4 mt-2">Aset Tetap</td></tr>
                        @foreach($asetTetap as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Aset Tetap</td>
                            <td class="text-end">Rp {{ number_format($asetTetap->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->where('tipe_akun', 'Aset Tetap')->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- TOTAL ASET -->
                        <tr class="fw-bold table-success">
                            <td>TOTAL ASET</td>
                            <td class="text-end">Rp {{ number_format($asetLancar->sum('saldo_akhir') + $asetTetap->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format(
                                    $laporanBanding->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->sum('saldo_akhir') + 
                                    $laporanBanding->where('tipe_akun', 'Aset Tetap')->sum('saldo_akhir'), 
                                    2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- KEWAJIBAN & EKUITAS -->
                        <tr class="fw-bold table-primary mt-4"><td colspan="{{ $bandingTanggal ? 3 : 2 }}">KEWAJIBAN DAN EKUITAS</td></tr>

                        <!-- Kewajiban -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4">Kewajiban</td></tr>
                        @foreach($kewajiban as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Kewajiban</td>
                            <td class="text-end">Rp {{ number_format($kewajiban->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- Ekuitas -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4 mt-2">Ekuitas</td></tr>
                        @foreach($ekuitas as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <!-- Laba Rugi Tahun Berjalan -->
                        <tr>
                            <td class="ps-5">Laba Rugi Tahun Berjalan</td>
                            <td class="text-end">Rp {{ number_format($labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($labaRugiBerjalanBanding, 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Ekuitas</td>
                            <td class="text-end">Rp {{ number_format($ekuitas->sum('saldo_akhir') + $labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->where('tipe_akun', 'Ekuitas')->sum('saldo_akhir') + $labaRugiBerjalanBanding, 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- TOTAL KEWAJIBAN & EKUITAS -->
                        <tr class="fw-bold table-success">
                            <td>TOTAL KEWAJIBAN DAN EKUITAS</td>
                            <td class="text-end">Rp {{ number_format($kewajiban->sum('saldo_akhir') + $ekuitas->sum('saldo_akhir') + $labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format(
                                    $laporanBanding->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])->sum('saldo_akhir') + 
                                    $laporanBanding->where('tipe_akun', 'Ekuitas')->sum('saldo_akhir') + 
                                    $labaRugiBerjalanBanding, 
                                    2, ',', '.') }}</td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <div class="row mt-5 text-center">
                <div class="col-md-4 offset-md-2">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_direktur ?? '(....................)' }}</p>
                    <p>Direktur</p>
                </div>
                <div class="col-md-4">
                    <p>Dibuat Oleh,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_akuntan ?? '(....................)' }}</p>
                    <p>Akuntan</p>
                </div>
            </div>
        </div>
    </div>
@endsection
