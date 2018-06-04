<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/6/2
 * Time: 16:26
 */

namespace app\index\job;

use think\Exception;
use think\Queue;
use think\queue\Job;
use app\index\model\Status as StatusModel;


class PostCode extends CurlBase
{

    public function fire(Job $job, $data) {
        print("<info>Id: " . $data['id'] . " Job start</info>\n");

        if ($this->work($data)) {
            $job->delete();
            print("<info>Id: " . $data['id'] . " Job is Done</info>\n");
        } else {
            if ($job->attempts() > 3) {
                print("<warn>Id: " . $data['id'] . " Job has been retried more than 3 times</warn>\n");
                $job->delete();
            }
        }
    }

    public function work($data) {
        try {
            $status = StatusModel::get(['id' => $data['id']]);

            $current_hdu_id =0;
            try{
                $status2 = StatusModel::get(function ($query) {
                    $query->where('hdu_id', '>', '-1')->order('id', 'desc')->limit(1);
                });
                $current_hdu_id = $status2->hdu_id;
            }
            catch (Exception $exception){
            }

            $language = $data['lang'];
            $problemid = $data['problem_id'];
            $usercode = $data['user_code'];

            //HDU题库状态页面
            $content = $this->GetStatus();
            //上传代码
            $this->PostCode($problemid, $language, $usercode);
            sleep(5);
            //HDU题库状态页面----返回获得结+果
            $content = $this->GetStatus();
            $pattern = '/\<td(.class=\".*?\")?\>(.*?)\<\/td\>/s';
            preg_match_all($pattern, $content, $matches);
            ////echo $matches[1][0];
            //var_dump($matches);
            $len = count($matches[0]);
            for ($i = 7; $i < $len; $i += 8) {
                if (strip_tags($matches[0][$i]) == $this->HDU_USER_NAME) {
                    $hduid = strip_tags($matches[0][$i - 7]);
                    if ($hduid > $current_hdu_id) {
                        $status->status = strip_tags($matches[0][$i - 5]);
                        $status->hduid = strip_tags($matches[0][$i - 7]);
                        $status->runtime = strip_tags($matches[0][$i - 3]);
                        $status->memory = strip_tags($matches[0][$i - 2]);
                        $status->hdu_id = $hduid;

                        $status->save();

                        if($status->status == "Queuing"){
                            $jobHandlerClassName = 'index/UpdateStatus';
                            $jobQueueName = "post";
                            Queue::push($jobHandlerClassName, [
                                'id'=>$data['id'],
                                'hdu_id'=>strip_tags($matches[0][$i - 7])
                            ], $jobQueueName);
                        }

                        return true;
                    } else {
                        sleep(10);
                        return false;
                    }
                }
            }
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }
}