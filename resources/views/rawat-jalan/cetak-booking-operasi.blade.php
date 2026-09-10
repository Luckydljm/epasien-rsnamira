<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jadwal Operasi - {{ $booking->no_rawat }}</title>
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
            padding: 5px 8px;
            vertical-align: top;
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
            <i class="bi bi-printer"></i> Cetak Jadwal
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

    <div class="report-title">SURAT JADWAL / BOOKING OPERASI</div>

    <!-- Data Pasien & Operasi -->
    <table class="table-data" style="border: 1px solid #000; margin-bottom: 15px;">
        <tr>
            <td style="width: 25%;"><strong>No. Rawat</strong></td>
            <td style="width: 75%;">: {{ $booking->no_rawat }}</td>
        </tr>
        <tr>
            <td><strong>No. Rekam Medis</strong></td>
            <td>: {{ $booking->no_rkm_medis }}</td>
        </tr>
        <tr>
            <td><strong>Nama Pasien</strong></td>
            <td>: <strong>{{ $booking->nm_pasien }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jenis Kelamin / Umur</strong></td>
            <td>: {{ $booking->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ $booking->umurdaftar }} {{ $booking->sttsumur }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pelaksanaan Operasi</strong></td>
            <td>: <strong>{{ date('d F Y', strtotime($booking->tanggal)) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jam Rencana Operasi</strong></td>
            <td>: <strong>{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} WITA / WIB</strong></td>
        </tr>
        <tr>
            <td><strong>Ruang Kamar Bedah (OK)</strong></td>
            <td>: <strong>{{ $booking->nm_ruang_ok ?? $booking->kd_ruang_ok }}</strong></td>
        </tr>
        <tr>
            <td><strong>Paket Tindakan Bedah / Operasi</strong></td>
            <td>: <strong>{{ $booking->nama_paket ?? $booking->kode_paket }}</strong> ({{ $booking->kode_paket }}) {{ $booking->kelas ? ' - Kelas ' . $booking->kelas : '' }}</td>
        </tr>
        <tr>
            <td><strong>Dokter Operator</strong></td>
            <td>: <strong>{{ $booking->operator ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Status Pelaksanaan</strong></td>
            <td>: <span style="font-weight: bold; text-transform: uppercase;">{{ $booking->status ?? 'Menunggu' }}</span></td>
        </tr>
    </table>

    <div style="font-size: 11px; margin-bottom: 20px; line-height: 1.5; color: #444;">
        <em>Catatan Penting Persiapan Operasi:</em><br>
        1. Pasien dianjurkan berpuasa minimal 6 - 8 jam sebelum jadwal pembedahan sesuai instruksi DPJP/Anestesi.<br>
        2. Mohon hadir di ruang persiapan operasi minimal 1 jam sebelum jadwal yang telah ditentukan.<br>
        3. Pastikan kelengkapan pemeriksaan penunjang (Laboratorium Darah Lengkap, Foto Thorax, EKG, dll) telah tersedia.
    </div>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center;">
                <div>Pasien / Keluarga Pasien,</div>
                <div style="height: 60px;"></div>
                <div>( .................................................. )</div>
            </td>
            <td style="width: 50%; text-align: center;">
                <div>{{ $setting->kabupaten ?? 'Lombok Timur' }}, {{ date('d F Y') }}</div>
                <div style="margin-top: 4px;">Dokter Penanggung Jawab / Operator,</div>
                <div style="height: 60px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $booking->operator ?? 'Dokter Operator' }}</div>
                <div style="font-size: 11px;">SIP / NIP: .......................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
