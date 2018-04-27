<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Waterfall extends CI_Controller {
    private $requestFlag = true;
    public function index(){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){

            if($this->requestFlag && !empty($_GET['dirName'])){

                $this->requestFlag = false;

                $basePath = dirname(dirname(dirname(__FILE__))) . '/resourse/images';
                $tarPath = $basePath . '/' . $_GET['dirName'] . '/JPEG';
                
                $handler = opendir($tarPath);
    
                // 所有照片
                $allFiles = [];

                while(($fileName = readdir($handler)) !== false){
                    if($fileName != '.' && $fileName != '..'){
                        if(strpos($fileName, 'DS_S') !== false){
                            unlink($tarPath . '/' . $fileName);
                        }else{
                            $allFiles[] = $fileName;
                        }
                    }
                }

                $allImagesCount = count($allFiles);

                // 分割
                $segImage = array_chunk($allFiles, 20);
                $pages = $_GET['pages'];
                if($pages > count($segImage)){
                    $allFiles = [];
                    $files = [];
                }else{
                    $allFiles = $segImage[($pages - 1)];
                }

                // 获取图片的大小
                foreach($allFiles as $fileList){
                    list($width, $height, $type) = getimagesize($tarPath . '/' . $fileList);
                    $files[] = [
                        'name' => $fileList,
                        'width' => $width,
                        'height' => $height,
                        'type' => $type
                    ];
                }
    
                closedir($handler);

                $this->requestFlag = true;
                $this->json([
                    'code' => true,
                    'list' => $files,
                    'count' => $allImagesCount
                ]);
            }else{
                $this->json([
                    'code' => false,
                    'msg' => '系统繁忙，请稍后'
                ]);
            }
        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->json([
                'code' => false,
                'msg' => '请求方式错误'
            ]);      
        }
    }
}
