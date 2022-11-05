<?php

namespace Hettiger\Honeypot\Tests\Features;

use function Hettiger\Honeypot\config;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;

trait FeatureTestHelpers
{
    public function token(): string
    {
        $header = config('header');

        return test()->post(route('fake'), headers: [$header => ''])->headers->get($header);
    }

    public function attempt(?string $token = null, ?string $value = null): TestResponse
    {
        $headers = $token ? [config('header') => $token] : [];
        $data = $value ? [config('field') => $value] : [];

        return test()->post(route('fake'), $data, $headers);
    }

    public function assertDidAccept(TestResponse $response, $withHeader = true)
    {
        ($withHeader ? $this->assertHeaderIsPresent($response) : $this->assertHeaderIsMissing($response))
            ->assertOk();
    }

    public function assertDidBlock(TestResponse $response, $withHeader = true)
    {
        ($withHeader ? $this->assertHeaderIsPresent($response) : $this->assertHeaderIsMissing($response))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function assertHeaderIsPresent(TestResponse $response): TestResponse
    {
        return $response->assertHeader(config('header'), Str::uuid()->toString());
    }

    public function assertHeaderIsMissing(TestResponse $response): TestResponse
    {
        return $response->assertHeaderMissing(config('header'));
    }
}
