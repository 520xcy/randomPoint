<!DOCTYPE html>
<html>

<head>
    <title>司令的骰子</title>
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
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css')}}">

</head>

<body>
    <div class="layui-container">
        <div class="layui-row">
            <div class="layui-col-xs8 layui-col-xs-offset2 layui-col-md6 layui-col-md-offset3">
                {!! QrCode::size(300)->gradient(0, 0, 0, 0, 150, 0, 'radial')->generate($url) !!}
            </div>
        </div>
    </div>
    <script src="{{ asset('layui/layui.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('js/DDRequest.js')}}"></script>
    <script>
        layui.use(['layer'], function() {
            var layer = layui.layer,
                hash = "{{ $hash }}";
            var intervalId = window.setInterval(function() {
                AjaxSubmit("{{route('pclogincheck')}}", { "random_key": hash }, function(res) {
                    clearInterval(intervalId);
                    if(res.state == 200){
                        layer.msg(res.message, function() {
                            location.href = "{{$target_url}}";
                        });
                    }else if(res.state == 312){
                        layer.msg(res.message)
                    }else if(res.state == 310){
                        location.reload();
                    }
                    

                }, function(err) {

                }, 'PUT');
            }, 2000);

            /*
             ** 时间戳转换成指定格式日期
             ** eg. 
             ** dateFormat(11111111111111, 'Y年m月d日 H时i分')
             ** → "2322年02月06日 03时45分"
             */
            var dateFormat = function(timestamp, formats) {
                // formats格式包括
                // 1. Y-m-d
                // 2. Y-m-d H:i:s
                // 3. Y年m月d日
                // 4. Y年m月d日 H时i分
                formats = formats || 'Y-m-d';

                var zero = function(value) {
                    if (value < 10) {
                        return '0' + value;
                    }
                    return value;
                };

                var myDate = timestamp ? new Date(timestamp) : new Date();

                var year = myDate.getFullYear();
                var month = zero(myDate.getMonth() + 1);
                var day = zero(myDate.getDate());

                var hour = zero(myDate.getHours());
                var minite = zero(myDate.getMinutes());
                var second = zero(myDate.getSeconds());

                return formats.replace(/Y|m|d|H|i|s/ig, function(matches) {
                    return ({
                        Y: year,
                        m: month,
                        d: day,
                        H: hour,
                        i: minite,
                        s: second
                    })[matches];
                });
            };


        });
    </script>
</body>

</html>