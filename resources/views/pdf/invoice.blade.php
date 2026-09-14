

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * {
        font-family: 'DejaVu Sans', sans-serif; /* DejaVu Sans comes pre-installed with DomPDF and supports Arabic characters */
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            margin: 0; 
            padding: 10px; 
            direction: rtl;
            text-align: right;
        }
        .invoice-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .invoice-table th { background: #2c6b9e; color: #fff; padding: 8px; text-align: center; }
        .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
  
.logo-wrapper {
    text-align: center;
    width: 100%;
}
.invoice-logo {
    width: 85%; /* Or any desired size */
    height: auto;
    display: inline-block;
    margin-bottom: 10px;
}
    </style>
</head>
<body>
<div class="logo-wrapper">
    @if(file_exists(public_path('com.jpeg')))
        <img src="{{ public_path('com.jpeg') }}" class="invoice-logo" alt="Logo">
    @endif
</div>
    <table class="invoice-table" style="margin-top: 10%;">
        <thead>
            <tr>    
                <th>السادة / شركة الخليج الدولي للرعاية الصحية</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><div>اسم المريض: {{ $member->first_name }} {{ $member->last_name }}</div></td>
                <td><div>رقم الفاتورة: {{ $invoice_no }}</div></td>
            </tr>
            <tr>
                <td><div>التاريخ: {{ $date }}</div></td>
                <td><div>رقم التعريف: {{ $member->member_ID }}</div></td>
            </tr>
        </tbody>
    </table>

    <table class="invoice-table">
    <thead>
        <tr>
            <th width="10%">#</th>
            <th width="50%">اسم الدواء</th>
            <th width="15%">الجرعة القياسية</th>
            <th width="10%">الكمية</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medications as $index => $med)
            <tr>
                <td>{{ $index + 1 }}</td>
                <!-- Use medical_name instead of name -->
                <td>{{ $med->medical_name }}</td> 
                <!-- Use dosages instead of dosage -->
                <td>{{ $med->dosages ?? '-' }}</td> 
                <!-- Access amount from the pivot relation -->
                <td>{{ $med->pivot->amount ?? 1 }}</td> 
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center;">لا توجد أدوية مسجلة</td>
            </tr>
        @endforelse
    </tbody>
</table>

    <table class="invoice-table">
        <thead>
            <tr>
                <th style="text-align: right">
                    الإجمالي بالدينار الليبي: {{ number_format($total_price, 2) }}
                </th>
            </tr>
        </thead>
    </table>
</body>
</html>