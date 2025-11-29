@extends('layouts.app')

@section('title', 'Buku Besar - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buku Besar</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('bukubesar.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="kode_akun" class="form-label">Akun</label>
                    <select class="form-select" id="kode_akun" name="kode_akun" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($akunList as $a)
                            <option value="{{ $a->kode_akun }}" {{ $kodeAkun == $a->kode_akun ? 'selected' : '' }}>
                                {{ $a->kode_akun }} - {{ $a->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedAkun)
        <div class="card">
            <div class="card-header">
                <strong>{{ $selectedAkun->kode_akun }} - {{ $selectedAkun->nama_akun }}</strong>
                <span class="float-end">Saldo Normal: {{ $selectedAkun->saldo_normal }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No Transaksi</th>
                            <th>Keterangan</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="fw-bold">Saldo Awal</td>
                            <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 2, ',', '.') }}</td>
                        </tr>
                        @php $saldo = $saldoAwal; @endphp
                        @foreach($transaksi as $t)
                            @php
                                if ($selectedAkun->saldo_normal == 'Debit') {
                                    $saldo += $t->debit - $t->kredit;
                                } else {
                                    $saldo += $t->kredit - $t->debit;
                                }
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->jurnal->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('jurnal.show', $t->jurnal->id_jurnal) }}" class="text-decoration-none">
                                        {{ $t->jurnal->no_transaksi }}
                                    </a>
                                </td>
                                <td>{{ $t->jurnal->deskripsi }}</td>
                                <td class="text-end">{{ $t->debit > 0 ? number_format($t->debit, 2, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $t->kredit > 0 ? number_format($t->kredit, 2, ',', '.') : '-' }}</td>
                                <td class="text-end">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Saldo Akhir</td>
                            <td class="text-end fw-bold">Rp {{ number_format($saldo, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
