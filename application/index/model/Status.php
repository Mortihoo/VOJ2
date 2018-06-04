<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/6/2
 * Time: 12:42
 */

namespace app\index\model;

use think\Model;


class Status extends Model
{
    public function create_status($user_id,$problem_id,$lang,$user_code){
        $status = new Status;
        $status->problem_id = $problem_id;
        $status->user_id = $user_id;
        $status->lang = $lang;
        $status->user_code = $user_code;
        if($status->save()){
            return $status->id;
        }
        else{
            return -1;
        }
    }

}