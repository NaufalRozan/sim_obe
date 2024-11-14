<!DOCTYPE html>
<html>

<head>
    <title>Export PDF - {{ $mkName }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>{{ $mkName }}</h1>

    @foreach ($cpls as $cpl)
        <h2>{{ $cpl['nama_cpl'] }}</h2>
        <ul>
            @foreach ($cpl['cpmks'] as $kode_cpmk => $deskripsi)
                <li><strong>{{ $kode_cpmk }}:</strong> {{ $deskripsi }}</li>
            @endforeach
        </ul>
    @endforeach

    <h2>Grafik Nilai Rata-Rata CPMK</h2>
    <img src="{{ $chartUrl }}" alt="Grafik Nilai Rata-Rata CPMK" width="400" height="200">

    <h2>Laporan</h2>
    @if ($reports->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>CPMK Kode</th>
                    <th>Faktor Pendukung dan Kendala</th>
                    <th>RTL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->cpmk->kode_cpmk ?? '-' }}</td>
                        <td>{{ $report->faktor_pendukung_kendala }}</td>
                        <td>{{ $report->rtl }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada laporan yang tersedia untuk MK Ditawarkan ini.</p>
    @endif
</body>

</html>
