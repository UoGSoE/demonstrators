<?php
// @codingStandardsIgnoreFile
namespace Tests\Unit;

use Tests\TestCase;
use App\DemonstratorRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DemonstratorRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function updating_the_year_of_a_request_keeps_the_same_day_of_the_week ()
    {
        $request = create(DemonstratorRequest::class);
        $original = Carbon::parse($request->start_date);
        $request->updateYear();
        $new = Carbon::parse($request->fresh()->start_date);
        $this->assertEquals(1, $new->year - $original->year);
        $this->assertEquals($new->dayOfWeek, $original->dayOfWeek);
    }
}
