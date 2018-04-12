<?php

namespace LaraMall\Weixin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\WxPayController;
use App\market_structure;
use App\TicketOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use LaraMall\Weixin\Facades\Weixin;
use LaraMall\Weixin\Models\User;

use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Mockery\Exception;

class WeixinController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    |
    |   构造函数
    |
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {

    }


    /*
    |--------------------------------------------------------------------------
    |
    |   登录链接
    |
    |--------------------------------------------------------------------------
    */
    public function login()
    {
        return redirect(Weixin::redirect());
    }

    /*
    |--------------------------------------------------------------------------
    |
    |   处理回调函数
    |
    |--------------------------------------------------------------------------
    */
    public function callback(Request $request)
    {
        Log::info('oauth回调函数');
        //获取关系
        $structure = $this->structure();
//        dd(json_encode($structure));
        //获取微信数据
        $info = Weixin::user(request()->code);
        //如果已经注册过直接登录
        if ($user = User::where('openid', $info->openid)->first()) {
            return view('ticket/index', ['user' => $user, 'structure' => $structure]);
        }
        //使用微信用户信息注册会员 再登录
        $user = new User;
        $user->openid = $info->openid;
        $user->avatar = $info->headimgurl;
        $user->nickname = $info->nickname;
//        $user->ip = $_SERVER['HTTP_X_REAL_IP'];
        $user->save();

        return view('ticket/index', ['user' => $user, 'structure' => $structure]);
    }


    /**
     * @param $array
     * @param int $parent_id
     * @return array
     *     递归获取下级关系
     */

    public function recursiveMarketStructure($array, $parent_id = 0)
    {
        $res = [];
        foreach ($array as $k => $v) {
            if ($v['pid'] == $parent_id) {
                $v['child'] = $this->recursiveMarketStructure($array, $v['cid']);
                $res [] = $v;
            }
        }
        return $res;
    }

    /**
     * @return array
     * 获取市场关系
     */
    public function structure()
    {
        //获取市场关系
        //获取市场关系
        $market_structure = market_structure::get();
        $structure = $this->recursiveMarketStructure($market_structure->toArray());
        return $structure;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 发送短息
     */
    public function SendSms(Request $request)
    {

        $config = [
            'app_key' => '24731880',
            'app_secret' => 'f71dcbc7050850759ff9412350d3eae0',
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
        ];
        // 使用方法一
        $client = new Client(new App($config));
        $req = new AlibabaAliqinFcSmsNumSend;
        $mobile = $request->input('mobile');
        $product = '花缘优美票务系统';
        $SignName = '花缘优美票务系统';
        $TemplateCode = 'SMS_52270383';
        $code = rand(100000, 999999);
        $req->setRecNum($mobile)
            ->setSmsParam(['code' => $code, 'product' => $product])
            ->setSmsFreeSignName($SignName)
            ->setSmsTemplateCode($TemplateCode);

        $interval = 300;
        Redis::set($mobile, json_encode(
            ['captcha' => $code, 'expire' => time() + $interval]));
        $resp = $client->execute($req);

        if (property_exists($resp, "code") && $resp->code > 0) {
            return response()->json(['status' => 201, 'error' => $resp->msg]);
        } else {
            return response()->json(['status' => 200, 'error' => '短信发送成功,请注意查收']);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * 验证短信并提交订单
     */
    public function verifySmsAndOrder(Request $request)
    {
        //接受手机号和验证码
//        暂时关闭手机验证码 功能  难缠的老娘们
//        $mobile = $request->input('mobile');
//        $captcha = $request->input('captcha');
        // 检查验证码
//        $redisCaptcha = json_decode(Redis::get($mobile), true);
//        $now = time();
//        if ($redisCaptcha['expire'] < $now) {
//            Redis::del($mobile);
//            return response()->json(['status' => 204, 'error' => '验证码过期']);
//        }
//        if ($redisCaptcha['captcha'] != $captcha) {
//            return response()->json(['status' => 201, 'error' => '验证码错误']);
//        }
        return response()->json(['status' => 200, 'error' => '验证通过']);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 保存订单信息
     */
    public function createOrder(Request $request)
    {

        //生成订单
        $order = new TicketOrder();
        $order->username = $request->input('username');
        $order->order_info = '花缘4月17大会门票';
        $order->order_price = 100.00;
        $order->ucid = $request->input('ucid');
        $order->area = $request->input('provinceName') . $request->input('cityName') . $request->input('streetName');
        $order->recommend_id = $request->input('tuijianren');
        $order->openid = $request->input('openid');
        $order->order_mobile = $request->input('mobile');
        //未支付状态
        $order->pay_status = 0;
        //未签到状态
        $order->sign_status = 0;
        //支付时间为空
        $order->pay_time = null;
        //订单类型线上订单
        $order->order_type=0;
        //生成随机订单码
        $order->order_id = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        //事物处理订单
        $flag = true;
        DB::beginTransaction();
        try {
            $order->save();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            $flag = false;
        }
        $market_structure = market_structure::where('cid', '=', $request->input('tuijianren'))->first();
        $recommend_name = $market_structure->name;
        $wxpay = new WxPayController();
        $options = $wxpay->ticketPayOrder($order);
//        dd($options['config']);
        return view('ticket.pay',
            ['config' => $options['config'],
                'js' => $options['js'],
                'order' => $order,
                'recommend_name' => $recommend_name]);
    }

    /**
     * 微信支付
     */
}