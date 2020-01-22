<?php

namespace Tests\Unit;

use App\Models\Data\SocialDataReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialDataUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_country_name()
    {
        $this->assertEquals(SocialDataReport::getCountryName(SocialDataReport::SD_COUNTRY_SG), 'Singapore');
    }

    public function test_can_get_null_when_country_id_does_not_exists()
    {
        $this->assertEquals(SocialDataReport::getCountryName('randomnumber'), null);
    }
}
