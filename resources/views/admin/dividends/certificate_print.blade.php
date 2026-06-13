<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Dividen - {{ $shareholder->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Times New Roman', serif;
        }
        .page {
            width: 210mm;
            height: 297mm;
            background-color: white;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 15mm; /* Outer border spacing */
            box-sizing: border-box;
        }
        
        /* The decorative border */
        .border-inner {
            width: 100%;
            height: 100%;
            border: 8px solid #2c5282; /* Blue outer */
            padding: 4px;
            box-sizing: border-box;
            position: relative;
        }
        .border-inner::after {
            content: '';
            position: absolute;
            top: 2px; bottom: 2px; left: 2px; right: 2px;
            border: 2px solid #d4af37; /* Gold inner */
            pointer-events: none;
        }

        /* Top Information */
        .top-info {
            position: absolute;
            top: 25mm;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 25mm;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            font-weight: bold;
            z-index: 10;
        }
        .cert-number {
            color: #b91c1c; /* Red color as in original */
        }
        
        /* Logo */
        .logo-container {
            text-align: center;
            margin-top: 15mm;
        }
        .logo-container img {
            height: 120px;
            object-fit: contain;
        }

        .cert-title {
            text-align: center;
            margin-top: 5mm;
        }
        .cert-title h1 {
            font-size: 24pt;
            margin: 0;
            color: #111827;
            text-transform: uppercase;
        }
        .cert-title h2 {
            font-size: 20pt;
            margin: 5px 0 0 0;
            color: #1f2937;
            text-transform: uppercase;
        }
        
        .intro-text {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin-top: 10mm;
            padding: 0 30mm;
        }

        /* Center content area */
        .center-content {
            margin-top: 5mm;
            text-align: center;
            position: relative;
        }
        
        /* Rosette/Guilloche Background for Percentage */
        .rosette-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(147,197,253,0.3) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 1;
            /* Extra decorative circles */
            border: 2px dashed rgba(59,130,246,0.2);
            box-shadow: inset 0 0 20px rgba(59,130,246,0.1);
        }

        .percentage-value {
            font-size: 90pt;
            font-weight: 900;
            color: #1e3a8a;
            margin: 0;
            line-height: 1.2;
            position: relative;
            z-index: 2;
        }

        .percentage-text-ribbon {
            display: inline-block;
            background-color: #2c5282;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 14pt;
            font-weight: bold;
            padding: 8px 40px;
            margin-top: -10px;
            letter-spacing: 2px;
            position: relative;
            z-index: 3;
        }
        /* Ribbon Ends */
        .percentage-text-ribbon::before,
        .percentage-text-ribbon::after {
            content: '';
            position: absolute;
            top: 0;
            border-bottom: 20px solid transparent;
            border-top: 20px solid transparent;
        }
        .percentage-text-ribbon::before {
            left: -20px;
            border-right: 20px solid #2c5282;
        }
        .percentage-text-ribbon::after {
            right: -20px;
            border-left: 20px solid #2c5282;
        }

        .shareholder-title {
            margin-top: 15mm;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #4b5563;
        }
        .shareholder-name {
            font-size: 24pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .shareholder-id {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #4b5563;
            margin-bottom: 15px;
        }
        .shareholder-desc {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #000;
            margin: 0 auto;
            max-width: 80%;
            text-align: justify;
            text-align-last: center;
        }

        /* Signatures Area */
        .signatures {
            position: absolute;
            bottom: 50mm;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 35mm;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
        }
        
        .signature-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 45%;
        }
        
        .signature-space {
            height: 25mm; /* Empty space for signature */
        }
        
        .signature-line {
            width: 100%;
            border-bottom: 1px dotted #000;
            margin-bottom: 5px;
        }

        /* Bottom Footer (QR & Golden Seal) */
        .footer-area {
            position: absolute;
            bottom: 15mm;
            width: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .qr-code {
            width: 25mm;
            height: 25mm;
            margin-bottom: 5px;
        }
        
        .qr-text {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .qr-link {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #4b5563;
        }

        .golden-seal {
            position: absolute;
            bottom: 45mm;
            left: 50%;
            transform: translateX(-50%);
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #d4af37, #fde047, #d4af37);
            border-radius: 50%;
            border: 2px dashed #a16207;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 8pt;
            font-weight: bold;
            color: #713f12;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Hide elements during print */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { background-color: white; }
            .page { box-shadow: none; margin: 0; }
            .print-btn { display: none; }
        }

        /* Print Button Floating */
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            z-index: 100;
            font-family: sans-serif;
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Cetak Sertifikat</button>

    <div class="page">
        <div class="border-inner">
            <!-- Top Info -->
            <div class="top-info">
                <div style="text-align: left;">
                    <div>No. Sertifikat:</div>
                    <div class="cert-number">JF-{{ (int)$shareholder->percentage }}%-{{ \Carbon\Carbon::parse($shareholder->issue_date)->format('Y') }}-{{ substr($shareholder->certificate_id, -4) }}</div>
                </div>
                <div style="text-align: right;">
                    <div>Tanggal Penerbitan:</div>
                    <div>{{ \Carbon\Carbon::parse($shareholder->issue_date)->translatedFormat('d F Y') }}</div>
                </div>
            </div>

            <!-- Logo -->
            <div class="logo-container">
                @if($shareholder->division && $shareholder->division->logo)
                    <img src="{{ asset('media/divisions/' . $shareholder->division->logo) }}" alt="Logo {{ $shareholder->division->name }}">
                @else
                    <img src="{{ asset('images/template_sertifikat_v2.png') }}" alt="Logo Jostru">
                @endif
            </div>

            <div class="cert-title">
                <h1>Sertifikat Kepemilikan</h1>
                <!-- Nanti akan dinamis sesuai divisi, sementara pakai Jostru Farm -->
                <h2>{{ $shareholder->division ? 'Jostru ' . $shareholder->division->name : 'Jostru Farm' }}</h2>
            </div>

            <div class="intro-text">
                Dokumen ini merupakan bukti sah kepemilikan atas bagian dari {{ $shareholder->division ? 'Jostru ' . $shareholder->division->name : 'Jostru Farm' }} dengan persentase kepemilikan sebagai berikut:
            </div>

            <!-- Center Details -->
            <div class="center-content">
                <div class="rosette-bg"></div>
                <h1 class="percentage-value">{{ (int)$shareholder->percentage }}%</h1>
                <div class="percentage-text-ribbon">{{ strtoupper($shareholder->percentage_text) }}</div>

                <div class="shareholder-title">Sertifikat ini diberikan kepada:</div>
                <div class="shareholder-name">{{ $shareholder->name }}</div>
                <div class="shareholder-id">ID Pemegang: {{ $shareholder->certificate_id }}</div>

                <div class="shareholder-desc">
                    Pemegang sertifikat ini memiliki hak atas {{ (int)$shareholder->percentage }}% ({{ strtolower($shareholder->percentage_text) }}) dari keseluruhan kepemilikan {{ $shareholder->division ? 'Jostru ' . $shareholder->division->name : 'Jostru Farm' }}, termasuk hak atas pembagian keuntungan (dividen), hak suara dalam keputusan perusahaan sesuai dengan persentase kepemilikan, dan hak lainnya sebagaimana diatur dalam Anggaran Dasar Perusahaan.
                </div>
            </div>

            <!-- Golden Seal -->
            <div class="golden-seal">ASLI</div>

            <!-- Signatures (Empty lines) -->
            <div class="signatures">
                <div class="signature-block">
                    <div>DIREKTUR UTAMA</div>
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div>{{ $shareholder->division ? 'Jostru ' . $shareholder->division->name : 'Jostru Farm' }}</div>
                </div>
                
                <div class="signature-block">
                    <div>KOMISARIS UTAMA</div>
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div>{{ $shareholder->division ? 'Jostru ' . $shareholder->division->name : 'Jostru Farm' }}</div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="footer-area">
                <img src="{{ $qrUrl }}" alt="QR Code" class="qr-code">
                <div class="qr-text">Verifikasi Sertifikat</div>
                <div class="qr-link">Pindai QR Code untuk verifikasi keaslian sertifikat</div>
                <div class="qr-link" style="color: #2563eb;">atau kunjungi: {{ url('/verify-cert/' . $shareholder->certificate_id) }}</div>
            </div>
        </div>
    </div>
</body>
</html>
