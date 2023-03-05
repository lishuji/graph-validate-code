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
    public function config(array $config = []): static
    {
        if (empty($config['rand_number']) || empty($config['width']) || empty($config['height'])) {
            throw new InvalidParamException('server config invalid');
        }

        $this->config = $config;

        return $this;
    }

    /**
     * 生成图片
     * @param $verifyCode
     * @return string
     */
    private function genVerifyCodeImg($verifyCode): string
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
     * 图片转base64
     * @param $image_file
     * @return string
     */
    private function base64EncodeImage($image_file): string
    {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        return 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    }

    /**
     * 生成随机验证码
     * @param int $length 验证码长度
     * @return string
     */
    private function getRandomVerifyCode(int $length = 4): string
    {
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $this->config['rand_number'][rand(0, strlen($this->config['rand_number']) - 1)];
        }

        return $code;
    }

    /**
     * 获取图片验证码
     * @param string $sessionId session id
     * @param string|null $verifyCode 验证码
     * @return string
     */
    public function getValidateImage(string $sessionId, string $verifyCode = null): string
    {
        $verifyCode = !is_null($verifyCode) ? $verifyCode : $this->getRandomVerifyCode();

        $img = $this->genVerifyCodeImg($verifyCode);

        Redis::setex(self::IMG_VERIFY_CODE_CACHE_KEY . $sessionId, 300, $verifyCode);

        return $img;
    }

    /**
     * 校验图片验证码
     * @param string $sessionId session id
     * @param int $verifyCode 验证码
     * @return bool
     */
    public function verifyCode(string $sessionId, int $verifyCode): bool
    {
        $cacheVerifyCode = Redis::get(self::IMG_VERIFY_CODE_CACHE_KEY . $sessionId);

        if (empty($cacheVerifyCode)) {
            return false;
        }

        return $cacheVerifyCode === $verifyCode;
    }
}