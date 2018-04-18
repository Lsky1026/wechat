
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TimeLine extends CI_Controller {
    private $requestFlag = true;
    public function index(){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $this->json([
                'code' => false,
                'data' => '请求方式错误'
            ]);
        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            if($this->requestFlag){

                $this->requestFlag = false;

                $basePath = dirname(dirname(dirname(__FILE__))) . '/resourse/images';
                $handler = opendir($basePath);
    
                while(($fileName = readdir($handler)) !== false){
                    if($fileName != '.' && $fileName != '..'){
                        $files[] = $fileName;
                    }
                }

                if(!empty($files)){
                    rsort($files);
                }
    
                closedir($handler);

                $this->requestFlag = true;
                $this->json([
                    'code' => true,
                    'list' => $files
                ]);
            }else{
                $this->json([
                    'code' => false,
                    'msg' => '系统繁忙，请稍后'
                ]);
            }
            
        }
    }
}