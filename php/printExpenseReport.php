<!DOCTYPE html>
<html>
<head>
    <title>Daily Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.5in;
        }
        
        @media print {
            body {
                margin: 0.5in;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0.5in;
            padding: 0;
        }
        
        .header {
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
        }
        
        .report-title {
            font-size: 12pt;
            margin: 0;
        }
        
        .date-section {
            margin: 5px 0 15px 0;
        }
        
        .barcode {
            float: right;
            margin-top: -60px;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .main-table th {
            background-color: #000;
            color: #fff;
            padding: 5px;
            text-align: left;
            font-size: 9pt;
            border: 1px solid #000;
        }
        
        .main-table td {
            padding: 3px 5px;
            border: 1px solid #000;
            font-size: 9pt;
        }
        
        .main-table .item-col {
            width: 33%;
        }
        
        .main-table .amount-col {
            width: 13.5%;
            text-align: right;
        }
        
        .section-header {
            background-color: #000 !important;
            color: #fff !important;
            font-weight: bold;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .detail-section {
            margin-bottom: 15px;
        }
        
        .detail-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .detail-item {
            margin-left: 0;
            font-size: 9pt;
            line-height: 1.4;
        }
        
        .amount-right {
            float: right;
        }
        
        .weight-value {
            display: inline-block;
            width: 60px;
            text-align: right;
        }
        
        .price-value {
            display: inline-block;
            width: 80px;
            text-align: right;
            margin-left: 30px;
        }
        
        .detail-total {
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">MASJAYA SAWIT SDN BHD</h1>
        <p class="report-title">Daily Report</p>
        <div class="date-section">Date : 02-09-2025</div>
        <div class="barcode">
            <svg width="150" height="60">
                <rect x="0" y="0" width="2" height="60" fill="#000"/>
                <rect x="4" y="0" width="1" height="60" fill="#000"/>
                <rect x="7" y="0" width="3" height="60" fill="#000"/>
                <rect x="12" y="0" width="1" height="60" fill="#000"/>
                <rect x="15" y="0" width="2" height="60" fill="#000"/>
                <rect x="19" y="0" width="1" height="60" fill="#000"/>
                <rect x="22" y="0" width="3" height="60" fill="#000"/>
                <rect x="27" y="0" width="2" height="60" fill="#000"/>
                <rect x="31" y="0" width="1" height="60" fill="#000"/>
                <rect x="34" y="0" width="2" height="60" fill="#000"/>
                <rect x="38" y="0" width="3" height="60" fill="#000"/>
                <rect x="43" y="0" width="1" height="60" fill="#000"/>
                <rect x="46" y="0" width="2" height="60" fill="#000"/>
                <rect x="50" y="0" width="1" height="60" fill="#000"/>
                <rect x="53" y="0" width="3" height="60" fill="#000"/>
            </svg>
        </div>
    </div>
    
    <table class="main-table">
        <thead>
            <tr>
                <th class="item-col">Item</th>
                <th class="amount-col">Daily</th>
                <th class="amount-col">FFB Accum..</th>
                <th class="item-col">Item</th>
                <th class="amount-col">Daily</th>
                <th class="amount-col">Accum..</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash From HQ</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB Purchase</td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Cash Variance</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>C/F</td>
                <td class="amount-col"></td>
                <td class="amount-col">0.00</td>
            </tr>
            <tr>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
                <td>Cash</td>
                <td class="amount-col">3.04</td>
                <td class="amount-col">3.04</td>
            </tr>
            <tr>
                <td>Cash</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col"></td>
                <td>Credit</td>
                <td class="amount-col">34.41</td>
                <td class="amount-col">34.41</td>
            </tr>
            <tr>
                <td>Cash received by Ramp</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>Credit B/F</td>
                <td class="amount-col">5.26</td>
                <td class="amount-col">5.26</td>
            </tr>
            <tr>
                <td>Cash received by FFB customer</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FM</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
            </tr>
            <tr>
                <td>Debit Note</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td></td>
                <td class="amount-col">42.71</td>
                <td class="amount-col">42.71</td>
            </tr>
            <tr>
                <td>Cash In</td>
                <td class="amount-col">19,268.60</td>
                <td class="amount-col">19,268.60</td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr class="section-header">
                <td></td>
                <td class="amount-col">19,268.60</td>
                <td class="amount-col"></td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Cash FFB Purchase</td>
                <td class="amount-col">2,199.90</td>
                <td class="amount-col">2,199.90</td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Cash Out</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB C/F</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Credit Note</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB In</td>
                <td class="amount-col">42.71</td>
                <td class="amount-col">42.71</td>
            </tr>
            <tr>
                <td>Credit FFB Advance (Ramp)</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB In (rejected)</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
            </tr>
            <tr>
                <td>Credit FFB Advance (HQ)</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FM</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
            </tr>
            <tr>
                <td>Cash FFB Advance</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB Out</td>
                <td class="amount-col">9.23</td>
                <td class="amount-col">9.23</td>
            </tr>
            <tr>
                <td>General Expenses</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>FFB Balance</td>
                <td class="amount-col">33.48</td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Petrol/Diesel</td>
                <td class="amount-col">500.00</td>
                <td class="amount-col">500.00</td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td>Staff Advance</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>Shortage/Return</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
            </tr>
            <tr>
                <td>Staff Salary</td>
                <td class="amount-col">0.00</td>
                <td class="amount-col">0.00</td>
                <td>Balance C/F</td>
                <td class="amount-col">33.48</td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
            <tr class="total-row">
                <td>Cash Balance</td>
                <td class="amount-col">16,568.70</td>
                <td class="amount-col"></td>
                <td></td>
                <td class="amount-col"></td>
                <td class="amount-col"></td>
            </tr>
        </tbody>
    </table>
    
    <div class="detail-section">
        <div class="detail-title">CASH</div>
        <div class="detail-item">P25001951 FFB 0.84 MT (MOHAMMAD WAZIR SHAFIX) <span class="amount-right"><span class="weight-value">0.84</span><span class="price-value">604.80</span></span></div>
        <div class="detail-item">P25001955 FFB 0.08 MT (MOHD FIRTI BIN SABAR) <span class="amount-right"><span class="weight-value">0.08</span><span class="price-value">60.00</span></span></div>
        <div class="detail-item">P25001958 FFB 0.80 MT (ROSSITAR BINTI BERING) <span class="amount-right"><span class="weight-value">0.80</span><span class="price-value">576.00</span></span></div>
        <div class="detail-item">P25001959 FFB 0.49 MT (ASIP BIN JAR [MUSI]) <span class="amount-right"><span class="weight-value">0.49</span><span class="price-value">352.80</span></span></div>
        <div class="detail-item">P25001960 FFB 0.54 MT (ASIP BIN JAR [MUSI]) <span class="amount-right"><span class="weight-value">0.54</span><span class="price-value">388.80</span></span></div>
        <div class="detail-item">P25001963 FFB 0.29 MT (ROSSITAR BINTI BERING) <span class="amount-right"><span class="weight-value">0.29</span><span class="price-value">217.50</span></span></div>
        <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right"><span class="weight-value">3.04</span><span class="price-value">2,199.90</span></span></div>
    </div>
    
    <div class="detail-section">
        <div class="detail-title">CASH IN</div>
        <div class="detail-item">BAKI BULAN 8 <span class="amount-right">19,268.60</span></div>
        <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">19,268.60</span></div>
    </div>
    
    <div class="detail-section">
        <div class="detail-title">FFB OUT</div>
        <div class="detail-item">Sale Eng Hong Palm Oil Mill <span class="amount-right">9.23</span></div>
        <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">9.23</span></div>
    </div>
    
    <div class="detail-section">
        <div class="detail-title">PETROL/DIESEL</div>
        <div class="detail-item">BRK8814 <span class="amount-right">200.00</span></div>
        <div class="detail-item">VMK8814 <span class="amount-right">100.00</span></div>
        <div class="detail-item">VNE8814 <span class="amount-right">200.00</span></div>
        <div class="detail-item detail-total" style="margin-left: 400px;"><span class="amount-right">500.00</span></div>
    </div>
    
    <div style="text-align: center; margin-top: 50px;">
        <p>PAGE 1</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
