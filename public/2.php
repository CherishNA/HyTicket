
/**
* @return array
* 配置微信支付
*/
public function config()
{
return $config = [
'wechat' => [
'app_id' => 'wx9c1fa5f5157b8888',
'mch_id' => '1490434922',
'notify_url' => 'http://wx.huayuanguoji.com/wxpay/paysuccess',
'key' => 'huayuanmeiyehuayuanmeiyehuayuanm',
'cert_client' =>  '/apiclient_cert.pem',
'cert_key' => '/apiclient_key.pem',
],
];

}


/**
* 支付方法
*/
public function dopay()
{
//        $ticketOrder = json_decode($request->input('order'));
$wxOrder = [
'out_trade_no' => 'test1',
'total_amount' => '1',
'subject' => 'test subject',
'spbill_create_ip' => '8.8.8.8',
'openid' => 'obkSg1KweqWKe4aFG_g8VTsdvvtY',
];
$pay = new Pay($this->config());
$result = $pay->driver('wechat')->gateway('mp')->pay($wxOrder);
dd($result);
}

/**
* 支付回调
*/
public function paysuccess(Request $request)
{

$pay = new Pay($this->config());
$verify = $pay->driver('wechat')->gateway('mp')->verify($request->getContent());

if ($verify) {
file_put_contents('notify.txt', "收到来自微信的异步通知\r\n", FILE_APPEND);
file_put_contents('notify.txt', '订单号：' . $verify['out_trade_no'] . "\r\n", FILE_APPEND);
file_put_contents('notify.txt', '订单金额：' . $verify['total_fee'] . "\r\n\r\n", FILE_APPEND);
} else {
file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
}

echo "success";
}