<?php


class SyncController extends SiteController{


    public function init()
    {
        if (!$this->getRequest()->isPost()) {
            exit('fail');
        }
    }


    public function syncDataToPdlAction()
    {
        $data = $_POST;
        if (!$data || !isset($data['sign'])) {
            echo json_encode(['status' => 0, 'msg' => 'access deny']);exit(0);
        }
        $sign_key = "x3tF*mG2ctNLb*FIPo5L";
        if ($data['sign'] != md5($data['timestamp'].$sign_key)) {
            echo json_encode(['status' => 0, 'msg' => 'access deny']);exit(0);
        }
        $page = $data['page'] < 1 ? 1 : $data['page'];
        $data = $this->getData($data['type'],$data['last_id'],$page);
        echo json_encode(['status' => 1, 'msg' => 'success','data' => $data]);exit(0);
    }


    public function getData($type,$last_id,$page){
        switch ($type){
            case "navigation":
                $data =  NavigationModel::where('id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            case "tab":
                $data = TabModel::where('tab_id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            case "nag_conf":
                $data = NagConfModel::where('id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            case "tags":
                $data = TagsModel::where('id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            case "mv":
                $data = MvModel::queryBase()
                    ->where('id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            case "construct":
                $data = ConstructModel::queryBase()
                    ->where('id','>',$last_id)
                    ->forPage($page,100)
                    ->toBase()
                    ->get();
                break;
            default:
                $data = [];
        }
        if (!empty($data)){
            return $data->toArray();
        }else{
            return [];
        }
    }

}