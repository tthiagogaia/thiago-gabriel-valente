<?php

namespace Tests\Unit\Helpers;

use App\Helpers\FileToArrayConverter;
use JsonMachine\Items;
use PHPUnit\Framework\TestCase;

class FileToArrayConverterTest extends TestCase
{
    /** @test */
    public function it_should_be_able_to_convert_json_file()
    {
        $filePath = __DIR__ . '/../../Resources/users.json';

        $this->assertInstanceOf(
            Items::class,
            FileToArrayConverter::make($filePath)
        );
    }

    /** @test */
    public function it_should_be_able_to_convert_xml_file()
    {
        $filePath = __DIR__ . '/../../Resources/users.xml';

        $this->assertIsArray(FileToArrayConverter::make($filePath));
    }
}
