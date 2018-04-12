<?php

namespace App\Http\Controllers;

use App\TicketOrder;
use App\YsyOrderModel;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;

class WxPayController extends Controller
{


    /**
     * @return array
     * 微信支付参数配置
     */
    public function options()
    {
        return [
            // 前面的appid什么的也得保留哦
            'app_id' => 'wx9c1fa5f5157b8888', //你的APPID
            'secret' => 'b83d89d9d94d33c8e52aa9e6f300e137',     // AppSecret
            // 'token'   => 'your-token',          // Token
            // 'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
            // ...
            // payment
            'payment' => [
                'merchant_id' => '1490434922',
                'key' => 'huayuanmeiyehuayuanmeiyehuayuanm',

                'cert_path' => './apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path' => './apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url' => 'http://ticket.vipgz1.idcfengye.com/wxpay/paysuccess',       // 你也可以在下单时单独设置来想覆盖它
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
    }

    /**
     * @return Application
     * 获取Application
     */
    public function getApp()
    {
        $options = $this->options();
        return new Application($options);
    }

    /**
     * @param $request
     * @return array|string
     * 下单操作
     */
    public function checkOrder($order, $pro_Price, $ysy_codeClass)
    {
        Log::info('请求下单');
//        $order = json_decode($order->input('order'));
        $app = $this->getApp();
        $payment = $app->payment;
        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => '购买' . $order->order_info,
            'detail' => $order->order_info, //我这里是通过订单找到商品详情，你也可以自定义
            'out_trade_no' => $order->order_id,
            'total_fee' => $pro_Price * 100, //因为是以分为单位，所以订单里面的金额乘以100
//            'total_fee' => 0.01 * 100, //因为是以分为单位，所以订单里面的金额乘以100
            'notify_url' => 'http://ticket.huayuanguoji.com/wxpay/paycallback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $order->openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
        ];
        $weChatOrder = new Order($attributes);
        $result = $payment->prepare($weChatOrder);
        //下单成功 处理
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {

            //如果邀请码不为空 而且还有使用次数 那么就+1
            if ($ysy_codeClass != null) {
                $used_count = $ysy_codeClass->used_count;
                $total_count = $ysy_codeClass->total_count;
                //如果还有邀请码还有次数 就修改为邀请价格
                if ($used_count < $total_count) {
                    $ysy_codeClass->used_count += 1;
                    $ysy_codeClass->save();
                }
            }
            //获取prepayId
            $prepayId = $result->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId, false);
            return $config;
        }
    }
    /**
     * @param $request
     * @return array|string
     * 门票下单操作
     */
    public function ticketCheckOrder($order)
    {
        Log::info('请求下单');
//        $order = json_decode($order->input('order'));
        $app = $this->getApp();
        $payment = $app->payment;
        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => '购买' . $order->order_info,
            'detail' => $order->order_info, //我这里是通过订单找到商品详情，你也可以自定义
            'out_trade_no' => $order->order_id,
            'total_fee' => $order->order_price * 100, //因为是以分为单位，所以订单里面的金额乘以100
//            'total_fee' => 0.01 * 100, //因为是以分为单位，所以订单里面的金额乘以100
            'notify_url' => 'http://ticket.huayuanguoji.com/wxpay/paycallback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $order->openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
        ];
        $weChatOrder = new Order($attributes);
        $result = $payment->prepare($weChatOrder);
        //下单成功 处理
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            //获取prepayId
            $prepayId = $result->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId, false);
            return $config;
        }
    }




    /**
     * 支付回调
     */
    public function payCallback()
    {
        $xml = file_get_contents('php://input');
        Log::info($xml);
        $app = $this->getApp();
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            //   使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = TicketOrder::where('order_id', '=', $notify->out_trade_no)->first();
            if (!$order) {
                $order = YsyOrderModel::where('order_id', '=', $notify->out_trade_no)->first();
            }
            if (count($order) == 0) { // 如果订单不存在
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->pay_time) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($notify['result_code'] === 'SUCCESS') {
                // 不是已经支付状态则修改为已经支付状态
                $order->pay_time = date('Y-m-d H:i:s', time()); // 更新支付时间为当前时间
                $order->pay_status = 1; //支付成功,
                
            } else { // 用户支付失败
                $order->pay_status = 0; //待付款
            }
            $order->save(); // 保存订单


            return true; // 返回处理完成
        });

        return $response;
    }


    /**
     * @param Request $request
     * @return mixed
     * 养生营支付调用
     */
    public function payOrder($order, $pro_Price, $ysy_codeClass)
    {
        //支付订单页面
        $app = $this->getApp();
        $js = $app->js;
        //下单操作
        $config = $this->checkOrder($order, $pro_Price, $ysy_codeClass);
        return ['js' => $js, 'config' => $config];
    }

    /**
     * @param Request $request
     * @return mixed
     * 门票支付调用
     */
    public function ticketPayOrder($order)
    {
        //支付订单页面
        $app = $this->getApp();
        $js = $app->js;
        //下单操作
        $config = $this->ticketCheckOrder($order);
        return ['js' => $js, 'config' => $config];
    }



    public function paySuccess(Request $request)
    {
        $order_id = $request->input('order_id');
        $order = TicketOrder::where('order_id', '=', $order_id)->first();		
        if (!$order) {
            $order = YsyOrderModel::where('order_id', '=', $order_id)->first();
			//发送验证码
           
            return view('ticket.success', ['order_id' => $order_id, 'order_info' => $order->order_info]);
        }
        return view('ticket.success', ['order_id' => $order_id, 'order_info' => $order->order_info]);
    }

//发送验证码
    public function sendYsySms($order)
    {

        $config = [
            'app_key' => '24731880',
            'app_secret' => 'f71dcbc7050850759ff9412350d3eae0',
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        ];
        // 使用方法一
        $client = new Client(new App($config));
        $req = new AlibabaAliqinFcSmsNumSend;
        $SignName = '花缘优美票务系统';
        $TemplateCode = 'SMS_128645383';
		$order_id = $order->order_id;
        $order_info = $order->order_info;
        $req->setRecNum($order->order_mobile)
            ->setSmsParam(['order_id' => $order_id, 'order_info' => $order_info])
            ->setSmsFreeSignName($SignName)
            ->setSmsTemplateCode($TemplateCode);
        return $client->execute($req);
    }
}


