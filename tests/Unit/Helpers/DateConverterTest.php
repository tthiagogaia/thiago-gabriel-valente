<?php

namespace Tests\Unit\Helpers;

use App\Helpers\DateConverter;
use PHPUnit\Framework\TestCase;

class DateConverterTest extends TestCase
{
    /** @test */
    public function it_should_be_able_to_convert_day_month_year_date()
    {
        $nowFormatted = now()->format('d/m/Y');

        $this->assertEquals(
            DateConverter::make($nowFormatted)->format('d/m/Y'),
            $nowFormatted
        );
    }

    /** @test */
    public function it_should_be_able_to_convert_date_with_timezone()
    {
        $nowFormatted = now('Europe/Amsterdam')->toAtomString();

        $this->assertEquals(
            DateConverter::make($nowFormatted)->toAtomString(),
            $nowFormatted
        );
    }
}
