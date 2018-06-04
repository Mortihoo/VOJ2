<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/5/31
 * Time: 17:59
 */

namespace app\index\controller;

use think\Controller;//引入Controller类


class Base extends Controller
{
    public $user;
    public $CONTEST_ID = '32221';
    public $CONTEST_PASSWORD = 'qwertyuiop';
    public $CONTEST_STARTTIME = '2018-06-02 07:00:00';
    public $CONTEST_ENDTIME = '2018-08-06 20:00:00';

    public function __construct() {
        parent::__construct();
    }

    public function initialize() {
        $this->user = new User();
        $this->assign_info_to_view();
    }

    public function assign_info_to_view() {
        $this->assign('user_id', $this->user->id);
        $this->assign('user_nickname', $this->user->nickname);
    }

    public function js_alert($str) {
        echo "<script>alert('" . $str . "');</script>";
    }

    public function stop_action($str = null){
        if (isset($str))
            echo "<script>alert('" . $str . "');</script>";
        echo "<script>history.go(-1);</script>";
        exit();
    }

}