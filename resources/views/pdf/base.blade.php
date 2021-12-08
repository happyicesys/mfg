<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        html {
            height: 100%;
        }
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
        }

        @font-face {
            font-family: 'founder-type';
            font-weight: bold;
            src: url('{{storage_path("fonts/founder-type.ttf")}}') format('truetype');
        }
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        .container {
            margin: 10px;
        }
        .container table tr td {
            margin: 8px;
        }
        .container .text {
            font-size: 11px;
        }
        .container .text div {
            padding-bottom: 5px;
        }
        .items {
            margin-top: 20px;
        }
        .items table {
            border-collapse: collapse;
        }
        .items table tr th {
            font-size: 10px;
            padding: 4px;
            border: 1px solid black;
            background-color: #ededed;
        }
        .items table tr td {
            font-size: 11px;
            padding: 4px;
            border: 1px solid black;
            height: auto;
        }
        .items tfoot tr td {
            font-size: 11px;
        }
        .footer {
            font-size: 11px;
        }
    </style>
</head>
<body>
    {{-- @dd(public_path('fonts/wts11.ttf')); --}}
    <div class="container">
        @yield('content')

        <script type="text/php">
            if ( isset($pdf) ) {
                $font = $fontMetrics->getFont("helvetica", "bold");
                $pdf->page_text(565, 820, "{PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));
            }
        </script>
</body>
</html>
