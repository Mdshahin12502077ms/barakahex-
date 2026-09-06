<!DOCTYPE html>
<html>

<head>
    <title>Label Sticker Printing</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        media="screen,print">
    <link href="https://fonts.google.com/share?selection.family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900">

    <style>
        @media print {
            body {
                font-family: 'Lato', sans-serif;
            }

        }

        body{
            background-color: transparent;
            color: #212529!important;
        }



        ::selection {
            background: #f31544;
            color: #fff;
        }
        ::moz-selection {
            background: #f31544;
            color: #fff;
        }
        h1, h2, h3, h4, h5, h6, p {
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 1.5em;
            color: #222;
        }
        h2 {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
        }
        p {
            font-size: 6px;
            color: #666;
            line-height: 1.2em;
        }
        img {
            max-width: 100%;
        }
        ul {
            margin: 0;
            padding: 0;
        }
        ul > li {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        ul.date {
            margin-top: 2mm;
            margin-bottom: 1mm;
        }

        .date ul{
            display: flex;
            width: 100%;
        }
        .date ul li {
            font-weight: 600;
            font-size: 8px;
            border: 1px double #ddd;
            width: 50%;
        }
        .date-type li{
            padding-top: 4px;
            padding-bottom: 4px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .text-center {
            text-align: center;
        }
        .mb-10 {
            margin-bottom: 10px;
        }
        .p-5 {
            padding: 5px;
        }

        .float-right {
            float: right;
        }
        .float-left {
            float: left;
        }

        .d-flex {
            display: flex;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-between {
            justify-content: space-between;
        }

        .single-border {
            border: 1px double #ddd;
        }
        .single-border li {
            padding: 3px 8px;
        }

        #mid {
            margin-top: 5px;
            min-height: 80px;
        }
        div#mid::after {
            content: "";
            clear: both;
            display: table;
        }
        #bot {
            min-height: 50px;
        }
        #top .logo {
            width: 120px;
            height: auto;
            filter: grayscale(1);
            filter: gray;
            /* height: 60px; */
        }
        .barcode img {
            width: 100%;
        }

        .info {
            display: block;
            float:left;
            margin-left: 0;
        }
        .info ul li {
            font-size: 8px;
            display: inline-block;
            font-weight: 600;
        }
        .info ul li span {
            font-size: 8px;
            padding-left: 0px;
            font-weight: normal;
        }
        .info {
            width: 60%;
        }
        .qr-code {
            width: 30%;
        }
        .qr-code img {
            height: 50px;
            width: 50px;
            object-fit: contain;
        }
        p.note {
            color: #000;
            font-size: 6px;
            margin-bottom: 3px;
        }
        .service {
        border-bottom: 1px solid #eee;
        }

        .payment-type{
            font-size: 8px;
            font-weight: 600;
        }
        .payment-type::after {
            content: "";
            clear: both;
            display: table;
        }
        ul.code-price {
        position: relative;
        }
        ul.code-price li {
            padding: 5px 10px !important;
            font-size: 8px;
            font-weight: 600;
        }
        .payment-status {
            width: 50%;
        }
        .payment-status h3 {
            font-size: 14px;
            font-weight: 600;
        }
        .payment-qr {
            width: 50%;
            object-fit: contain;
        }
        .payment-qr img {
            width: 100px;
            height: 80px;
            object-fit: contain;
        }


        .branch-dest{
            border-top-style: dotted;
            border-top-color: rgba(34, 32, 32, 0.5);
        }
        .branch-dest p {
            font-size: 4px;
            color: #000;
        }
        .branch-dest p a {
            color: #0563C1;
        }

        .custom-hr {
            height: 1px!important;
            background-color: black !important;
            border: 1px solid black !important;
            align-items: center;
            width: 100%;
        }

        #invoice-POS {
            width: 76.2mm;
            min-height: 127mm;
            border: 1px solid #ddd;
            margin: auto;
            padding: 20px;
        }
        #invoice{
            size: 3in 5in;
            /* padding-bottom: 18.897637795px;
            padding-right: 75.590551181px;
            padding-left: 11.338582677px;
            padding-top: 50.38582677px; */
        }



        .package ul {
            display:flex;
            font-size: 8px;
            font-weight: 600;

        }

        .package ul .single-border{
            width: 96%;
            padding-top: 4px;
            padding-bottom: 4px;
            gap: 4;

        }

        .npc{
            border: 1px double #ddd;
            width: 38%;
            font-size: 8px;
            margin-left: auto;
        }

        .npc-pall{
            padding: 10px;
            font-size: 8px;
            width: 100%;
        }

        .npc-pall .pall{
            font-weight: 600;
        }
        .fs-5{
            font-size: 8px;
        }

        .fs-3{
            font-size: 8px;
        }
        .address{
            width:50%;
            padding-left: 10px;
        }

        .address ul {
            border: none;
        }
        .address ul li{
            margin-bottom: 5px;
            border: 1px solid #ddd;
        }
        .address .single-border li:last-of-type {
            margin-bottom: 0;
        }

        .justify-content-space-between{
            justify-content: space-between;
        }

        .invoice-text {
            font-size: 8px;
            font-weight: 600;
            align-items: center;
            justify-content: center;
            display: flex;
            margin-top: 4px;
        }

        .logo {
            font-size: 20px;
            font-weight: 600;
        }

        @page {
            size: 3in 5in;
            outline: 1pt dotted;
            margin: 0;
        }

    </style>
</head>

<body>
  <div id="invoice-POS">
    <div id="invoice">
        <center id="top" class="mb-3">
            <img class="logo" src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : getFileLink('80X80', []) }}" alt="deliX" class="img-fluid">
        </center>

        <div class="date">
            <ul class="date-type mb-10 gap-3">
                <li class="single-border text-center">{{ __($parcel->parcel_type) }}</li>
                @php
                $deliveryDate = new DateTime($parcel->delivery_date);
                $formattedDate = $deliveryDate->format('j F');
                @endphp
                <li class="single-border  text-center">{{ $formattedDate }}</li>
            </ul>
        </div>
        <div id="mid">
            <div class="info float-left">
                <ul>
                    <li><b>{{ __('merchant') }}</b>: <span>{{ @$parcel->merchant->company }}</span> <br>
                        <strong>{{ @$parcel->merchant->phone_number }}</strong>
                    </li>
                    <br/>
                    <li><b>{{ __('customer') }}</b>: <span> {{ $parcel->customer_name }}</span><br><span><strong>{{ $parcel->customer_phone_number }}</strong></span></li><br>
                    <li><b>{{ __('address') }}</b>: <span> {{ $parcel->customer_address }}</span></li>
                </ul>
            </div>
          <div class="npc d-flex">
            <div class="npc-pall">
                <span class="text-center">{{ @$parcel->pickupBranch->name }}</span><br><span class="text-center">TO</span><br><span class="text-center pall">PALL</span>
            </div>
          </div>

        </div>
        <p class="note w-100 mt-2">{{ __('instruction:') }}{{ $parcel->note }}</p>

        <!--End Invoice Mid-->

        <div class="delivery-details">
            <ul class="code-price single-border mb-3 d-flex align-items-center d-flex align-items-center justify-content-center">
                <li>{{ $parcel->price > 0 ? $parcel->price : 'NO COD' }}</li>
                <li>|</li>
                <li>PAID</li>
            </ul>
            <div class="payment-type mb-3 d-flex align-items-center justify-content-between">
                <div class="payment-qr float-right">
                   {{ $qr_code}}
                </div>
                <div class="address">
                    <ul class="single-border mb-10">
                    <li class="text-center">{{ @$parcel->pickupBranch->name }}</li>
                    </ul>
                    @if($parcel->home_delivery == '1')
                        <ul class="single-border">
                            <li class="text-center">{{  __('home_delivery') }}</li>
                        </ul>
                    @endif
                </div>
            </div>

            <div class="package">
                <ul class="mb-3 gap-2">
                    @if($parcel->open_box == '1')
                    <li class="single-border text-center">{{ $parcel->open_box == '1' ? __('open_box') : __('close_box') }}</li>
                    @endif
                    @if($parcel->fragile == '1')
                    <li class="single-border  text-center">{{ $parcel->fragile == '1' ? __('fragile') : __('non_fragile') }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <!--End Delivery Details-->

        <div class="branch-dest">
          <p class="text-center mt-1">
            NOTE: THIS PARCEL IS FROM {{ setting('company_name') }} IF FOUND
            THIS PLEASE RETURN TO {{ setting('company_name') }}.
            <a href="#">WWW.pickfast.COM</a>, CONTACT: {{ setting('phone') }}
          </p>
        </div>
        <div class="invoice">
            <p class="text-dark invoice-text">{{ $parcel->parcel_no }}</p>
        </div>
      </div>
  </div>
  <!--End Branch Dest-->

    <script>
        window.print();
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>

</html>
