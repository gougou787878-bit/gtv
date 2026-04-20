<?php


namespace App\console;


use App\console\Queue\QueueOption;

class FixMvDataConsole extends AbstractConsole
{

    public $name = 'fix-mv-data';

    public $description = 'mv数据打散及统计修复';


    public function process($argc, $argv)
    {
        $uuidData = [
            "9a67934d320008e8112cf0f8a4cfbdb9" => 1,
            "27b5368098ca77f1431bc7f64f69d2ec" => 2,
            "dad2e1d552a006ba4960d90ce0d33306" => 3,
            "b66b3498fd8860ac3bb972be3a282c5a" => 4,
            "dc290e2309f71bf18752a47345726f52" => 5,
            "6077dfa8a320bc3ff69f0af8387e277c" => 6,
            "d416a6e4914609a89919954a0871ba7f" => 7,
            "7b41fc86cf1ab4e01f12a970d5b20c45" => 8,
            "851a4285e8f9e9d2a116ea41814e82f4" => 9,
            "0c77296e1c690036d43c688872018ba2" => 10
        ];
        $count = count($uuidData);
        $uuidArr = array_keys($uuidData);
        $uidArr = array_values($uuidData);

        /** @var \MvSubmitModel $max_subMit */
        $max_subMit = \MvSubmitModel::query()->orderByDesc('id')->first();
        if (is_null($max_subMit)) {
            echo "\r\n no submit Row \r\n";
        } else {
            $max_id = $max_subMit->id;
            for ($i = 1; $i <= $max_id; $i++) {
                $row = \MvSubmitModel::where([
                    'id'     => $i,
                    'status' => \MvModel::STAT_UNREVIEWED,
                ])->first();
                if (is_null($row)) {
                    echo "no do submit {$i} max_id {$max_id} \r\n";
                    continue;
                }
                $key = (int)($i % $count);
                $uid = $uidArr[$key];
                if($uid){
                    $flag = $row->update(['uid' => $uid]);
                    echo "\r\n update submit :{$i} flag:{$flag} \r\n";
                }
            }
        }

        //mv

        /** @var \$ $max_subMit */
        $max_mv = \MvModel::query()->orderByDesc('id')->first();
        if (is_null($max_mv)) {
            echo "\r\n no mv Row \r\n";
        } else {
            $max_mv_id = $max_mv->id;
            for ($i = 1; $i <= $max_mv_id; $i++) {
                $row = \MvModel::where([
                    'id'     => $i
                ])->first();
                if (is_null($row)) {
                    echo "no do mv {$i}  max_id {$max_mv_id} \r\n";
                    continue;
                }
                $key = (int)($i % $count);
                $uid = $uidArr[$key];
                if($uid){
                    $flag = $row->update(['uid' => $uid]);
                    echo "\r\n update mv :{$i} flag:{$flag} \r\n";
                }
            }
        }
        echo "\r\n ############fixcount############## \r\n";
        $this->fixCount();
    }


    protected function fixCount()
    {
        $this->resetCount();
        $this->fansCount();
        $this->followCount();
        $this->videoCount();
        $this->likesCount();
    }


    protected function resetCount()
    {
        \DB::update("update ks_members set videos_count=0 where 1");
    }


    protected function fansCount()
    {

        $row = \DB::update("update ks_members m set fans_count=(select count(uid) from ks_member_attention ma where ma.touid=m.uid) where 1");
        $this->logSuccess("fans_count影响了{$row}");
    }

    protected function followCount()
    {

        $row = \DB::update("update ks_members m set followed_count=(select count(uid) from ks_member_attention ma where ma.uid=m.uid) where 1");
        $this->logSuccess("followed_count影响了{$row}");
    }

    protected function videoCount()
    {
        $row = \DB::update("update ks_members m set videos_count=(select count(id) from ks_mv mv where mv.uid=m.uid and status=1) where 1");
        $this->logSuccess("videos_count影响了{$row}");
    }

    protected function likesCount()
    {
        $row = \DB::update("update ks_members m set likes_count=(select count(id) from ks_user_likes uk where uk.uid=m.uid) where 1");
        $this->logSuccess("likes_count影响了{$row}");
    }


}