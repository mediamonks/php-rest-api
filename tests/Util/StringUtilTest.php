<?php

namespace MMediaMonks\RestApi\Tests\Util;

use MediaMonks\RestApi\Util\StringUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Constraint;

class StringUtilTest extends TestCase
{
    public function testClassToSnakeCase()
    {
        $this->assertEquals('not_found', StringUtil::classToSnakeCase(new NotFoundHttpException, 'HttpException'));
        $this->assertEquals('bad_request', StringUtil::classToSnakeCase(new BadRequestHttpException, 'HttpException'));
        $this->assertEquals('not_blank', StringUtil::classToSnakeCase(new Constraint\NotBlank));
        $this->assertEquals('email', StringUtil::classToSnakeCase(new Constraint\Email));
    }
}
