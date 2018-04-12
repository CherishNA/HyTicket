<?php

namespace App\Admin\Controllers;

use App\YsyClassModel;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YsyClassController extends Controller
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
            $content->description('往期期次管理');

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
            $content->description('修改往期期次');

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
            $content->description('新增往期期次');

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
        return Admin::grid(YsyClassModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('clsname', '班级名称')->editable();
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
        return Admin::form(YsyClassModel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('clsname', '往期期次名称');
            $form->switch('status')->default(0);
        });
    }
}
