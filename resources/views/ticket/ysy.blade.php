<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>花缘国际养生营报名通道</title>

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
                    }, job: {
                        required: true,
                    }
                    , tuijianren: {
                        required: true,
                   }
                }, messages: {
                    username: '请输入购票人姓名',
                    mobile: '请输入正确的手机号',
                    ucid: '请输入正确的身份证号码',
                    captcha: '请填写验证码',
                    job: '请填写职业',
                    tuijianren: '请填写您的推荐人'
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
    </script>

</head>
<body>
<div style="width:100%">
    <form action="/weixin/createYsyOrder" method="post" id="orderForm" name="orderForm">
        <div class="form-group" style="width: 80% ; margin-left: 10%">
            <br>
            <h6 class="text-center">尊敬的：{{$user->nickname}}</h6>
            <h3 class="text-center text-info" style="text-decoration:underline"> 花缘国际养生营报名通道</h3>
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
                <lable> 性别：</lable>
                <label class="radio-inline">
                    <input type="radio" class="radio-inline" name="sex" id="mobile" value="1"> 男性
                </label>
                <label class="radio-inline">
                    <input type="radio" class="radio-inline" name="sex" id="mobile" value="0" checked>女性
                </label>
            </div>
            <div class="form-group">
                <lable>职业/所属行业：</lable>
                <input type="text" class="form-control" name="job" id="job">
            </div>
            <div class="form-group">

                <lable> 购票人手机号(请务必填写本人手机号)：</lable>
                <p>
                    <input type="text" class="form-control" name="mobile" id="mobile" style="width: 60%; float: left">
                </p>
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

                推荐人姓名:
                <div id="element_id">
                    <input name="tuijianren" class="form-control" id="tuijianren" style="width:100%">
                    </input>
                </div>
            </div>
            <div class="form-group">
                {{--获取班级--}}
                选择推荐人期次:
                <select name="ysyClass" class="form-control" id="ysyClass" style="width:100%">
                    @foreach($ysyClass as $k1=>$v1)
                        <option value="{{$v1['id']}}">{!! str_repeat("&bull;",0).$v1['clsname'] !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                {{--获取课程--}}
                选择报名课程:
                <select name="ysySubject" class="form-control" id="ysySubject" style="width:100%">
                    @foreach($ysySubject as $k1=>$v1)
                        <option value="{{$v1['id']}}"> {!! str_repeat("&bull;",0).$v1['subname'] !!}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                {{--获取期次--}}
                选择报名期次:
                <select name="ysyDaily" class="form-control" id="ysyDaily" style="width:100%">
                    @foreach($ysyDaily as $k1=>$v1)
                        <option value="{{$v1['id']}}">
                            {{$v1['dailyname']}}(时间：{!! date('Y-m-d',strtotime($v1['ddate'])) !!})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
               
                <lable> 选择票务类型期次:</lable>
                {{--<select name="ysypro" class="form-control" id="ysypro" style="width:100%">--}}
                @foreach($ysyPro as $k1=>$v1)
                    <label class="radio-inline" style="margin-left: 10px">
                        <input name="ysypro" id="ysypro" type="radio" class="radio-inline" checked
                               value="{{$v1['id']}}"/>
                        <lable>
                            {{$v1['project_name']}}
                            (价格：{{$v1['project_price']}})
                        </lable>
                    </label>
                @endforeach
                {{--</select>--}}
            </div>
            邀请码(没有可不填):
            <div id="element_id">
                <input name="ysy_code" class="form-control" id="ysy_code" style="width:100%">
                </input>
            </div>
        </div>
    </form>
    <button id="submitOrder" class="btn btn-primary" style="margin-left:5%; width: 90%;height: 50px"
            name="submitOrder" id="submitOrder" onclick="order()">
        提交信息
    </button>
    <div style="width: 50px">
        &nbsp;
    </div>

</div>
</body>
<script>
    /**
     * 提交订单
     */
    function order() {
        $("#orderForm").submit();
    }

</script>
</html>
