<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
    <style>
        p {
            font-family: 微软雅黑;
            text-align: center;
            font-size: 18px;
            color: lightslategray;
            line-height: 50px;
        }
    </style>
    <title>购票成功</title>
</head>
<body style=" background-color: floralwhite">
<div>
    <div style="margin: 15% auto;">

        <p style="font-size: 30px; " class="alert alert-success">恭喜您订票成功!</p>
        <p style="font-size: 30px; " class="alert alert-success">您的订单号:{{$order_id}}</p>
        <div class="alert alert-success">
            <p style="color: lightslategray">
                <span style="font-weight: bold">请您截图保留好本页面信息 <br> 关闭后此页面将不再显示</span></p>
            <p style="color: lightslategray"><span style="font-weight: bold"></span>
                恭喜您已经订购成功！
                <br>
                票务名称：
                <br>
                {{$order_info}}
                <span
                        style="font-weight: bold"></span>
                <br></p>

        </div>
        <div style="margin-top: 70px; ">
            <p style="font-weight: bold">
                &copy上海花缘优美生物科技有限公司 <br>
                全体员工期待您的到来!
            </p>


        </div>
    </div>
</div>
</body>
</html>
