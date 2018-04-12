<?php

namespace App\Admin\Controllers;

use App\YsyDailyModel;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YsyDailyController extends Controller
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
            $content->description('花缘养生营期次管理');
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

            $content->header('修改');
            $content->description('修改期次');
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

            $content->header('新增');
            $content->description('新增期次');
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
        return Admin::grid(YsyDailyModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('dailyname', '期次名称')->editable();
            $grid->column('ddate', '期次时间')->editable('date');
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
        return Admin::form(YsyDailyModel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('dailyname', '期次名称')->placeholder('如：2018年3月第1期');
            $form->datetime('ddate', '日期选择');
            $form->switch('status')->default(0);
        });
    }
}
