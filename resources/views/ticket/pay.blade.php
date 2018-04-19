<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    {{--JS --}}
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    {{--微信支付JSAPI--}}
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
    <title>支付页面</title>

</head>
<body>
<div>
    <input type="hidden" value="{{$order}}" id="order" name="order">
    <div>

        <table class="table">
            <caption>您的订单信息</caption>
            <thead>
            <tr>
                <th>名称</th>
                <th>信息</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>订单号:</td>
                <td>{{$order->order_id}}</td>
            </tr>
            <tr>
                <td>订单名称:</td>
                <td>{{$order->order_info}}</td>
            </tr>
            <tr>
                <td>购票人姓名:</td>
                <td>@if(empty($order->name))
                        {{$order->username}}
                    @else
                        {{$order->name}}
                    @endif
                </td>
            </tr>
            <tr>
                <td>购票人手机号:</td>
                <td>{{$order->order_mobile}}</td>
            </tr>
            <tr>
                <td>购票人身份证号码:</td>
                <td>{{$order->ucid}}</td>
            </tr>
            <tr>
                <td>所在地区:</td>
                <td>{{$order->area}}</td>
            </tr>
            <tr>
                <td>推荐人:</td>
                <td>{{$recommend_name}}</td>
            </tr>
            <tr>
                <td>订单金额:</td>
                <td>@if(empty($pro_price))
                        {{$order->order_price}}
                    @else
                        {{$pro_price}}
                    @endif

                    元
                </td>
            </tr>
            </tbody>
        </table>

        <button class="btn btn-primary text-right" id="pay" name="pay" style="width: 100%">确认支付</button>
    </div>
</div>
</body>
<script>
    wx.config(<?php echo $js->config(array('chooseWXPay'), false) ?>); //这里改成true就可以打开微信js的调试模式
    $('#pay').click(function () {
        callpay();
    });

    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            {

                "appId": '{{$config["appId"]}}',
                "timeStamp": '{{$config["timestamp"]}}',
                "nonceStr": '{{$config["nonceStr"]}}',
                "package": '{{$config["package"]}}',
                "signType": '{{$config["signType"]}}',
                "paySign": '{{$config["paySign"]}}'
            },
            function (res) {
//                WeixinJSBridge.log(res.err_msg);
//                alert(res.err_msg);
                if (res.err_msg == "get_brand_wcpay_request:ok") {
                    alert('支付成功。');
                    window.location.href = '/wxpay/paysuccess?order_id=' + '{{$order->order_id}}' + '&order_info=' + '{{$order->order_info}}';
                }
                else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                    alert("支付失败，请返回重试。");
                }
            }
        )
        ;
    }

    function callpay() {
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }


</script>

</html>
