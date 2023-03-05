<?php

namespace Test;

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

        $this->expectExceptionMessage('server config invalid');

        $server = new GraphValidateCode();

        $server->config([
            'rand_number' => '1234567890',
            'width' => 100,
        ]);

        $this->fail('server config invalid');
    }

    /**
     * @return void
     */
    public function testGetValidateImage(): void
    {
        $server = \Mockery::mock(GraphValidateCode::class);
        $server->shouldReceive('getValidateImage')->once()->andReturn();

        $base64Image = $server->getValidateImage('1234567890abcdef', '123456');

        $this->assertIsString($base64Image);
    }

    /**
     * @return void
     */
    public function testVerifyCode(): void
    {
        $server = \Mockery::mock(GraphValidateCode::class);
        $server->shouldReceive('getValidateImage', 'checkCode')->once()->andReturn(true);

        $server->getValidateImage('1234567890abcdef', '3799');

        $checkoutRes = $server->checkCode('1234567890abcdef', '3799');

        $this->assertTrue($checkoutRes);
    }
}