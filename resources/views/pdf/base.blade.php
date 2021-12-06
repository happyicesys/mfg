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
        * {
            font-family: 'Firefly Sung', Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        .container {
            margin: 12px;
        }
        .container table tr td {
            margin: 10px;
        }
        .container .text {
            font-size: 11px;
        }
        .container .text div {
            padding-bottom: 5px;
        }
        .items {
            margin-top: 30px;
        }
        .items table {
            border-collapse: collapse;
        }
        .items table tr th {
            font-size: 10px;
            padding: 8px;
            border: 1px solid black;
            background-color: #ededed;
        }
        .items table tr td {
            font-size: 12px;
            padding: 5px;
            border: 1px solid black;
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
    <div class="container">
        @yield('content')
    </body>
</html>
