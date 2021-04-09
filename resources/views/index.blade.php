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

        #res_tbody {
            height: 200px;
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
                    <form class="layui-form">
                        <div class="layui-inline" style="width: 78%;">
                            <input type="text" id="name" name="name" required lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input" lay-verType="tips" lay-reqText="你想让大家称呼阿猫阿狗？">
                        </div>
                        <div class="layui-inline" style="width: 20%;">
                            <button id="setname" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-blue layui-btn-fluid" lay-submit lay-filter="setname">确认</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <hr class="layui-border-blue">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md6">
                <div class="layui-panel">
                    <div style="padding: 10px;">
                        <p>当前在线人数：<span id="user_count"></span></p>
                        <p>玩家列表：<span id="user_namelist"></span></p>

                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="layui-panel">
                    <div style="padding: 10px;">
                        <table class="layui-table" lay-size="sm">
                            <thead id="res_thead"></thead>
                            <tbody id="res_tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <button id="random" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-blue layui-btn-fluid">试试运气</button>
            </div>
            <div class="layui-col-md6">
                <button id="clear" type="button" class="layui-btn layui-btn layui-btn-primary layui-border-red layui-btn-fluid">清除大家所有历史数据</button>
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
                user_uuid = '{{ $user_id ?? '' }}';

            websocket = new WebSocket("ws://{{ str_replace('http://','',config('app.url'))}}:6789/");

            form.on('submit(setname)', function(data) {
                websocket.send(JSON.stringify({ action: 'setname', user_uuid: user_uuid, value: data.field.name.toString() }));
            });

            $('#random').click(function() {
                websocket.send(JSON.stringify({ action: 'random', user_uuid: user_uuid }));
            });

            $('#clear').click(function() {
                layer.prompt({ formType: 1, title: '管理员密码' }, function(value, index, elem) {
                    websocket.send(JSON.stringify({ action: 'clear_history', pwd: value }));
                    layer.close(index);
                });
            })

            websocket.onopen = function() {
                websocket.send(JSON.stringify({ action: 'join_room', user_uuid : user_uuid }));
                layer.msg('成功加入大家庭');

            }

            websocket.onerror = function(e) {
                //如果出现连接、处理、接收、发送数据失败的时候触发onerror事件
                layer.msg(e);
            }

            websocket.onclose = function(e) {
                //当客户端收到服务端发送的关闭连接请求时，触发onclose事件
                layer.msg('与大家庭链接断开了，要不刷新下页面试试？');
            }

            websocket.onmessage = function(event) {
                data = JSON.parse(event.data);
                console.log(data);
                switch (data.type) {
                    case 'state':
                        let res = data.data;
                        let res_new = [];
                        for (let i in res) {
                            res[i].reverse();
                            res_new.push(res[i].length);
                        }
                        let max = res_new.sort((a, b) => a < b)[0];
                        let key = Object.keys(res);
                        let thead = '<tr><th>次数</th>';
                        for (k in key) {
                            thead += '<th>' + key[k].toString() + '</th>';
                        }
                        thead += '</tr>';

                        $('#res_thead').html(thead);
                        let tbody = '';
                        let count = max;
                        for (row = 0; row < max; row++) {
                            let tr = '<tr><td>' + count.toString() + '</td>';

                            for (k in res) {
                                let td = res[k][row] ? res[k][row] : '';
                                tr += '<td>' + td + '</td>';
                            }
                            tr += '</td>';
                            tbody += tr;
                            count--;
                        }
                        $('#res_tbody').html(tbody);
                        break;
                    case 'users':
                        $('#user_count').html(data.count.toString())
                        $('#user_namelist').html(data.name.join(';'));

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
                    default:
                        console.error(
                            "unsupported event", data);
                }

            };
        });
    </script>
</body>

</html>