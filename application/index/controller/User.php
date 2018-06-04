<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/6/1
 * Time: 14:29
 */

namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Exception;
use think\facade\Cookie;

class User
{
    public $id = -1;
    public $is_login = false;
    public $nickname = 'anonymous';
    public $is_admin = false;

    public function __construct() {
        if (Cookie::has('id', 'user_') && Cookie::has('nickname', 'user_')) {
            $this->id = Cookie::get('id', 'user_');
            $this->nickname = Cookie::get('nickname', 'user_');
            $this->is_login = true;
            $this->is_admin();
            Cookie::set('id', $this->id, ['prefix' => 'user_', 'expire' => 3600]);
            Cookie::set('nickname', $this->nickname, ['prefix' => 'user_', 'expire' => 3600]);
        } else {
            Cookie::clear('user_');
        }
    }

    private function is_admin() {
        try {
            $user = UserModel::get(['username' => $this->username]);
            if ($user->is_admin == 1) {
                $this->is_admin = true;
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function login($username, $password) {
        if ($this->is_login)
            return true;
        try {
            $user = UserModel::get(['username' => $username]);
            if ($user->password == md5($password)) {
                $this->is_login = true;
                $this->nickname = $user->nickname;
                $this->id = $user->id;
                Cookie::set('id', $this->id, ['prefix' => 'user_', 'expire' => 3600]);
                Cookie::set('nickname', $this->nickname, ['prefix' => 'user_', 'expire' => 3600]);
                $this->is_admin();
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }

    public function register($username, $password, $nickname) {
        try{
            $user = new UserModel;
            $user->nickname = $nickname;
            $user->password = md5($password);
            $user->username = $username;
            if ($user->save()) {
                return true;
            }
        }
        catch (Exception $exception){
            return false;
        }
        return false;
    }

    public function logout() {
        $this->is_login = false;
        $this->nickname = 'anonymous';
        $this->id = -1;
        $this->is_admin = false;

        Cookie::clear('user_');
        return true;
    }

}

