<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/6/1
 * Time: 20:54
 */

namespace app\index\controller;

use app\index\model\Status as StatusModel;
use think\Exception;
use think\Queue;

class Contest extends Base
{
    public function __construct() {
        parent::__construct();
        if (!$this->user->is_login)
            $this->redirect(url("index/Index/login"));
        $nowtime = date("y-m-d H:i:s", time());
        if (strtotime($nowtime) < strtotime($this->CONTEST_STARTTIME)) {
            //$this->redirect(url("index/Index/index"));
            $this->stop_action("比赛还没有开始");
        }

    }

    private function check_contest_end() {
        $nowtime = date("y-m-d H:i:s", time());
        if (strtotime($nowtime) > strtotime($this->CONTEST_ENDTIME)) {
            $this->stop_action("比赛结束,无法交题");
        }
    }

    public function problem_list() {
        return $this->fetch();
    }

    public function submit() {
        if ($this->request->param('submit')) {
            dump($this->request->param());
            $this->check_contest_end();
            if (!$this->request->param('problem_id')) {
                $this->stop_action("请写题号！");
            }
            if ($this->request->param('lang') != 0 && !$this->request->param('lang')) {
                $this->stop_action("请选择语言！");
            }
            if (!$this->request->param('user_code')) {
                $this->stop_action("代码不能为空！");
            }
            $status = new StatusModel;
            $status_id = $status->create_status(
                $this->user->id,
                $this->request->param('problem_id'),
                $this->request->param('lang'),
                $this->request->param('user_code')
            );
            if ($status_id == -1) {
                $this->stop_action("提交失败!");
            }

            $data = [
                'id' => $status_id,
                'lang' => $this->request->param('lang'),
                'user_code' => $this->request->param('user_code'),
                'problem_id' => $this->request->param('problem_id')
            ];

            $jobHandlerClassName = 'index/PostCode';
            $jobQueueName = "post";
            $isPushed = Queue::push($jobHandlerClassName, $data, $jobQueueName);
            if ($isPushed !== false) {
                $this->redirect("index/Contest/status");
            } else {
                $this->stop_action("提交失败!");
            }
        }
        return $this->fetch();
    }

    public function status() {
        $status_list = StatusModel::paginate(10);
        $this->assign('status_list', $status_list);
        return $this->fetch();
    }

    public function view_code($id) {
        try {
            $status = StatusModel::get(['id' => $id]);
            if ($status->user_id != $this->user->id && !$this->user->is_admin) {
                $this->assign('flag', false);
                $this->assign('msg', "没有权限查看代码");
            } else {
                $this->assign('user_code', $status->user_code);
                $this->assign('flag', true);
            }
        } catch (Exception $exception) {
            $this->assign('flag', false);
            $this->assign('msg', "没有此代码");
        }
        return $this->fetch();
    }

}