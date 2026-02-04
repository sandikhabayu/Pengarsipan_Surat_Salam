<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat {{ $jenis_surat == 'kepala_desa' ? 'Kepala Desa' : 'Sekretariat' }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* PRESERVE STYLES FROM EDITOR */
        .isi-surat p,
        .isi-surat div {
            margin: 10px 0;
        }

        /* Specific alignment - IMPORTANT untuk preserve styling */
        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-justify {
            text-align: justify !important;
        }

        /* Table styling - preserve from editor */
        .isi-surat table {
            border-collapse: collapse !important;
            margin: 10px 0 !important;
            page-break-inside: avoid !important;
        }

        .isi-surat table td,
        .isi-surat table th {
            padding: 5px 8px !important;
            vertical-align: top !important;
        }

        /* Preserve border styles */
        .isi-surat table[border="0"] {
            border: none !important;
        }

        .isi-surat table[border="0"] td,
        .isi-surat table[border="0"] th {
            border: none !important;
        }

        .isi-surat table[border="1"] {
            border: 1px solid #000 !important;
        }

        .isi-surat table[border="1"] td,
        .isi-surat table[border="1"] th {
            border: 1px solid #000 !important;
        }

        /* Preserve custom widths */
        .isi-surat table[width] {
            width: attr(width) !important;
        }

        .isi-surat td[width],
        .isi-surat th[width] {
            width: attr(width) !important;
        }

        /* Inline styling preservation */
        .isi-surat *[style*="text-align"] {
            text-align: inherit !important;
        }

        .isi-surat strong,
        .isi-surat b {
            font-weight: bold !important;
        }

        .isi-surat em,
        .isi-surat i {
            font-style: italic !important;
        }

        .isi-surat u {
            text-decoration: underline !important;
        }

        /* Kop Surat tetap */
        .kop-surat {
            text-align: center !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .kop-surat h3 {
            margin: 2px 0;
            padding: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .kop-surat h4 {
            margin: 5px 0;
            padding: 0;
            font-size: 16pt;
            font-weight: bold;
        }

        /* Header surat */
        .header {
            margin-bottom: 30px;
        }

        /* Penandatangan */
        .penandatangan {
            float: right;
            text-align: center;
            margin-top: 100px;
            width: 300px;
        }

        /* Untuk mencegah page break di tengah tabel */
        table {
            page-break-inside: avoid !important;
        }

        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <h3>PEMERINTAH KABUPATEN WONOSOBO</h3>
        <h3>KECAMATAN KALIBAWANG</h3>
        <h3>DESA SUKOHARJO</h3>
        <h4>{{ $jenis_surat == 'kepala_desa' ? 'SURAT KEPALA DESA' : 'SURAT SEKRETARIAT' }}</h4>
    </div>

    <!-- Nomor Surat -->
    <div class="header">
        <p>Nomor : {{ $surats->nomor_surat }}</p>
        <p>Lampiran : {{ $surats->lampiran }}</p>
        <p>Perihal &nbsp;&nbsp;&nbsp;&nbsp;: {{ $surats->perihal }}</p>
    </div>

    <!-- Tujuan Surat -->
    <div style="margin: 20px 0;">
        <p>Kepada Yth:</p>
        <p><strong>{{ $surats->kepada }}</strong></p>
        <p>Di Tempat</p>
    </div>

    <!-- Isi Surat - Style dari editor dipertahankan -->
    <div class="isi-surat">
        {!! $surats->isi_surat !!}
    </div>

    <!-- Penandatangan -->
    <div class="penandatangan">
        <p>Salam, {{ \Carbon\Carbon::parse($surats->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p>Pj. Kepala Desa Salam</p>

        <div style="margin-top: 80px;">
            <p><strong>BAMBANG LISTIONO AGUS, P.S.Sos</strong></p>
            <p>Pembina / IVa</p>
            <p>NIP. 196808111989031008</p>
        </div>
    </div>
</body>

</html>
