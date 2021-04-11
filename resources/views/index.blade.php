<!DOCTYPE html>
<html>

<head>
    <title>司令的骰子</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css')}}">
    <style>
        .point {
            font-size: 2rem;
        }

        .touzi {
            padding: 10px;
            cursor: pointer;
        }

        .touzi img {
            width: 100%;
        }

        .touzi span {
            position: absolute;
            right: 10%;
            top: 10%;
            font-size: 1.2rem;
            color: #1E9FFF !important;
        }

        .random .layui-form-checkbox {
            position: absolute;
            top: 21%;
            right: 1%;
            z-index: 1000;
        }

        .setting .layui-form-select dl dd.layui-this {
            background-color: #1E9FFF;
            color: #fff;
        }

        .layui-timeline-axis {
            color: #1E9FFF !important;
        }

        .random .layui-form-checked span,
        .layui-form-checked:hover span {
            background-color: #393D49;
        }

        .random .layui-form-checked i,
        .layui-form-checked:hover i {
            color: #393D49;
        }

        #res_tbody {
            height: 100px;
            overflow-y: scroll;
            display: block;
        }

        #res_thead,
        #res_tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
            /* text-align:center; */
        }
    </style>
</head>

<body>
    <div class="layui-container">
        <div class="layui-collapse">
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">设置</h2>
                <div class="layui-colla-content layui-show">
                    <form class="layui-form layui-row setting" lay-filter="setting">
                        <div class="layui-col-md4" style="padding:2px">
                            <input type="text" id="name" name="name" required lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input" lay-verType="tips" lay-reqText="你想让大家称呼阿猫阿狗？">
                        </div>

                        <div class="layui-col-md4" style="padding:2px">
                            <button id="setname" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-blue layui-btn-fluid" lay-submit lay-filter="setname">确认昵称</button>
                        </div>
                        <div class="layui-col-md4" style="padding:1px">
                            <select id="d_color" name="d_color" lay-filter="d_color">
                            </select>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <hr class="layui-border-blue">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md9">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-xs12">
                        <div class="layui-panel">
                            <div style="padding: 10px;">
                                <p>当前在线人数：<span id="user_count"></span></p>
                                <p>玩家列表：<span id="user_namelist"></span></p>

                            </div>
                        </div>
                    </div>
                    <div class="layui-col-xs12">
                        <div class="layui-panel">
                            <div style="padding: 10px;height:200px;overflow-y: scroll;">
                                {{-- <table class="layui-table" lay-size="sm">
                                    <thead id="res_thead"></thead>
                                    <tbody id="res_tbody"></tbody>
                                </table> --}}
                                <ul class="layui-timeline" id="res_history">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="layui-col-md3">
                <div class="layui-panel">
                    <div class="layui-row">
                        <div class="layui-col-xs4 touzi" data-number="1d2">
                            <img src="/images/1d2-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d3">
                            <img src="/images/1d3-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d4">
                            <img src="/images/1d4-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d6">
                            <img src="/images/1d6-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d8">
                            <img src="/images/1d8-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d10">
                            <img src="/images/1d10-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d12">
                            <img src="/images/1d12-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d20">
                            <img src="/images/1d20-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                        <div class="layui-col-xs4 touzi" data-number="1d100">
                            <img src="/images/1d100-black~iphone.png" alt="" srcset="">
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md4 layui-form random">
                <input type="checkbox" name="dark" title="暗骰" lay-filter="dark">

                <button id="random" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-blue layui-btn-fluid">试试运气</button>


            </div>

            <div class="layui-col-md4">
                <button id="clear" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-orange layui-btn-fluid">重选骰子</button>
            </div>
            <div class="layui-col-md4">
                <button id="clearhistory" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-red layui-btn-fluid">清除大家所有历史数据</button>
            </div>
        </div>
    </div>
    <script src="{{ asset('layui/layui.js') }}"></script>
    <script>
        layui.use(function() {
            var $ = layui.$,
                layer = layui.layer,
                form = layui.form,
                laypage = layui.laypage,
                element = layui.element,
                laydate = layui.laydate,
                util = layui.util;

            var user_count, user_name = '',
                user_uuid = "{{ $user_id ?? '' }}";
            var d_color = ['black', 'blue', 'cyan', 'green', 'orange', 'pink', 'purple', 'red', 'white', 'yellow'];
            var d_random = {};
            var d_type = false;

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
            for (const color of d_color) {
                $('#d_color').append('<option value="' + color + '">' + color + '</option>');
            }
            form.render('select', 'setting');
            form.on('select(d_color)', function(data) {

                $('.touzi').each(function() {
                    let imgsrc = '/images/' + $(this).data('number') + '-' + data.value + '~iphone.png';
                    $(this).find('img').attr('src', imgsrc);
                })

            });
            websocket = new WebSocket("ws://" + location.hostname + ":6789/");

            form.on('submit(setname)', function(data) {
                websocket.send(JSON.stringify({ action: 'setname', user_uuid: user_uuid, value: data.field.name.toString() }));
            });
            form.on('checkbox(dark)', function(data) {
                d_type = data.elem.checked;
            });
            $('#random').click(function() {
                if (!$.isEmptyObject(d_random)) {
                    websocket.send(JSON.stringify({ action: 'random', user_uuid: user_uuid, data: d_random, dark: d_type }));
                    $("#clear").trigger("click");
                }else{
                    layer.msg('您还没选骰子');
                }

            });

            $('#clear').click(function() {
                d_random = {};
                $('.touzi span').each(function() {
                    $(this).html('');
                })
            });
            $('#clearhistory').click(function() {
                layer.prompt({ formType: 1, title: '管理员密码' }, function(value, index, elem) {
                    websocket.send(JSON.stringify({ action: 'clear_history', pwd: value }));
                    layer.close(index);
                });
            })
            $('.touzi').click(function() {
                let number = $(this).data('number');
                if (number in d_random) {
                    d_random[number] += 1;
                } else {
                    d_random[number] = 1;
                }
                $(this).find('span').html(d_random[number]);
            });
            setInterval(function() {
                websocket.send(JSON.stringify({ action: 'set_heart', user_uuid: user_uuid, time: new Date().getTime() }));
            }, 20000);

            websocket.onopen = function() {
                websocket.send(JSON.stringify({ action: 'join_room', user_uuid: user_uuid }));
                layer.msg('成功加入大家庭');

            }

            websocket.onerror = function(e) {
                //如果出现连接、处理、接收、发送数据失败的时候触发onerror事件
                layer.msg(e);
            }

            websocket.onclose = function(e) {
                //当客户端收到服务端发送的关闭连接请求时，触发onclose事件
                // layer.msg('与大家庭链接断开了，要不刷新下页面试试？');
                layer.open({
                    title: false
                    ,content: '与大家庭链接断开了，要不刷新下页面试试？'
                    ,btn:[]
                });   
            }


            websocket.onmessage = function(event) {
                data = JSON.parse(event.data);
                switch (data.type) {
                    case 'state':
                        let res_html = '';
                        let res = data.data;
                        for (key in res) {
                            let res_name = res[key][1];
                            let res_time = dateFormat(res[key][0], 'Y-m-d H:i:s');
                            let res_history = '';
                            let res_sum = 0;
                            res_sum += res[key][2][0];
                            for (res_type in res[key][2][1]) {
                                res_history += '<tr><td>' + res_type + '</td><td>' + res[key][2][1][res_type] + '</td></tr>';
                            }
                            res_html += '<li class="layui-timeline-item"><i class="layui-icon layui-timeline-axis">&#xe63f;</i><div class="layui-timeline-content layui-text"><h3 class="layui-timeline-title">' + res_name + '</h3><p>' + res_time + '</p><table class="layui-table" lay-size="sm"><thead><tr><th>骰子</th><th>结果</th></tr></thead><tbody><tr><td>合计</td><td>' + res_sum + '</td></tr>' + res_history + '</tbody></table></div></li>';

                        }
                        $('#res_history').html(res_html);
                        // let res = data.data;
                        // let res_new = [];
                        // for (let i in res) {
                        //     res[i].reverse();
                        //     res_new.push(res[i].length);
                        // }
                        // let max = res_new.sort((a, b) => b - a)[0];
                        // let key = Object.keys(res);
                        // let thead = '<tr>';
                        // for (k in key) {
                        //     thead += '<th>' + key[k].toString() + '</th>';
                        // }
                        // thead += '</tr>';

                        // $('#res_thead').html(thead);
                        // let tbody = '';
                        // for (row = 0; row < max; row++) {
                        //     let tr = '<tr>';

                        //     for (k in res) {
                        //         let td = res[k][row] ? res[k][row] : '';
                        //         tr += '<td>' + td + '</td>';
                        //     }
                        //     tr += '</td>';
                        //     tbody += tr;
                        // }
                        // $('#res_tbody').html(tbody);

                        break;
                    case 'users':
                        $('#user_count').html(data.count.toString())
                        $('#user_namelist').html(data.name.join('; '));

                        break;
                    case 'reg':
                        user_name = data.name;
                        $('#name').val(user_name);
                        layer.msg('昵称设置成功');
                        break;
                    case 'msg':
                        layer.msg(data.message);
                        break;
                    case 'newpoint':
                        layer.msg('你投掷了<br><span class="point">' + data.point.toString() + '</span>');
                        break;
                    case 'otherpoint':
                        layer.msg(data.name.toString() + '<br>投掷了<br><span class="point">' + data.point.toString() + '</span>');
                        break;
                    default:
                        console.error(
                            "unsupported event", data);
                }

            };
        });
    </script>
</body>

</html>
