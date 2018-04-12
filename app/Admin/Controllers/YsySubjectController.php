<?php

namespace App\Admin\Controllers;

use App\YsySubjectModel;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YsySubjectController extends Controller
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
            $content->description('课程管理');

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
            $content->description('修改课程');

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
            $content->description('创建课程');

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
        return Admin::grid(YsySubjectModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('subname', '课程名称')->editable();;
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
        return Admin::form(YsySubjectModel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('subname', '课程名称');
            $form->switch('status')->default(0);
        });
    }
}
