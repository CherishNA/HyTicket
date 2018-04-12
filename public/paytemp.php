/**
* @return array
* 微信支付参数配置
*/
//    public function options()
//    {
//        return [
//            // 前面的appid什么的也得保留哦
//            'app_id' => 'wx9c1fa5f5157b8888', //你的APPID
//            'secret' => 'b83d89d9d94d33c8e52aa9e6f300e137',     // AppSecret
//            // 'token'   => 'your-token',          // Token
//            // 'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
//            // ...
//            // payment
//            'payment' => [
//                'merchant_id' => '1490434922',
//                'key' => 'huayuanmeiyehuayuanmeiyehuayuanm',
//
//                'cert_path' => './apiclient_cert.pem', // XXX: 绝对路径！！！！
//                'key_path' => './apiclient_key.pem',      // XXX: 绝对路径！！！！
//                'notify_url' => 'http://cherishna.natapp1.cc/wxpay/paysuccess',       // 你也可以在下单时单独设置来想覆盖它
//                // 'device_info'     => '013467007045764',
//                // 'sub_app_id'      => '',
//                // 'sub_merchant_id' => '',
//                // ...
//            ],
//        ];
//    }
//
//    public function doPay(Request $request)
//    {
//        $order = json_decode($request->input('order'));
//        $options = $this->options();
//        $app = new Application($options);
//        $payment = $app->payment;
//        $attributes = [
//            'trade_type' => 'APP', // JSAPI，NATIVE，APP...
//            'body' => '购买' . $order->order_info,
//            'detail' => $order->order_info, //我这里是通过订单找到商品详情，你也可以自定义
//            'out_trade_no' => $order->order_id,
//            'total_fee' => 0.01 * 100, //因为是以分为单位，所以订单里面的金额乘以100
//            // 'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
//            'openid' => $order->openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
//        ];
//        $weChatOrder = new Order($attributes);
//        $result = $payment->prepare($weChatOrder);
//
//        //支付成功 处理
//        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
//            //如果支付成功更新支付状态
////            $ticetOrder = TicketOrder::find($order->id);
////            $ticetOrder->pay_status = 1;
////            $ticetOrder->pay_time = time();
//            $prepayId = $result->prepay_id;
//            $config = $payment->configForAppPayment($prepayId);
//            return response()->json($config);
//        }
//    }
//
//    public function paySuccess()
//    {
//        $options = $this->options();
//        $app = new Application($options);
//        $response = $app->payment->handleNotify(function ($notify, $successful) {
//            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
//            $order = TicketOrder::where('order_id', $notify->out_trade_no)->first();
//            if (count($order) == 0) { // 如果订单不存在
//                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
//            }
//            // 如果订单存在
//            // 检查订单是否已经更新过支付状态
//            if ($order->pay_time) { // 假设订单字段“支付时间”不为空代表已经支付
//                return true; // 已经支付成功了就不再更新了
//            }
//            // 用户是否支付成功
//            if ($successful) {
//                // 不是已经支付状态则修改为已经支付状态
//                $order->pay_time = time(); // 更新支付时间为当前时间
//                $order->pay_status = 1; //支付成功,
//            } else { // 用户支付失败
//                $order->pay_status = 0; //待付款
//            }
//            $order->save(); // 保存订单
//            return true; // 返回处理完成
//        });
//    }
