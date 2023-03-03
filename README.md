<h1 align="center"> 图形验证码 </h1>

## Installing

```shell
$ composer require kanelli/graph-validate-code
```

## 配置

在 `config/Validate.php` 的 `graph` 中增加如下部分：

```php
    //...
    'graph' => [
         'rand_number'     => env('RAND_NUMBER', '1234567890'),
         'width'           => env('WIDTH', 100),
         'height'          => env('HEIGHT', 40),
    ];
```

## License

MIT