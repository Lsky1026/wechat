<?php
/**
 * Wafer php demo 配置文件
 */

$config = [
    'rootPath' => '',

    // 微信小程序 AppID
    'appId' => 'wxfd430b9612a7c960',

    // 微信小程序 AppSecret
    'appSecret' => '2f7aa2cda3b6796c62d29970afae4372',

    // 使用腾讯云代理登录
    'useQcloudLogin' => false,

    /**
     * MySQL 配置，用来存储 session 和用户信息
     * 若使用了腾讯云微信小程序解决方案
     * 开发环境下，MySQL 的初始密码为您的微信小程序 AppID
     */
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'lsky',
        'db'   => 'cAuth',
        'pass' => 'hutao0903&',
        'char' => 'utf8mb4'
    ],

    'cos' => [
        /**
         * 区域
         * 上海：cn-east
         * 广州：cn-sorth
         * 北京：cn-north
         * 广州二区：cn-south-2
         * 成都：cn-southwest
         * 新加坡：sg
         * @see https://www.qcloud.com/document/product/436/6224
         */
        'region' => 'cn-sorth',
        // Bucket 名称
        'fileBucket' => 'wafer',
        // 文件夹
        'uploadFolder' => 'hutao'
    ],

    // 微信登录态有效期
    'wxLoginExpires' => 7200,
    'wxMessageToken' => 'abcdefgh'
];
