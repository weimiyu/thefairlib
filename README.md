### 说明

TheFairLib 是基于yaf框架,集成了一些基本的类

* 阿里云OSS,使用成本很低,非常实用,个人站点的js,css,image都可以用的

* BigPipe 主要用于h5页面

* DB操作使用  predis / illuminate/database

* App的IM接入了融云

* App的消息推送,接入个推/极光

* 消息列表使用接入kafka nmred/kafka-php

* 全文搜索使用coreseek

* RPC服务器,使用swoole

* 后台或h5使用smarty模板,已升级到最新

* 验证码支持图片/短信验证码,只支持redis,短信目前只接入了云片网

* 微信营销接入有赞

* 工具库,支持汉字转拼音,能满足基本需求

### 阿里云OSS上传

**config目录下新建AliYun.php文件，不能使用其他名称**

```
<?php

/**
 * 阿里云CDN配置文件
 *
 * @author mingzhil
 * @mail liumingzhij26@qq.com
 */
namespace config;

class AliYun
{
    /**
     * 只允许修改参数，其他不能改变
     */
    public $OSS = [
        'OSS_ACCESS_ID' => '**********',
        'OSS_ACCESS_KEY' => '************',
        'OSS_ENDPOINT' => 'oss-cn-beijing.aliyuncs.com',
        'OSS_TEST_BUCKET' => 'static-pub',
        'ALI_LOG' => false,
        'ALI_DISPLAY_LOG' => false,
        'ALI_LANG' => 'zh',
    ];
}

```



**Demo**

```
$file = new TheFairLib\Aliyun\AliOSS\Upload('file', [
    "host" => 'http://static.biyeyuan.com/',//CDN的域名、、
    "savePath" => '/tmp',//上传文件的路径
    "ossPath" => APP_NAME,//项目名称，也就是自定义阿里云目录
    "maxSize" => 2000, //单位KB
    "allowFiles" => [".gif", ".png", ".jpg", ".jpeg", ".bmp", ".css", ".js"]
]);
$data = $file->getFileInfo();
\Response\Response::Json($data);

```

### 验证码使用

**font目录**

* 只需要将字体放到font目录下，使用数字顺序命名，即可
* 默认随机字体

**Demo**

输出验证码

```
$code = new \TheFairLib\Verify\Image();
$code->type = 'code';//类型，如login,reg,bind
$code->output(1);

```
查看或验证

```
$code = new \TheFairLib\Verify\Image();
$code->type = 'code';
$code->validate($_GET['code']);
echo $code->getCode();
```

### 上传普通文件

**Demo**

```
$file = new TheFairLib\Uploader\Upload('files', [
    "savePath" => '/tmp',//上传文件的路径
    "maxSize" => 2000, //单位KB
    "allowFiles" => [".gif", ".png", ".jpg", ".jpeg", ".bmp", ".css", ".js"]
]);
$status = $file->getFileInfo();

```

### 引入smarty模板

**在yaf中的plugin目录下新建一下Tpl.php文件**

```
<?php
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;
use Yaf\Registry;
use Yaf\Dispatcher;

class TplPlugin extends Plugin_Abstract
{

    /**
     * 路由结束之后触发       此时路由一定正确完成, 否则这个事件不会触发
     *
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     * @return mixed|void
     */
    public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
    {
        $config = Registry::get("config")->smarty->toArray();
        $config['template_dir'] = $config['template_dir'] . $request->module . '/';
        $smarty = new TheFairLib\Smarty\Adapter(null, $config);
        Dispatcher::getInstance()->setView($smarty);
    }

}
```

**在Bootstrap.php中挂起插件**

```
/**
 * 加载插件
 * @param \Yaf\Dispatcher $dispatcher
 */
public function _initPlugin(Yaf\Dispatcher $dispatcher)
{
    $dispatcher->registerPlugin(new TplPlugin());
}
```

### 发送短信验证码

**config目录下新建Verify.php文件，不能使用其他名称，使用之前请将服务器加入白名单中**

```
<?php
namespace config;

class Verify
{


    /**
     * 默认手机验证码提供商云片网
     *
     * @var string
     */
    public $mobileVerify = [
        'name' => 'YunPian',
    ];

    /**
     * 手机验证码提供商
     *
     * @var array
     */
    public $mobileVerifyList = [
        'YunPian',

    ];
    public $appKey = [
        'YunPian' => [
            'key' => '***11e86244daa8fe53c14e5fcc14edfa1d***'
        ]
    ];

}

```

**Demo**

```
TheFairLib\Verify\Mobile::Instance()->sendMessage('18888888888','您的验证码是'.mt_rand(1000,9999));

返回结果
{
    code: 0,
    msg: "OK",
    result: {
    count: 1,
        fee: 1,
        sid: 3489475182
    }
}
```

