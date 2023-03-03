<?php

namespace Kanelli\GraphValidateCode;


use Illuminate\Support\Facades\Redis;
use Kanelli\GraphValidateCode\Exceptions\InvalidParamException;


class GraphValidateCode
{
    /**
     * 图片验证码缓存 key
     */
    const IMG_VERIFY_CODE_CACHE_KEY = "Cache::imgVerifyCodeCache::";

    protected $config;

    /**
     * @param array $config
     * @throws InvalidParamException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['rand_number']) || empty($config['width']) || empty($config['height'])) {
            throw new InvalidParamException('server config invalid');
        }

        $this->config = $config;

        return $this;
    }

    /**
     * 生成校验图片
     * @param $verifyCode
     * @return string
     */
    public function genVerifyCodeImg($verifyCode)
    {
        //验证码图片保存路径，文件名称
        $file_name = '/tmp/' . $verifyCode . '.png';

        $imageHandle = imagecreatetruecolor($this->config['width'], $this->config['height']); //创建画布
        $backGroundColor = imagecolorallocate($imageHandle, 255, 255, 255); //设置背景颜色
        $txtColor = ImageColorAllocate($imageHandle, 0, 0, 0);  //文本颜色（黑色）

        for ($i = 0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($imageHandle, rand(80, 220), rand(80, 220), rand(80, 220));
            imageline($imageHandle, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $lineColor);
        }

        // 设置干扰线
        for ($i = 0; $i < 200; $i++) {
            $pointColor = imagecolorallocate($imageHandle, rand(50, 200), rand(50, 200), rand(50, 200));
            imagesetpixel($imageHandle, rand(1, 99), rand(1, 29), $pointColor);
        }

        imagefill($imageHandle, 0, 0, $backGroundColor); //填充背景颜色
        imagestring($imageHandle, 5, 10, 10, $verifyCode, $txtColor); //写入验证码
        imagepng($imageHandle, $file_name);

        return $this->base64EncodeImage($file_name);
    }

    /**
     * @param $image_file
     * @return string
     */
    public function base64EncodeImage($image_file)
    {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        return 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    }

    /**
     * 生成随机验证码
     * @param int $num 验证码位数
     * @return string
     */
    public function getRandomVerifyCode($num = 4)
    {
        $code = '';

        for ($i = 0; $i < $num; $i++) {
            $code .= $this->config['rand_number'][rand(0, strlen($this->config['rand_number']) - 1)];
        }

        return $code;
    }

    /**
     * 生成图片验证码,并放入Redis
     * @return mixed
     */
    public function genImgVerifyCode($sessionId, $verifyCode = null)
    {
        $verifyCode = !is_null($verifyCode) ? $verifyCode : $this->getRandomVerifyCode();

        $img = $this->genVerifyCodeImg($verifyCode);

        Redis::setex(self::IMG_VERIFY_CODE_CACHE_KEY . $sessionId, 300, $verifyCode);

        return $img;
    }

    /**
     * 校验图片验证码
     * @param $key
     * @param $verifyCode
     * @return bool
     */
    public function checkImgVerifyCode($sessionId, $verifyCode)
    {
        $cacheVerifyCode = Redis::get(self::IMG_VERIFY_CODE_CACHE_KEY . $sessionId);

        if (empty($cacheVerifyCode)) {
            return false;
        }

        return $cacheVerifyCode === $verifyCode;
    }
}