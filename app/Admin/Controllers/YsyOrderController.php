<?php

namespace App\Admin\Controllers;

use App\YsyClassModel;
use App\YsyDailyModel;
use App\YsyOrderModel;

use App\YsyProject;
use App\YsySubjectModel;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use LaraMall\Weixin\Models\User;

class YsyOrderController extends Controller
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

            $content->header('花缘养生营');
            $content->description('报名管理');

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

            $content->header('花缘养生营');
            $content->description('修改报名信息');

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

            $content->header('花缘养生营');
            $content->description('创建报名信息');

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
        return Admin::grid(YsyOrderModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
//            $grid->openid('购票微信昵称')->display(function ($openid) {
//                return User::where('openid', $openid)->first()->nickname;
//            });
            $grid->column('name', '购票人姓名');
            $grid->column('order_mobile', '购票人手机号');
            $grid->sex('性别')->display(function ($sex) {
                return $sex ? '男' : '女';
            });
            $grid->column('ucid', '身份证号码');
            $grid->column('job', '工作');
            $grid->column('area', '地区');
            $grid->column('order_id', '订单号');
            $grid->order_type('订单类型')->display(function ($order_type) {
                return $order_type == 0 ? '微信订单' : '线下订单';
            });
            $states = [
                'on' => ['value' => 1, 'text' => '已支付', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未支付', 'color' => 'default'],
            ];
            $grid->pay_status('支付状态')->switch($states);
            $grid->pay_time('支付时间')->display(function ($pay_time) {
                return $pay_time == '' ? '未支付无支付时间' : $pay_time;
            });
            $grid->column('order_price', '订单价格');
            $grid->column('recommend', '推荐人');
            $grid->class_id('班次')->display(function ($class_id) {
                return YsyClassModel::where('id', $class_id)->first()->clsname;
            });
            $grid->subject_id('课程')->display(function ($subject_id) {
                return YsySubjectModel::where('id', $subject_id)->first()->subname;
            });
            $grid->daily_id('期次')->display(function ($daily_id) {
                return YsyDailyModel::where('id', $daily_id)->first()->dailyname;
            });
            $grid->pro_id('票务类型')->display(function ($pro_id) {
                return YsyProject::where('id', $pro_id)->first()->project_name;
            });
            $grid->created_at('创建时间');

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(YsyOrderModel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('openid', '微信openid')->value('线下购买无需填写');
            $form->text('name', '购票人姓名');
            $form->text('order_mobile', '手机号码');
            $form->hidden('order_type')->default(1);
            $states = [
                'on' => ['value' => 1, 'text' => '男', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '女', 'color' => 'default'],
            ];
            $form->switch('sex', '性别')->states($states);
            $form->text('ucid', '身份证号码');
            $form->text('job', '工作');
            $form->text('area', '地区');
            $form->text('order_id', '订单号')
                ->value(date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
            $payStates = [
                'on' => ['value' => 1, 'text' => '已支付', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未支付', 'color' => 'default'],
            ];
            $form->text('order_info', '订单信息');
            $form->switch('pay_status', '支付状态')->states($payStates)->default(1);
            $form->datetime('pay_time', '支付时间')->default(now());
            $form->text('order_price', '订单价格');
            $form->text('recommend', '推荐人');
            $form->select('pro_id', '选择票务项目类型')->options('/api/getpro');
            $form->select('class_id', '选择班次')->options('/api/getcls');
            $form->select('subject_id', '选择课程')->options('/api/getsub');
            $form->select('daily_id', '选择期次')->options('/api/getdai');
        });
    }
}
