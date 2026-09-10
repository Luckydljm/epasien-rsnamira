<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Operasi - {{ $lap->no_rawat }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 12px;
            color: #333;
        }
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 10px 0 15px 0;
            letter-spacing: 1px;
        }
        .table-data {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .border-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 250px;
            white-space: pre-wrap;
            font-family: inherit;
            line-height: 1.6;
            margin-top: 5px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm font-bold shadow-sm">
            <i class="bi bi-printer"></i> Cetak Dokumen
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm ms-1 font-bold">
            Tutup
        </button>
    </div>

    <!-- Header RS -->
    <table class="header-table">
        <tr>
            <td style="width: 80px; text-align: center; vertical-align: middle;">
                <div style="width: 60px; height: 60px; border: 2px solid #0d7044; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #0d7044; font-weight: bold; font-size: 20px;">
                    RSN
                </div>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div class="header-title">{{ $setting->nama_instansi ?? 'RS NAMIRA' }}</div>
                <div class="header-sub">{{ $setting->alamat_instansi ?? 'Jl. KH. Ahmad Dahlan No. 1, Pancor, Selong' }}, {{ $setting->kabupaten ?? 'Lombok Timur' }}</div>
                <div class="header-sub">Kontak: {{ $setting->kontak ?? '(0376) 21123' }} | Email: {{ $setting->email ?? 'rsnamira@gmail.com' }}</div>
            </td>
            <td style="width: 80px;"></td>
        </tr>
    </table>

    <div class="report-title">LAPORAN OPERASI</div>

    <!-- Identitas Pasien -->
    <table class="table-data" style="border: 1px solid #000; margin-bottom: 12px;">
        <tr>
            <td style="width: 18%;"><strong>No. Rawat</strong></td>
            <td style="width: 32%;">: {{ $lap->no_rawat }}</td>
            <td style="width: 18%;"><strong>Nama Pasien</strong></td>
            <td style="width: 32%;">: <strong>{{ $lap->nm_pasien }}</strong></td>
        </tr>
        <tr>
            <td><strong>No. Rekam Medis</strong></td>
            <td>: {{ $lap->no_rkm_medis }}</td>
            <td><strong>Jenis Kelamin / Umur</strong></td>
            <td>: {{ $lap->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ $lap->umurdaftar }} {{ $lap->sttsumur }}</td>
        </tr>
        <tr>
            <td><strong>Ruang / Poliklinik</strong></td>
            <td>: {{ $lap->nm_poli ?? '-' }}</td>
            <td><strong>Dokter Operator</strong></td>
            <td>: <strong>{{ $lap->operator ?? '-' }}</strong></td>
        </tr>
    </table>

    <!-- Data Tindakan Operasi -->
    <table class="table-data" style="border: 1px solid #000; margin-bottom: 12px;">
        <tr>
            <td style="width: 25%;"><strong>Tanggal Operasi</strong></td>
            <td style="width: 25%;">: {{ date('d-m-Y', strtotime($lap->tgl_operasi ?? $lap->tanggal)) }}</td>
            <td style="width: 25%;"><strong>Waktu Mulai - Selesai</strong></td>
            <td style="width: 25%;">: {{ date('H:i', strtotime($lap->tgl_operasi ?? $lap->tanggal)) }} - {{ date('H:i', strtotime($lap->selesaioperasi ?? $lap->tanggal)) }} WIB</td>
        </tr>
        <tr>
            <td><strong>Diagnosa Pre-Operasi</strong></td>
            <td>: <strong>{{ $lap->diagnosa_preop ?? '-' }}</strong></td>
            <td><strong>Diagnosa Post-Operasi</strong></td>
            <td>: <strong>{{ $lap->diagnosa_postop ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jaringan yang Dieksisi</strong></td>
            <td>: {{ $lap->jaringan_dieksekusi ?? '-' }}</td>
            <td><strong>Pemeriksaan PA</strong></td>
            <td>: <strong>{{ $lap->permintaan_pa ?? 'Tidak' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jenis Anestesi</strong></td>
            <td>: {{ $lap->jenis_anasthesi ?? '-' }}</td>
            <td><strong>Kategori Operasi</strong></td>
            <td>: {{ $lap->kategori ?? '-' }}</td>
        </tr>
    </table>

    <!-- Uraian Operasi -->
    <div style="font-weight: bold; margin-bottom: 4px; text-transform: uppercase;">
        Uraian Jalannya Operasi / Prosedur Pembedahan:
    </div>
    <div class="border-box">
{{ $lap->laporan_operasi }}
    </div>

    <!-- Tanda Tangan Operator -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div>{{ $setting->kabupaten ?? 'Lombok Timur' }}, {{ date('d F Y', strtotime($lap->tanggal)) }}</div>
                <div style="margin-top: 5px;">Dokter Bedah / Operator,</div>
                <div style="height: 70px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $lap->operator ?? 'Dokter Spesialis Bedah' }}</div>
                <div style="font-size: 11px;">SIP / NIP: .......................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
