<h1 align="center"> 图形验证码 </h1>

### 安装

```shell
$ composer require kanelli/graph-validate-code

$ php artisan vendor:publish --provider="Kanelli\GraphValidateCode\GraphValidateCodeServiceProvider"
```

### 配置

1. 依赖Redis缓存，需要在 `.env` 中配置Redis的连接信息。

```
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
```

2. 在 `config/validate.php` 中增加如下部分：

```
    [
         'rand_number'     => env('RAND_NUMBER', '1234567890'), // 随机因子
         'width'           => env('WIDTH', 60), // 图片宽度
         'height'          => env('HEIGHT', 40), // 图片高度
    ];
```

3. 在 `config/app.php` 中增加如下部分：

```
    'providers' => [
        // ...
        Kanelli\GraphValidateCode\GraphValidateCodeServiceProvider::class,
    ],
    
    'aliases' => [
        // ...
        'GraphValidateCodeFacade' => Kanelli\GraphValidateCode\Facades\GraphValidateCodeFacade::class,
    ],
```

### 使用

1. 使用Facade获取图片,并校验：

```
    GraphValidateCodeFacade::config(config('validate'))->getValidateImage('1234', '6666');
    
    GraphValidateCodeFacade::config(config('validate'))->checkCode('1234', '6666');
```

2. 使用provider获取图片,并校验：

```
    app('gvc')->config(config('validate'))->getValidateImage('1234', '3309');
    
    app('gvc')->config(config('validate'))->checkCode('1234', '3309');
```

### License

MIT