<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>รายงานการจองคิว - คลินิกแพทย์แผนไทย</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 16px;
            border-bottom: 2px solid #644986;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 14pt;
            color: #644986;
            margin: 0 0 4px 0;
        }
        .header p {
            margin: 0;
            font-size: 8.5pt;
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            border-radius: 4px;
            background: #e2e8f0;
            color: #334155;
        }
        .footer {
            margin-top: 20px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php
        $clinicName = \App\Models\Setting::get('clinic_name_th', 'คลินิกแพทย์แผนไทย');
        $hospitalName = \App\Models\Setting::get('hospital_name', 'โรงพยาบาลศูนย์การแพทย์ มหาวิทยาลัยวลัยลักษณ์');
    @endphp

    <div class="header">
        <h1>รายงานสรุปการจองคิวการรักษา</h1>
        <p>{{ $clinicName }} · {{ $hospitalName }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>ช่วงวันที่รายงาน:</strong> {{ $filters['date_from'] ?? 'ทั้งหมด' }} ถึง {{ $filters['date_to'] ?? 'ทั้งหมด' }}</td>
            <td class="text-right"><strong>วันที่ออกรายงาน:</strong> {{ date('d/m/Y H:i น.') }}</td>
        </tr>
        <tr>
            <td><strong>จำนวนรายการทั้งหมด:</strong> {{ number_format(count($bookings)) }} รายการ</td>
            <td class="text-right"><strong>ออกโดย:</strong> {{ auth('admin')->user()?->name ?? 'ระบบ' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">รหัสจอง</th>
                <th style="width: 10%;">วันที่</th>
                <th style="width: 12%;">รอบเวลา</th>
                <th style="width: 16%;">ผู้รับบริการ</th>
                <th style="width: 12%;">เบอร์โทร</th>
                <th style="width: 18%;">หัตถการ</th>
                <th style="width: 12%;">หมอนวด/แพทย์</th>
                <th style="width: 10%;" class="text-center">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td><strong>{{ $booking->booking_no }}</strong></td>
                    <td>{{ $booking->booking_date?->format('d/m/Y') }}</td>
                    <td>{{ $booking->timeSlot?->label_th ?? ($booking->timeSlot ? substr($booking->timeSlot->start_time, 0, 5).' - '.substr($booking->timeSlot->end_time, 0, 5) : '—') }}</td>
                    <td>{{ $booking->customer?->full_name }}</td>
                    <td>{{ $booking->customer?->phone }}</td>
                    <td>{{ $booking->service?->name_th }}</td>
                    <td>{{ $booking->therapist?->displayName ?? 'จัดให้อัตโนมัติ' }}</td>
                    <td class="text-center">
                        <span class="badge">{{ $booking::STATUSES[$booking->status] ?? $booking->status }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">ไม่พบรายการจองตามเงื่อนไขที่เลือก</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        เอกสารนี้สร้างโดยระบบจัดการคลินิกแพทย์แผนไทย มหาวิทยาลัยวลัยลักษณ์ เมื่อ {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
