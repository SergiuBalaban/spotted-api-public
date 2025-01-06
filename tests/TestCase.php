<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    //    use RefreshDatabase;
    use LazilyRefreshDatabase;

    public function checkFailedResponseData(array $responseData, string $errorMessage, ?string $errorCode = null): void
    {
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('code', $responseData['error']);
        $this->assertArrayHasKey('status_code', $responseData['error']);
        $this->assertTrue($responseData['error']['message'] === $errorMessage);
        if ($errorCode) {
            $this->assertTrue($responseData['error']['code'] === $errorCode);
        }
    }
}
