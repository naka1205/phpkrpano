<?php
namespace Models;
class Vtour
{
    public static function data(){
        $data_path = PUBLIC_PATH . DS ."data";
        $dir = opendir($data_path);
    
        $result = array();
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($data_path . DS . $file)) {
                    $result[$file] = "/data/" . $file . "/vtour/";
                }
            }
        }
        closedir($dir);
        return $result;
    }

    public static function update($post){

        $data = json_decode($post["tour"]);

        $title = $post["title"];
        $scene_index = $post["scene_index"];

        $result = [];
        $result['status'] = "error";

        $xmlfile = PUBLIC_PATH . "/data/" . $title . '/vtour/tour.xml';
        $tourDom = new \DOMDocument();
        $tourDom->load($xmlfile);
        $sceneList = $tourDom->getElementsByTagName("scene");
        

        foreach ($data as $key => $value) {
            $sceneIndex = $value->index;
        //    property_exists($value, "welcomeFlag")?
            $welcomeFlag = isset($value->welcomeFlag) ? $value->welcomeFlag : '';
            $sceneName = $value->name;
            $autorotate = isset($value->autorotate) ? $value->autorotate : '';
            $hotSpots = isset($value->hotSpots) ? $value->hotSpots : '';
            $fov = isset($value->fov) ? $value->fov : '';
            $sceneItem = $sceneList->item($sceneIndex);
        
            if (!is_int($sceneIndex)) {
                return $result;
            }
        
        
            //自动旋转
            if ($autorotate) {
                $enabled = $autorotate->enabled;
                $v1 = $sceneItem->getElementsByTagName("autorotate");
                $v2 = $v1->item(0);
                if ($enabled) {
                    $v2->setAttribute("enabled", "true");
                } else {
                    $v2->setAttribute("enabled", "false");
                }
            }
        
            //重新命名
            $oldSceneName = $sceneItem->getAttribute("name");
            if ($oldSceneName != $sceneName) {
                foreach ($sceneList as $t0) {
                    $t1 = $t0->getElementsByTagName("hotspot");
                    foreach ($t1 as $t2) {
                        $t3 = $t2->getAttribute("linkedscene");
                        if ($t3 == $oldSceneName) {
                            $t2->setAttribute("linkedscene", $sceneName);
                        }
                    }
                }
                $sceneItem->setAttribute("name", $sceneName);
            }
        
        
            //初始场景
            if ($welcomeFlag) {
                $actionList = $tourDom->getElementsByTagName("action");
                $actionItem = $actionList->item(0);
                $actionItem->nodeValue =
                    "if(startscene === null OR !scene[get(startscene)], 
                    copy(startscene,scene[" . $sceneIndex . "].name); );
                    loadscene(get(startscene), null, MERGE);if(startactions !== null, startactions() );js('onready(" . $sceneIndex . ")');";
            }
        
            if ($sceneIndex != $scene_index) continue;
        
        
            if ($fov != null) {
                $viewList = $sceneItem->getElementsByTagName("view");
                $viewItem = $viewList->item(0);
                $viewItem->setAttribute("fov", $value->fov);
                $initH = $value->initH;
                $initV = $value->initV;
                if ($initH) $viewItem->setAttribute("hlookat", $initH);
                if ($initV) $viewItem->setAttribute("vlookat", $initV);
            }
            
            $flag = $post["isaddhotspot"];
        
            if ($flag == "false") {
                continue;
            }
        
            $hotSpotsList = $sceneItem->getElementsByTagName("hotspot");
            $layerList = $sceneItem->getElementsByTagName("layer");//
            while ($hotSpotsList->length != 0) {
                $sceneItem->removeChild($hotSpotsList->item(0));
            }
            while ($layerList->length != 0) {
                $sceneItem->removeChild($layerList->item(0));
            }
        
            if ($hotSpots != null) {
                foreach ($hotSpots as $key => $value) {
                    $tempName = $value->name;
                    $oldTitle = $value->title;
                    $size = intval( self::utf8_strlen($oldTitle) / 8);
                    $mod = self::utf8_strlen($oldTitle) % 8;
                    $newTitle = "";
                    for ($i = 0; $i < $size; $i++) {
                        if ($newTitle != "") {
                            $newTitle = $newTitle . "[br]";
                        }
                        $newTitle = $newTitle . mb_substr($oldTitle, $i * 8, 8, "utf-8");
                    }
        
                    if($newTitle != ""){
                        $newTitle = $newTitle . "[br]";
                    }
        
                    if ($mod != 0) {
                        $newTitle = $newTitle . mb_substr($oldTitle, $size * 8, $mod, "utf-8");
                    }
        
                    $node = $tourDom->createElement("hotspot");
                    $node->setAttribute("ath", $value->ath);
                    $node->setAttribute("atv", $value->atv);
                    $node->setAttribute("linkedscene", $value->linkedscene);
                    $node->setAttribute("style", $value->style);
                    $node->setAttribute("dive", $value->dive);
                    $node->setAttribute("name", $tempName);
                    $sceneItem->appendChild($node);
                    $player = $tourDom->createElement("layer");
                    $clyaer = $tourDom->createElement("layer");
                    $player->setAttribute("name", $tempName . "_1");
                    $player->setAttribute("parent", "hotspot[" . $tempName . "]");
                    $player->setAttribute("width", "200");
                    $player->setAttribute("height", "200");
                    $player->setAttribute("maskchildren", "true");
                    $player->setAttribute("scalechildren", "false");
                    $player->setAttribute("vcenter", "true");
                    $player->setAttribute("visible", "true");
                    $player->setAttribute("type", "container");
                    $player->setAttribute("align", "centerbottom");
                    $player->setAttribute("bgcolor", "0x1eb98f");
                    $player->setAttribute("y", "-1");
                    $player->setAttribute("origin", "cursor");
                    $player->setAttribute("edge", "centertop");
                    $player->setAttribute("textalign", "center");
                    $player->setAttribute("padding", "0");
                    $player->setAttribute("roundedge", "8");
                    $clyaer->setAttribute("name", $tempName . "_2");
                    $clyaer->setAttribute("html", $newTitle);
                    $clyaer->setAttribute("backgroundalpha", "0");
                    $clyaer->setAttribute("visible", "true");
                    $clyaer->setAttribute("type", "container");
                    $clyaer->setAttribute("css", "text-align:center; color:#FFFFFF; font-family:tahoma; font-weight:normal; font-size:25px;");
                    $clyaer->setAttribute("origin", "cursor");
                    $clyaer->setAttribute("url", "%SWFPATH%/plugins/textfield.swf");
                    $player->appendChild($clyaer);
                    $sceneItem->appendChild($player);
                }
            }
        }
        
        
        $tourDom->save($xmlfile);
        $result['status'] = "success";

        return $result;
    }


    public static function upload($files,$post = [])
    {
        $data_path = PUBLIC_PATH . DS ."data";
        
        $timestamp = $post["timestamp"] ? $post["timestamp"] : time();
        $index = $post["index"] ? $post["index"] : 0;

        $save_path = $data_path . DS . $timestamp;
        if ( !is_dir($save_path) ) {
            mkdir ($save_path , 0777, true);
        }

        $result = [];
        foreach ( $files as $key => $value ) {
            if ( !$value['file_name'] || !$value['file_data'] ) {
                continue;
            }
            $ext = explode(".",$value['file_name'])[1]; 

            $name = $index == 0 ? $key : $index;
            $file_name = $name . "." . $ext;
            $file_path = $save_path . DS . $file_name;
            file_put_contents($file_path, $value['file_data']);
            $value['file_path'] = "/data/" . $timestamp . "/" . $file_name;
            unset($value['file_data']);
            $result[] = $value;
        }
        return $result;
    }

    public static function add($post){
        if ( IS_WIN ) {
            $tools = TOOLS_PATH . "/krpanotools64";
        }else{
            $tools = TOOLS_PATH . "/krpanotools";
        }
        $tools .= " makepano -config=" . TOOLS_PATH . "/templates/vtour-normal.config";
        $data_path = PUBLIC_PATH . DS ."data";

        $timestamp = $post["timestamp"];
        $result = [];
        $result['status'] = "error";

        $file_path = $data_path . DS . $timestamp;
        if ( !file_exists($file_path) ) {
            return $result;
        }

        $file = scandir($file_path);
        $cmd = $tools;
        foreach ($file as $key => $value) {
            if ($value != "." && $value != "..") {
                $cmd = $cmd . " " . $file_path . DS . $value;
            }
        }
        exec($cmd, $log, $status);
    
        $src = VTOUR_PATH . DS;
        $dst = $file_path . DS . "vtour";
        if( !is_dir($dst) ){
            mkdir ($dst , 0777, true);
        }

        self::copys($src, $dst);
        self::tour($timestamp);

        $result['status'] = "success";
        return $result;
    }

    public static function utf8_strlen($string = null) {
        preg_match_all("/./us", $string, $match);
        return count($match[0]);
    }

    public static function copys($src, $dst){
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    @mkdir($dst . '/' . $file);
                    self::copys($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function tour($timestamp){
        $xmlfile = PUBLIC_PATH . '/data/' . $timestamp . '/vtour/tour.xml';
        $dom = new \DOMDocument(null);
        $dom->load($xmlfile);
    
        $actionList = $dom->getElementsByTagName('action');
        $defaultActionItem = $actionList->item(0);
        $defaultActionItem->nodeValue =
            "if(startscene === null OR !scene[get(startscene)],
            copy(startscene,scene[0].name); );
            loadscene(get(startscene), null, MERGE);
            if(startactions !== null, startactions() );js('onready(0)');";
    
        $sceneList = $dom->getElementsByTagName("scene");
        foreach ($sceneList as $sceneItem) {
            $node = $dom->createElement("autorotate");
            $node->setAttribute("enabled", "false");
            $node->setAttribute("waittime", "1.5");
            $node->setAttribute("accel", "1.0");
            $node->setAttribute("speed", "5.0");
            $node->setAttribute("horizon", "0.0");
    
            $sceneItem->setAttribute("onstart", "activatespot(90)");
    
            $sceneItem->appendChild($node);
        }
        $dom->save($xmlfile);
    }

}