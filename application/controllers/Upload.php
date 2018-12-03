<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// use \QCloud_WeApp_SDK\Conf as Conf;
// use \QCloud_WeApp_SDK\Cos\CosAPI as Cos;
// use \QCloud_WeApp_SDK\Constants as Constants;

class Upload extends CI_Controller {
    const DEFAULT_PERCENT = 0.1;
    const COMPRESS_DIR = 'JPEG';

    public function index() {
        // 处理文件上传
        $file = $_FILES['uploadImg']; // 去除 field 值为 file 的文件

        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '10M');

        // 限制文件格式，支持图片上传
        if ($file['type'] !== 'image/jpeg' && $file['type'] !== 'image/png' && $file['type'] !== 'image/jpg') {
            $this->json([
                'code' => 1,
                'data' => '不支持的上传图片类型：' . $file['type']
            ]);
            return;
        }
        
        // 限制文件大小：5M 以内
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->json([
                'code' => 1,
                'data' => '上传图片过大，仅支持 5M 以内的图片上传'
            ]);
            return;
        }

        $baseDir = date('Y-m-d');   // 未来将支持选择文件夹
        $basePath = dirname(dirname(dirname(__FILE__))) . '/resourse/images';
        $tarPath = $basePath . '/' . $baseDir . '/';
        if(!is_dir($tarPath) && 
            !(mkdir($tarPath, 0777, true))){
            return $this->json([
                'code' => false,
                'msg' => "创建{$baseDir}文件夹失败"
            ]);
        }

        // 压缩图片存在目录
        // 压缩目录为 resourse/images/$baseDir/JPEG/
        $compressPath = $tarPath . "/" . self::COMPRESS_DIR . "/";

        if(!is_dir($compressPath) && 
        !(mkdir($compressPath, 0777, true))){
            return $this->json([
                'code' => false,
                'msg' => '创建JPEG文件夹失败'
            ]);
        }

        // 图片重命名
        $fileName = $baseDir . '_' . time() . '.' . explode('/', $file['type'])[1];
        $fileNamePath = $tarPath . $fileName;
        // 原本图片名  用于定位
        $oldImgName = $tarPath . $file['name'];
        if(file_exists($fileNamePath)){
            return $this->json([
                'code' => false,
                'msg' => '文件已经存在'
            ]);
        }

        move_uploaded_file($file['tmp_name'], $oldImgName);
        rename($oldImgName, $fileNamePath);

        if(!$this->compressImage($fileNamePath, $fileName, $compressPath)){
            return $this->json([
                'code' => false,
                'msg' => '压缩图片失败'
            ]);
        }

        return $this->json([
            'code' => true,
            'image' => $fileName,
            'msg' => '上传成功'
        ]);

        // $cosClient = Cos::getInstance();
        // $cosConfig = Conf::getCos();
        // $bucketName = $cosConfig['fileBucket'];
        // $folderName = $cosConfig['uploadFolder'];

        // try {
        //     /**
        //      * 列出 bucket 列表
        //      * 检查要上传的 bucket 有没有创建
        //      * 若没有则创建
        //      */
        //     $bucketsDetail = $cosClient->listBuckets()->toArray()['Buckets'];
        //     $bucketNames = [];
        //     foreach ($bucketsDetail as $bucket) {
        //         array_push($bucketNames, explode('-', $bucket['Name'])[0]);
        //     }

        //     // 若不存在 bucket 就创建 bucket
        //     if (count($bucketNames) === 0 || !in_array($bucketName, $bucketNames)) {
        //         $cosClient->createBucket([
        //             'Bucket' => $bucketName,
        //             'ACL' => 'public-read'
        //         ])->toArray();
        //     }

        //     // 上传文件
        //     $fileFolder = $folderName ? $folderName . '/' : '';
        //     $fileKey = $fileFolder . md5(mt_rand()) . '-' . $file['name'];
        //     $uploadStatus = $cosClient->upload(
        //         $bucketName,
        //         $fileKey,
        //         file_get_contents($file['tmp_name'])
        //     )->toArray();

        //     $this->json([
        //         'code' => 0,
        //         'data' => [
        //             'imgUrl' => $uploadStatus['ObjectURL'],
        //             'size' => $file['size'],
        //             'mimeType' => $file['type'],
        //             'name' => $fileKey
        //         ]
        //     ]);
        // } catch (Exception $e) {
        //     $this->json([
        //         'code' => 1,
        //         'error' => $e->__toString()
        //     ]);
        // }
    }

    /**
     * 压缩图片 并保存
     * @param tarImage 目标图片
     */
    public function compressImage ($tarImage, $imageName, $tarPath){
        if(empty($tarImage) || empty($imageName) || empty($tarPath)){
            return false;
        }
        $imageTypeList = [1 => "GIF",2 => "JPG",3 => "PNG",4 => "SWF",5 => "PSD",6 => "BMP",7 => "TIFF",8 => "TIFF",9 => "JPC",10 => "JP2",11 => "JPX",12 => "JB2",13 => "SWC",14 => "IFF",15 => "WBMP",16 => "XBM"];
        // 执行压缩操作
        list($width, $height, $type, $attr) = getimagesize($tarImage);
        $type = strtolower($imageTypeList[$type]);
        $imageCreate = "imagecreatefrom" . $type;
        // 生成原图样例
        $image = $imageCreate($tarImage);
        // 按比例生成画布
        $image_thump = imagecreatetruecolor($width * self::DEFAULT_PERCENT, $height * self::DEFAULT_PERCENT);
        imagecopyresampled($image_thump, $image, 0, 0, 0, 0, $width * self::DEFAULT_PERCENT, $height * self::DEFAULT_PERCENT, $width, $height);
        $func = "image" . $type;
        $func($image_thump, $tarPath . $imageName);
        imagedestroy($imageCreate);
        return true;
    }
}
