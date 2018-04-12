<?php

namespace LaraMall\Weixin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\WxPayController;
use App\market_structure;
use App\TicketOrder;
use App\YsyClassModel;
use App\YsyDailyModel;
use App\YsyOrderModel;
use App\YsyProject;
use App\YsySubjectModel;
use App\YsyTicketCodeModel;
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

class YsyWeiXinController extends Controller
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
        return redirect(Weixin::ysyRedirect());
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
        //获取班级
        $ysyClass = YsyClassModel::where('status', '=', '0')->get();
        //获取课程
        $ysySubject = YsySubjectModel::where('status', '=', '0')->get();
        //获取期次
        $ysyDaily = YsyDailyModel::where('status', '=', '0')->get();
        //获取票务类型
        $ysyPro = YsyProject::where('status', '=', '0')->get();

        Log::info('oauth回调函数');
        //获取微信数据
        $info = Weixin::user(request()->code);
        //如果已经注册过直接登录
        if ($user = User::where('openid', $info->openid)->first()) {
            return view('ticket/ysy', ['user' => $user, 'ysyClass' => $ysyClass,
                'ysySubject' => $ysySubject, 'ysyDaily' => $ysyDaily, 'ysyPro' => $ysyPro]);
        }
        //使用微信用户信息注册会员 再登录
        $user = new User;
        $user->openid = $info->openid;
        $user->avatar = $info->headimgurl;
        $user->nickname = $info->nickname;
//        $user->ip = $_SERVER['HTTP_X_REAL_IP'];
        $user->save();
        return view('ticket/ysy', ['user' => $user, 'ysyClass' => $ysyClass,
            'ysySubject' => $ysySubject, 'ysyDaily' => $ysyDaily, 'ysyPro' => $ysyPro]);
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 保存订单信息
     */
    public function createOrder(Request $request)
    {

        //生成订单
        $order = new YsyOrderModel();
        $order->sex = $request->input('sex');
        $order->job = $request->input('job');
        //        班级id
        $order->class_id = $request->input('ysyClass');
        //        期次id
        $order->subject_id = $request->input('ysySubject');
        //        班级id
        $order->daily_id = $request->input('ysyDaily');
        $order->name = $request->input('username');
        //获取期次信息
        $daily = YsyDailyModel::where('id', '=', $request->input('ysyDaily'))->first();
//        获取票务类型价格
        $pro = YsyProject::where('id', '=', $request->input('ysypro'))->first();
        $pro_Price = $pro->project_price;
		$order->pro_id = $request->input('ysypro');
        //获取邀请码
        $ysy_code = $request->input('ysy_code');
        $ysy_codeClass = YsyTicketCodeModel::where(['hy_code' => $ysy_code, 'status' => 0])->first();
        //如果邀请码存在 修改价格
        if ($ysy_codeClass != null) {
            $used_count = $ysy_codeClass->used_count;
            $total_count = $ysy_codeClass->total_count;
            //如果还有邀请码还有次数 就修改为邀请价格
            if ($used_count < $total_count) {
                $pro_Price = $ysy_codeClass->discount_price;
            }
        }
//{!! date('Y-m-d',strtotime($v1['ddate'])) !!} $daily->ddate
        $order->order_info = $pro->project_name . ',期次:' . $daily->dailyname . '(' . date('Y-m-d', strtotime($daily->ddate)) . ')';
        $order->order_price =$pro_Price;
        $order->ucid = $request->input('ucid');
        $order->area = $request->input('provinceName') . $request->input('cityName') . $request->input('streetName');
        $recommend_name = $request->input('tuijianren');
        $order->recommend = $recommend_name;
        $order->openid = $request->input('openid');
        $order->order_mobile = $request->input('mobile');
        //未支付状态
        $order->pay_status = 0;
        //支付时间为空
        $order->pay_time = null;
        //订单类型线上订单
        $order->order_type = 0;
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
        $wxpay = new WxPayController();
        $options = $wxpay->payOrder($order, $pro_Price,$ysy_codeClass);
//        dd($options['config']);
        return view('ticket.pay',
            ['config' => $options['config'],
                'js' => $options['js'],
                'order' => $order,
                'recommend_name' => $recommend_name,
                'pro_price' => $pro_Price]);
    }

    /**
     * 微信支付
     */
}