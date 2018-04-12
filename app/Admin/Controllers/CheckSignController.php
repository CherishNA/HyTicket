<?php

namespace App\Admin\Controllers;

use App\market_structure;
use App\TicketOrder;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use function foo\func;
use LaraMall\Weixin\Models\User;

class CheckSignController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('订单管理');
            $content->description('花缘购票系统 订单管理');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('修改订单');
            $content->description('花缘购票系统 修改订单');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('新增订单');
            $content->description('花缘购票系统 新增订单');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {


        return Admin::grid(TicketOrder::class, function (Grid $grid) {
            $grid->filter(function ($filter) {
                $filter->in('pay_status')->checkbox([
                    '0' => '未付款',
                    '1' => '已付款',
                ]);
                $filter->in('sign_status')->checkbox([
                    '0' => '未签到',
                    '1' => '已签到',
                ]);
                $filter->in('order_type')->checkbox([
                    '0' => '线上订单',
                    '1' => '线下订单',
                ]);
                //推荐人
                $filter->like('market.name', '推荐人查询');
                //去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('openid', 'openid查询');
                $filter->like('order_id', '订单号查询');
                $filter->like('username', '购票人姓名');
                $filter->like('order_mobile', '手机号');
                $filter->like('ucid', '身份证号码');
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
//                $actions->disableEdit();
            });
            $grid->id('ID')->sortable();
            $grid->column('order_id', '订单编号');
            $grid->openid('购票微信昵称')->display(function ($openid) {
                if ($openid == '线下购买') {
                    return $openid;
                }
                return User::where('openid', $openid)->first()->nickname;
            });

            $grid->column('username', '购票人姓名');
            $grid->column('order_mobile', '购票人电话');
            $grid->column('ucid', '身份证号码');
            $grid->column('area', '所在地区');
            //支付状态
            $payStates = [
                'on' => ['value' => 1, 'text' => '已支付', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未支付', 'color' => 'default'],
            ];
            $grid->pay_status('支付状态')->switch($payStates);
//            更改签到状态
            // 设置text、color、和存储值
            $states = [
                'on' => ['value' => 1, 'text' => '已签到', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未签到', 'color' => 'default'],
            ];

            $grid->column('order_price','订单金额');
            $grid->sign_status('签到状态')->switch($states);
            $grid->seatno('座位号')->display(
                function ($seatno) {
                    if ($seatno == null) {
                        return '楼 排 座';
                    } else {
                        return $seatno;
                    }
                }
            )->editable('textarea');
            $grid->recommend_id('推荐人姓名')->display(function ($recommend_id) {
                return market_structure::where('cid', $recommend_id)->first()->name;
            });

            $grid->pay_time('付款时间');
            $grid->created_at('订单创建时间');
            $grid->updated_at('订单更新时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected
    function form()
    {
        return Admin::form(TicketOrder::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('order_id', '订单号')
                ->value(date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
            $form->text('username', '购票人姓名')->rules('required');
            $form->text('openid', '微信openid')->value('线下购买');
            $form->text('ucid', '购票人身份证号码')->rules('required')->value('x00x0019xx0000xxxx');
            $form->text('area', '购票人地区')->placeholder('江苏省徐州市泉山区')->value('江苏省徐州市泉山区');
            $form->text('order_mobile', '购票人手机号')->rules('required')->value('13800008888');
            $form->text('pay_time', '支付时间')->value(date('Y-m-d H:i:s', time()));
            $payStates = [
                'on' => ['value' => 1, 'text' => '已支付', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未支付', 'color' => 'default'],
            ];

            $form->switch('pay_status', '支付状态')->states($payStates);
            $form->text('order_type', '订单类型')->value('1');
            $form->text('order_info', '订单信息')->value('花缘年会门票');
            $form->text('order_price', '订单价格')->value('980');
            $form->select('recommend_id', '选择推荐人')->options(function ($cid) {
                $rec = market_structure::find($cid);
                if ($rec) {
                    return [$rec->cid => $rec->name];
                }
            })->ajax('/api/getrec');

            $states = [
                'on' => ['value' => 1, 'text' => '已签到', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未签到', 'color' => 'default'],
            ];
            $form->switch('sign_status', '签到状态')->states($states);
            $form->text('seatno', '座位号')->value('楼 排 座');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function getCid($v)
    {
        return market_structure::where('name', '=', $v)->first()->cid;
    }
}
