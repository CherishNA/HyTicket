<?php

namespace App\Admin\Controllers;

use App\YsyTicketCodeModel;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YsyTicketController extends Controller
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

            $content->header('花缘养生营票务管理');
            $content->description('邀请码管理');

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

            $content->header('花缘养生营票务管理');
            $content->description('修改邀请码');

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

            $content->header('花缘养生营票务管理');
            $content->description('新增邀请码');

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
        return Admin::grid(YsyTicketCodeModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('hy_code', '邀请码');
            $grid->column('used_count', '已使用次数')->editable();
            $grid->column('total_count', '总次数')->editable();
            $grid->column('discount_price', '优惠后价格')->editable();
            $states = [
                'on' => ['value' => 1, 'text' => '已禁用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未禁用', 'color' => 'default'],
            ];
            $grid->status('禁用状态')->switch($states);

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(YsyTicketCodeModel::class, function (Form $form) {

            $code = $this->create_uuid('hy');
            $form->display('id', 'ID');
            $form->text('hy_code', '邀请码')->default($code);
            $form->text('discount_price', '优惠后价格');
            $form->text('total_count', '可使用总次数');
            $form->hidden('used_count')->default(0);
            $form->switch('status')->default(0);
        });
    }

    public function create_uuid($prefix = "")
    {
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4);
        return $prefix . '-' . $uuid;
    }

}
