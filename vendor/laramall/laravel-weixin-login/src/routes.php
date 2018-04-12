<?php

Route::namespace('LaraMall\Weixin')
    ->middleware('web')
    ->group(function () {
		
		 Route::get('weixin/ysylogin1', function () {
            return '系统维护中，给您带来不便，尽情谅解!!';
        });
        Route::get('weixin/login1', function () {
            return '系统维护中，给您带来不便，尽情谅解!!';
        });
        /**
		
         * 门票入口
         */
        Route::get('weixin/login', 'WeixinController@login');

//         * 门票回调

        Route::get('weixin/callback', 'WeixinController@callback');
        //创建门票订单
        Route::post('weixin/createOrder', 'WeixinController@createOrder');
        /**
         *        养生营入口
         */
        Route::get('weixin/ysylogin', 'YsyWeiXinController@login');
        //养生营回调地址
        Route::get('weixin/ysyCallback', 'YsyWeiXinController@callback');

        //创建养生营订单
        Route::post('weixin/createYsyOrder', 'YsyWeiXinController@createOrder');

        //阿里大于短信
        Route::get('weixin/alisms', 'WeixinController@SendSms');
        // 短信验证码
        Route::get('weixin/verifySmsAndOrder', 'WeixinController@verifySmsAndOrder');

    });