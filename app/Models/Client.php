<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($client) {
            $client->app_key = $client->generateAppKey();
            $client->app_token = $client->generateAppToken();
            $client->account = preg_replace('/\s+/', '', $client->account);
            $client->payment_system = preg_replace('/\s+/', '', $client->payment_system);
        });

        static::updating(function ($client) {
            $client->account = preg_replace('/\s+/', '', $client->account);
            $client->payment_system = preg_replace('/\s+/', '', $client->payment_system);
        });
    }

    /**
     * @return HasOne
     */
    public function credential(): HasOne
    {
        return $this->hasOne(Credential::class);
    }

    /**
     * @return string
     */
    public function generateAppKey(): string
    {
        $length = 11;
        $result = '-';

        while (strlen($result) < $length) {
            $randomString = strtoupper(Str::random(1));
            if (preg_match('/[A-Z0-9]/', $randomString)) {
                $result .= $randomString;
            }
        }

        return Str::upper($this->account) . $result;
    }

    /**
     * @return UuidInterface
     */
    public function generateAppToken(): UuidInterface
    {
        return Str::uuid();
    }

    /**
     * @param mixed $account
     * @param mixed $appKey
     * @param mixed $appToken
     * @return null|self
     */
    public function getClientByCredentials($account, $appKey, $appToken): null|self
    {
        if (empty($account) && empty($appKey) && empty($appToken)) {
            return null;
        }

        return self::query()
            ->when($account, function ($query) use ($account) {
                $query->where('account', $account);
            })
            ->when($appKey, function ($query) use ($appKey) {
                $query->where('app_key', $appKey);
            })
            ->when($appToken, function ($query) use ($appToken) {
               $query->where('app_token', $appToken);
            })
            ->first();
    }
}
