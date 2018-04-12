<?php

namespace App\Admin\Controllers;

use App\YsyProject;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YsyProjectController extends Controller
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

            $content->header('养生营');
            $content->description('门票类型管理');

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

            $content->header('header');
            $content->description('修改');

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

            $content->header('header');
            $content->description('新建');

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
        return Admin::grid(YsyProject::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('project_name', '票务名称')->editable();
            $grid->column('project_price', '票务价格')->editable();
            $states = [
                'on' => ['value' => 1, 'text' => '已禁用', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '未禁用', 'color' => 'default'],
            ];
            $grid->status('禁用状态')->switch($states);

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
        return Admin::form(YsyProject::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('project_name', '票务名称');
            $form->text('project_price', '票务价格');
            $form->display('created_at', '创建时间');
            $form->switch('status')->default(0);
        });
    }
}
