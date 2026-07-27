<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat TOEFL - {{ $result->user->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #FAF8F2;
            color: #1e293b;
        }
        .sheet {
            width: 297mm;
            height: 210mm;
            padding: 40px 60px;
            box-sizing: border-box;
            text-align: center;
        }
        .logo {
            width: 70px;
            height: 70px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .brand-sub {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #475569;
            margin-top: 4px;
            margin-bottom: 24px;
        }
        .main-title {
            font-family: 'DejaVu Serif', serif;
            font-size: 34px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 18px;
        }
        .awarded-to {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 6px;
        }
        .student-name {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .student-npm {
            font-size: 11px;
            letter-spacing: 2px;
            color: #475569;
            margin-bottom: 24px;
        }
        table.score-table {
            width: 85%;
            margin: 0 auto 12px auto;
            border-collapse: collapse;
            font-size: 13px;
        }
        table.score-table td {
            border: 1px solid #1e293b;
            padding: 10px 18px;
            text-align: left;
        }
        table.score-table td.label {
            color: #334155;
        }
        table.score-table td.value {
            text-align: right;
            font-weight: 700;
            font-size: 15px;
        }
        table.score-table td.total-label {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table.score-table td.total-value {
            text-align: right;
            font-weight: 900;
            font-size: 18px;
            background-color: #f1f5f9;
        }
        .footnote {
            width: 85%;
            margin: 0 auto 20px auto;
            text-align: left;
            font-size: 9px;
            color: #475569;
            line-height: 1.4;
        }
        .valid-until {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 40px;
        }
        table.signatures {
            width: 100%;
            margin-top: 20px;
        }
        table.signatures td {
            width: 33%;
            text-align: center;
            vertical-align: bottom;
        }
        .sign-name {
            font-family: 'DejaVu Serif', serif;
            font-style: italic;
            font-size: 22px;
            color: #334155;
            margin-bottom: 4px;
        }
        .sign-line {
            border-top: 1px solid #1e293b;
            padding-top: 4px;
            font-size: 11px;
            font-weight: 700;
        }
        .stamp-box {
            width: 70px;
            height: 70px;
            border: 1px dashed #94a3b8;
            margin: 0 auto;
            text-align: center;
            line-height: 70px;
            font-size: 8px;
            letter-spacing: 1px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <img class="logo" src="{{ public_path('images/logo.webp') }}" alt="Logo">
        <div class="brand-title">TOEFL PIKSI V12</div>
        <div class="brand-sub">Official Score Report</div>

        <div class="main-title">Certificate of Achievement</div>

        <div class="awarded-to">Awarded To</div>
        <div class="student-name">{{ $result->user->name }}</div>
        <div class="student-npm">NPM: {{ $result->user->npm ?? '-' }}</div>

        <table class="score-table">
            <tr>
                <td class="label">Listening Comprehension:<span class="value" style="float:right;">{{ $result->correct_listening }}</span></td>
                <td class="label">Structure &amp; Written Expression:<span class="value" style="float:right;">{{ $result->correct_structure }}</span></td>
            </tr>
            <tr>
                <td class="label">Reading Comprehension:<span class="value" style="float:right;">{{ $result->correct_reading }}</span></td>
                <td class="total-label">Total Score:<span class="total-value" style="float:right;">{{ $result->score_total }}</span></td>
            </tr>
        </table>

        <div class="footnote">
            * Total score scaled range: 310-677<br>
            ** Valid for institutional testing program only
        </div>

        <div class="valid-until">
            Valid until: {{ $result->submitted_at->addYears(2)->format('F d, Y') }}
        </div>

        <table class="signatures">
            <tr>
                <td>
                    <div class="sign-name">Rangga M. I</div>
                    <div class="sign-line">Signature</div>
                </td>
                <td>
                    <div class="stamp-box">STAMP</div>
                </td>
                <td>
                    <div class="sign-name">Director</div>
                    <div class="sign-line">Signature</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>