<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Surat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #003B4B;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .filter-info {
            margin-bottom: 15px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .section-title {
            background-color: #003B4B;
            color: white;
            padding: 8px;
            margin: 20px 0 10px;
            font-size: 14px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN SURAT MASUK DAN SURAT KELUAR</h1>
        <p>SD N 2 BUMIREJO</p>
    </div>

    @if ($filterApplied)
        <div class="filter-info">
            Filter:
            @if ($bulan && $tahun)
                Bulan {{ $bulan }} Tahun {{ $tahun }}
            @elseif($tahun)
                Tahun {{ $tahun }}
            @elseif($bulan)
                Bulan {{ $bulan }}
            @endif
        </div>
    @endif

    @if (!$suratMasuks->isEmpty())
        <div class="section-title">SURAT MASUK</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Surat</th>
                    <th>Nomor Surat</th>
                    <th>Pengirim</th>
                    <th>Tanggal Masuk</th>
                    <th>Perihal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suratMasuks as $index => $surat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $surat->kode_surat }}</td>
                        <td>{{ $surat->nomor_surat }}</td>
                        <td>{{ $surat->pengirim }}</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal_masuk)->format('d-m-Y') }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td>{{ $surat->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="section-title">SURAT MASUK</div>
        <p>Tidak ada data surat masuk.</p>
    @endif

    @if (!$suratKeluars->isEmpty())
        <div class="section-title">SURAT KELUAR</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Surat</th>
                    <th>Nomor Surat</th>
                    <th>Tujuan</th>
                    <th>Tanggal Keluar</th>
                    <th>Perihal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suratKeluars as $index => $surat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $surat->kode_surat }}</td>
                        <td>{{ $surat->nomor_surat }}</td>
                        <td>{{ $surat->tujuan }}</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal_keluar)->format('d-m-Y') }}</td>
                        <td>{{ $surat->perihal }}</td>
                        <td>{{ $surat->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="section-title">SURAT KELUAR</div>
        <p>Tidak ada data surat keluar.</p>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>

</html>
