<div>
    <h1>支付0.01元</h1>
    <br>
    <input type="button" value="支付" id="pay">
</div>
{{--JS--}}
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>

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
                "timeStamp": '{{$config["timeStamp"]}}',
                "nonceStr": '{{$config["nonceStr"]}}',
                "package": '{{$config["package"]}}',
                "signType": '{{$config["signType"]}}',
                "paySign": '{{$config["paySign"]}}'
            },
            function (res) {
                WeixinJSBridge.log(res.err_msg);
                alert(res.err_code + res.err_desc + res.err_msg);
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