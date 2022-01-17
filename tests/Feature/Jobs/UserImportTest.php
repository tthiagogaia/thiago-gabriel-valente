<?php

namespace Tests\Feature\Jobs;

use App\Helpers\FileToArrayConverter;
use App\Jobs\UserImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UserImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-01-16');
    }

    /** @test */
    public function it_should_be_able_to_import_users_from_xml_file_to_database()
    {
        $filePath  = base_path('tests/Resources/users.xml');
        $users     = FileToArrayConverter::make($filePath);
        $jobs      = [];

        foreach ($users as $user) {
            $jobs[] = new UserImport($user);
        }

        Bus::batch($jobs)->dispatch();

        $this->assertDatabaseCount('users', 8);
        $this->assertDatabaseCount('credit_cards', 8);
    }

    /** @test */
    public function it_should_be_able_to_import_users_from_json_file_to_database()
    {
        $filePath  = base_path('tests/Resources/users.json');
        $users     = FileToArrayConverter::make($filePath);
        $jobs      = [];

        foreach ($users as $user) {
            $jobs[] = new UserImport($user);
        }

        Bus::batch($jobs)->dispatch();

        $this->assertDatabaseCount('users', 8);
        $this->assertDatabaseCount('credit_cards', 8);
    }

    /** @test */
    public function it_should_be_able_to_import_without_duplicating_data()
    {
        $filePath  = base_path('tests/Resources/users-duplicated.json');
        $users     = FileToArrayConverter::make($filePath);
        $jobs      = [];

        foreach ($users as $user) {
            $jobs[] = new UserImport($user);
        }

        Bus::batch($jobs)->dispatch();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('credit_cards', 1);
    }

    /** @test */
    public function it_should_be_able_to_import_only_users_within_the_permitted_age_or_unknown()
    {
        $filePath  = base_path('tests/Resources/users-age-check.json');
        $users     = FileToArrayConverter::make($filePath);
        $jobs      = [];

        foreach ($users as $user) {
            $jobs[] = new UserImport($user);
        }

        Bus::batch($jobs)->dispatch();

        $this->assertDatabaseHas('users', ['email' => 'unknown@email.com']);
        $this->assertDatabaseHas('users', ['email' => 'in@email.com']);

        $this->assertDatabaseMissing('users', ['email' => 'oldg@email.com']);
        $this->assertDatabaseMissing('users', ['email' => 'young@email.com']);
    }
}
