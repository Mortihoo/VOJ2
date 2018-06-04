<?php

namespace app\index\controller;


use app\index\job\PostCode;
use app\index\model\Status as StatusModel;



class Index extends Base
{

    public function index() {
        $this->assign('now', date("y-m-d H:i:s", time()));
        $this->assign('now_time', strtotime(date("y-m-d H:i:s", time())));
        $this->assign('contest_end_time', strtotime($this->CONTEST_ENDTIME));
        $this->assign('contest_start_time', strtotime($this->CONTEST_STARTTIME));
        return $this->fetch();
    }

    public function miss() {
        $this->assign('now', date("y-m-d H:i:s", time()));
        $this->assign('now_time', strtotime(date("y-m-d H:i:s", time())));
        $this->assign('contest_end_time', strtotime($this->CONTEST_ENDTIME));
        $this->assign('contest_start_time', strtotime($this->CONTEST_STARTTIME));
        return $this->fetch();
    }

    public function login() {
        if ($this->request->param('login')) {
            $username = $this->request->param('username');
            $password = $this->request->param('password');
            if ($this->user->login($username, $password)) {
                $this->js_alert("登陆成功");
                $this->assign_info_to_view();
                $this->redirect(url("index/Index/index"));
            } else {
                $this->stop_action("账号或密码错误！");
            }
        }

        return $this->fetch();
    }

    public function logout() {
        $this->user->logout();
        $this->assign_info_to_view();
        $this->redirect(url("index/Index/index"));
    }

    public function register() {
        if ($this->request->param('register')) {
            $username = $this->request->param('username');
            $password = $this->request->param('password');
            $password2 = $this->request->param('password2');
            $nickname = $this->request->param('nickname');
            if ($password != $password2) {
                $this->stop_action("密码不一致!");
            }
            if ($this->user->register($username, $password, $nickname)) {
                $this->assign_info_to_view();
                $this->redirect(url("index/Index/index"));
            } else {
                $this->stop_action("账号已经被注册!");
            }
        }
        return $this->fetch();
    }

    public function test() {
      //  echo phpinfo();

        $list = StatusModel::paginate(20);
        $this->assign('list', $list);
        return $this->fetch();
//        $a = new PostCode();
//
//        $data = [
//            'id' => 3 ,
//            'lang' => 0,
//            'user_code' => "asdddddddddddddddddddddddddddsadasdasdddddddddddddddddddddddddddddddddddddddsssssssssssssssssssssssssssssss",
//            'problem_id' => 1001,
//        ];
//
//       // echo $a->get_content('http://acm.hdu.edu.cn/diy/contest_show.php?cid=32221');
//      // echo $a->GetStatus();
//
//        if($a->work($data))
//            echo 123123123;
//        else
//            echo 888888888888;
    }
}
