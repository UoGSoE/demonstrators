<?php

// @codingStandardsIgnoreFile

namespace Tests\Unit;

use App\DemonstratorRequest;
use Carbon\Carbon;
use Tests\TestCase;

class DemonstratorRequestTest extends TestCase
{
    /** @test */
    public function updating_the_year_of_a_request_keeps_the_same_day_of_the_week()
    {
        $request = create(DemonstratorRequest::class);
        $original = Carbon::parse($request->start_date);
        $request->updateYear();
        $new = Carbon::parse($request->fresh()->start_date);
        $this->assertEquals(1, $new->year - $original->year);
        $this->assertEquals($new->dayOfWeek, $original->dayOfWeek);
    }
}
