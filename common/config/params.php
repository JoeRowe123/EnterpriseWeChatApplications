<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'union-pay-number' => '302510147220009',
    'backApi' => 'http://zhb.sinety.cn/api/',
//    'backApi' => 'http://backend.qywechat.com/api/',
    'wework' => [
        //企业id。
        'corpId' => "wxcd0971fc49bd8ea6",

        //后台应用id。
        'backId' => "1000004",

        //应用秘钥
        'secret' => "OvXKGLcOZPCnczEYBGd2Ha8HyKPkgE23u9718LxPb_I",

        //前端应用id。
        'frontId' => "1000005",

        //前端应用秘钥
        'frontSecret' => "z2ArwpuFJxHJ3K1XCrR9XryNq0TYo7qvjHksNCr4pUM",

        //前端文章详情链接
        'frontArticleUrl' => "http://zhbq.sinety.cn/index/get-user-info?",

        //前端投票详情链接
        'frontVoteUrl' => "http://zhbq.sinety.cn/index/get-user-info?type=vote&id=",

        //前端活动详情链接
        'frontActivityUrl' => "http://zhbq.sinety.cn/index/get-user-info?type=activity&id=",

        //前端考试详情链接
        'frontPaperUrl' => "http://zhbq.sinety.cn/index/get-user-info?type=examination&id=",
    ],
    // 图片服务器的域名设置，拼接保存在数据库中的相对地址，可通过web进行展示
    'domain' => 'http://zhb.sinety.cn/',
    'webuploader' => [
        // 后端处理图片的地址，value 是相对的地址
        'uploadUrl' => 'file/upload',
        // 多文件分隔符
        'delimiter' => ',',
        // 基本配置
        'baseConfig' => [
            'defaultImage' => 'http://zhb.sinety.cn/img/no-img.png',
            'disableGlobalDnd' => true,
            'accept' => [
                'title' => 'Images',
                'extensions' => 'gif,jpg,jpeg,bmp,png',
                'mimeTypes' => 'image/jpg,image/jpeg,image/png,image/gif,image/bmp',
            ],
            'pick' => [
                'multiple' => false,
            ],
        ],
    ],
];
