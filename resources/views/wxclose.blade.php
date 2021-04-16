<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <!--==============手机端适应============-->
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <!--===================================-->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--==============强制双核浏览器使用谷歌内核============-->
    <meta name="renderer" content="webkit">
    <meta name="force-rendering" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        提示
    </title>
    <link href="{{asset('layui/css/layui.css')}}" rel="stylesheet">
    <script charset="utf-8" src="{{asset('layui/layui.js')}}"></script>
</head>

<body>

    <script>
        layui.use(['layer'], function() {
            var layer = layui.layer;

            layer.msg('{{$message}}', {
                time:5000,
            }, function() {
                try {
                    //这个可以关闭安卓系统的手机

                    document.addEventListener('WeixinJSBridgeReady', function() { WeixinJSBridge.call('closeWindow'); }, false);

                    //这个可以关闭ios系统的手机

                    WeixinJSBridge.call('closeWindow');
                } catch (e) {

                }
            });
        });
    </script>
</body>

</html>