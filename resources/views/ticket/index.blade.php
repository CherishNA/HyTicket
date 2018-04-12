<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>花缘国际会议购票系统</title>

    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    {{--JS--}}
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>

    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    {{--地区js--}}
    <script src="https://cdn.bootcss.com/distpicker/2.0.2/distpicker.min.js"></script>
    {{--validata--}}
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/lib/jquery.js"></script>
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
    <script>

        $().ready(function () {

            /**
             * 表单验证
             *
             */
            $("#orderForm").validate({
                rules: {
                    username: "required",
                    mobile: {
                        required: true,
                        cellPhone: true,
                        minlength: 11,
                        maxlength: 11,
                    },
                    ucid: {
                        required: true,
                        IDCard: true
                    }, captcha: {required: true},tuijianren:{
                        required: true,
                    }
                }, messages: {
                    username: '请输入购票人姓名',
                    mobile: '请输入正确的手机号',
                    ucid: '请输入正确的身份证号码',
                    captcha: '请填写验证码',
                    tuijianren:'请选择推荐人'
                }
            });
        });

        //手机验证
        jQuery.validator.addMethod("cellPhone", function (value, element) {
            var tel = /1[3|5|7|8|][0-9]{9}$/;
            return this.optional(element) || (tel.test(value));
        }, "请输入有效的手机号码");
        //身份证号码
        jQuery.validator.addMethod("IDCard", function (value, element) {
            var tel = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            return this.optional(element) || (tel.test(value));
        }, "请输入有效的身份证号码");
        /**
         * 短信定时
         * @type {number}
         */
        var wait = 60;
        function time(o) {
            if (wait == 0) {
                o.removeAttribute("disabled");
                o.innerHTML = "免费获取验证码";
                wait = 60;
            } else {
                o.setAttribute("disabled", true);
                o.innerHTML = "重新发送(" + wait + ")";
                wait--;
                setTimeout(function () {
                        time(o)
                    },
                    1000)
            }
        }
        /**
         * 发送短信
         * @param o
         */
        function sendSms(o) {

            var mobile = $("#mobile").val();
            //短信定时60S
            time(o);
            $.ajax({
                url: "/weixin/alisms",
                Type: 'get',
                data: {'mobile': mobile},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (data) {
                    var status = data['status'];
                    var msg = data['error'];
                    if (status == "200") {
                        alert(msg);
                    } else {
                        alert('短信发送失败，请重试');
                    }
                },
            });
        }


    </script>

</head>
<body>
<div style="width:100%">
    <form action="/weixin/createOrder" method="post" id="orderForm" name="orderForm">
        <div class="form-group" style="width: 80% ; margin-left: 10%">
            <br>
            <h6 class="text-center">尊敬的：{{$user->nickname}}</h6>
            <h3 class="text-center text-info" style="text-decoration:underline"> 欢迎使用花缘票务系统</h3>
            {{--<p class="text-center"><img src="{{$user->avatar}}" width="50px" height="50px"></p>--}}
            <br>
            <input type="hidden" id="openid" name="openid" value="{{$user->openid}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <h4 class="text-center text-info"> 请完善您的购票信息</h4>
            <div class="form-group">
                <lable>购票人姓名：</lable>
                <input type="text" class="form-control" name="username" id="username">
            </div>
            <div class="form-group">

                <lable> 购票人手机号(请务必填写本人手机号)：</lable>
                <p>
                    <input type="text" class="form-control" name="mobile" id="mobile" style="width: 60%; float: left">

                    {{--<button class="btn  btn-primary" name="sendCaptcha" id="sendCaptcha" onclick="sendSms(this)"--}}
                    {{--style="float: right">--}}
                    {{--发送短信验证--}}
                    {{--</button>--}}
                </p>
                {{--<p style="clear: both">--}}
                {{--<br>--}}
                {{--<lable>验证码:</lable>--}}
                {{--<input type="text" class="form-control" name="captcha" id="captcha">--}}
                {{--</p>--}}
            </div>

            <div class="form-group" style="clear: both">

                <lable> 身份证号码：</lable>
                <input type="text" class="form-control" name="ucid" id="ucid">
            </div>
            <div class="form-group">

                <lable> 地区：</lable>
                <div id="distpicker" data-toggle="distpicker">
                    <select id="provinceName" class="form-control" data-province="江苏省" name="provinceName"
                            style="width: 30%;float: left;"></select>
                    <select id="cityName" class="form-control" data-city="徐州市" name="cityName"
                            style="width: 30%;float: left;margin-left: 5%"></select>
                    <select id="streetName" class="form-control" data-district="泉山区" name="streetName"
                            style="width: 30%;float: left;margin-left: 5%"></select>
                </div>
            </div>
            <div class="form-group" style="clear: both">

                选择你的推荐人:
                <div id="element_id">
                    <select name="tuijianren" class="form-control" id="tuijianren" style="width:100%">
                        <option value="" selected></option>
                        @foreach($structure as $k1=>$v1)
                            @if($v1['pid']==0)
                                <option value="{{$v1['cid']}}">{!! str_repeat("&bull;",0).$v1['name'] !!}</option>
                                @foreach($v1['child'] as $k2=>$v2)
                                    <option value="{{$v2['cid']}}">{!! str_repeat("&nbsp;&bull;",2).$v2['name'] !!} </option>
                                    @foreach($v2['child'] as $k3=>$v3)
                                        <option value="{{$v3['cid']}}">{!! str_repeat("&nbsp;&bull;&bull;",3).$v3['name'] !!} </option>
                                    @endforeach
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">


            </div>
        </div>
    </form>
    <button id="submitOrder" class="btn btn-primary" style="margin-left:5%; width: 90%;height: 50px"
            name="submitOrder" id="submitOrder" onclick="order()">
        提交信息
    </button>
</div>
</body>
<script>
    /**
     * 验证短信验证码 并且提交订单
     */
    function order() {
        var mobile = $("#mobile").val();
        var captcha = $("#captcha").val();
        $.ajax({
            url: "/weixin/verifySmsAndOrder",
            method: "get",
            dataType: "json",
            data: {
                'mobile': mobile,
                'captcha': captcha,
            },
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function (data) {
                var status = data['status'];
                var msg = data['error'];
                if (status !== 200) {
                    alert(msg);
                    return false;
                } else {
                    $("#orderForm").submit();
                }
            },

        })
    }

</script>
</html>
