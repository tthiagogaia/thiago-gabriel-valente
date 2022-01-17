<?php

namespace App\Jobs;

use App\Helpers\DateConverter;
use App\Models\CreditCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UserImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected const BEGIN_YEAR = 18;

    protected const END_YEAR = 65;

    public $tries = 5;

    public $backoff = 5;

    public function __construct(public array $user)
    {
    }

    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $dateOfBirth = $this->birthDateCheck($this->user['date_of_birth']);

        if ($dateOfBirth === false) {
            return;
        }

        DB::transaction(function () use ($dateOfBirth) {
            $user = User::query()->updateOrCreate([
                'email' => $this->user['email'],
            ], [
                'name'          => $this->user['name'],
                'address'       => $this->user['address'],
                'checked'       => $this->user['checked'],
                'description'   => $this->user['description'],
                'interest'      => $this->user['interest'],
                'date_of_birth' => $dateOfBirth instanceof Carbon ? $dateOfBirth->toAtomString() : null,
                'account'       => $this->user['account'],
            ]);

            CreditCard::query()->updateOrCreate([
                'user_id' => $user->id,
                'number'  => $this->user['credit_card']['number'],
            ], [
                'type'            => $this->user['credit_card']['type'],
                'name'            => $this->user['credit_card']['name'],
                'expiration_date' => $this->user['credit_card']['expirationDate'],
            ]);
        });
    }

    protected function birthDateCheck(string | null $birthDate): Carbon | bool
    {
        if ($birthDate === null) {
            return true;
        }

        $dateOfBirth = DateConverter::make($birthDate);

        if ($dateOfBirth->diffInYears(now()) < self::BEGIN_YEAR
            || $dateOfBirth->diffInYears(now()) > self::END_YEAR
        ) {
            return false;
        }

        return $dateOfBirth;
    }
}
