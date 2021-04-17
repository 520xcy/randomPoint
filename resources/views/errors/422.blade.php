<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <!--==============手机端适应============-->
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
        <!--===================================-->
        <meta name="description" content="">
        <meta name="author" content="">
        <!--==============强制双核浏览器使用谷歌内核============-->
        <meta name="renderer" content="webkit" >
        <meta name="force-rendering" content="webkit" >
        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
        <title>
            错误：422
        </title>
        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">
                    
                </div>
                {!! $exception->getMessage() !!}
            </div>
        </div>
    </body>
</html>
