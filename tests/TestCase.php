<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use PHPUnit\Framework\Assert;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        EloquentCollection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contains the specified value.");
        });
        EloquentCollection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection does not contain the specified value.");
        });
        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
        \Hash::setRounds(4);
    }
}
