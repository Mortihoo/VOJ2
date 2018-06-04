<?php
/**
 * Created by PhpStorm.
 * User: morti
 * Date: 2018/6/4
 * Time: 9:08
 */

namespace app\index\job;

use think\Exception;
use think\queue\Job;
use app\index\model\Status as StatusModel;

class UpdateStatus extends CurlBase
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
        $status = StatusModel::get(['id' => $data['id']]);
        $content = $this->GetStatus("&first=" . $data['hdu_id']);
        $pattern = '/\<td(.class=\".*?\")?\>(.*?)\<\/td\>/s';
        preg_match_all($pattern, $content, $matches);
        ////echo $matches[1][0];
        //var_dump($matches);
        $len = count($matches[0]);
        for ($i = 7; $i < $len; $i += 8) {
            $hduid = strip_tags($matches[0][$i - 7]);
            if ($hduid == $data['hdu_id'] && strip_tags($matches[0][$i - 5]) != "Queuing") {
                $status->status = strip_tags($matches[0][$i - 5]);
                $status->hduid = strip_tags($matches[0][$i - 7]);
                $status->runtime = strip_tags($matches[0][$i - 3]);
                $status->memory = strip_tags($matches[0][$i - 2]);

                $status->save();
                return true;
            } else {
                sleep(10);
                return false;
            }
        }

        return false;
    }
}