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
    /* @font-face {
      font-family: 'Firefly Sung';
      font-style: normal;
      font-weight: 400;
      src: url(http://eclecticgeek.com/dompdf/fonts/cjk/fireflysung.ttf) format('truetype');
    }         */
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        .container {
            margin: 20px;
        }
        .container table tr td {
            margin: 12px;
        }
        .container .text {
            font-size: 12px;
        }
        .container .text div {
            padding-bottom: 5px;
        }
        .items {
            margin-top: 40px;
        }
        .items table {
            border-collapse: collapse;
        }
        .items table tr th {
            font-size: 12px;
            padding: 10px;
            border: 1px solid black;
            background-color: #ededed;
        }
        .items table tr td {
            font-size: 12px;
            padding: 10px;
            border: 1px solid black;
        }
        .items tfoot tr td {
            font-size: 12px;
        }
        .footer {
            font-size: 12px;
        }
    </style>

</head>
<body>
    <div class="container">
        @yield('content')
    </body>
</html>
