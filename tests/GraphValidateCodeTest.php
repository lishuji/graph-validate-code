<?php

namespace Test;

use Illuminate\Support\Facades\Redis;
use Kanelli\GraphValidateCode\Exceptions\InvalidParamException;
use Kanelli\GraphValidateCode\GraphValidateCode;
use PHPUnit\Framework\TestCase;


class GraphValidateCodeTest extends TestCase
{
    /**
     * @return void
     * @throws InvalidParamException
     */
    public function testConfig()
    {
        $this->expectException(InvalidParamException::class);

        new GraphValidateCode([
            'rand_number' => '1234567890',
            'width' => 100,
        ]);

        $this->expectExceptionMessage('server config invalid');
    }

    /**
     * @return void
     * @throws InvalidParamException
     */
    public function testGenVerifyCodeImg(): void
    {
        Redis::shouldReceive('setex')->once()->andReturn(true);

        $server = new GraphValidateCode([
            'rand_number' => '1234567890abcdef',
            'width' => 60,
            'height' => 30
        ]);

        $base64Image = $server->genImgVerifyCode('1234567890abcdef');

        $this->assertIsString($base64Image);
    }

    /**
     * @return void
     * @throws InvalidParamException
     */
    public function testVerifyCode(): void
    {
        $server = new GraphValidateCode([
            'rand_number' => '1234567890abcdef',
            'width' => 60,
            'height' => 30
        ]);

        $code = $server->getRandomVerifyCode();

        Redis::shouldReceive('setex')->once()->andReturn(true);

        $server->genImgVerifyCode('1234567890abcdef', $code);

        Redis::shouldReceive('get')->once()->andReturn($code);

        $checkoutRes = $server->checkImgVerifyCode('1234567890abcdef', $code);

        $this->assertTrue($checkoutRes);
    }
}