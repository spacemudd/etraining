<?php

namespace App\Classes;

use App\Models\Back\Audit;
use App\Models\User;
use App\Services\GosiService;

class GosiEmployee
{
    private string $ninOrIqama;

    private array $fetchedData = [];

    private array $error = [];

    public function __construct(string $ninOrIqama)
    {
        $this->ninOrIqama = $ninOrIqama;
    }

    public static function new(string $ninOrIqama)
    {
        return new GosiEmployee($ninOrIqama);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function get()
    {
        Audit::create([
            'event' => 'gosi.query',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->user()->id,
            'user_type' => User::class,
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'url' => url()->current(),
            'new_values' => [
                'ninOrIqama' => $this->ninOrIqama,
            ],
        ]);

        $data = GosiService::getEmployeeData($this);

        if (array_key_exists('errorCode', $data)) {
            $this->error = $data;
        }

        $this->fetchedData = $data;

        return $this;
    }

    public function getNinOrIqama()
    {
        return $this->ninOrIqama;
    }

    public function toArray()
    {
        if ($this->error) {
            return collect($this->error);
        }

        return $this->fetchedData;
    }
}
