<?
namespace ul\regedit;
class regedit extends regedit_factory{    public function getInstance(){        return \singletone::getInstance(__CLASS__);
    }

    public function setVal($key, $value){
    }

    public function getVal($key){
    }

    public function delVar($key){
    }
}
?>