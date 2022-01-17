<?php

namespace Tests\Feature\Console\Commands;

use App\Helpers\FileToArrayConverter;
use App\Jobs\UserImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ImportUsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-01-16');
    }

    /** @test */
    public function it_should_be_able_to_execute_the_import_command_successfully()
    {
        $filePath  = base_path('tests/Resources/users.xml');

        $this->artisan("import:users $filePath")
            ->assertSuccessful();

        $this->assertDatabaseCount('users', 8);
    }
}
