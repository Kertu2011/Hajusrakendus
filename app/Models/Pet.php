<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string $description
 * @property string $species
 * @property string $gender
 * @property Carbon|null $date_of_birth
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $user_id
 * @property string $approximate_age See {@see self::approximateAge()}
 */
class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'description',
        'species',
        'gender',
        'approximate_age',
    ];

    protected $hidden = [
        'date_of_birth',
    ];

    protected $appends = [
        'approximate_age',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    private function approximateTimeSince(Carbon $date): string
    {
        return Carbon::now()->diffInMonths($date, true) < 11.5
            ? round(Carbon::now()->diffInMonths($date, true)) . ' months'
            : round(Carbon::now()->diffInYears($date, true)) . ' years';
    }

    private function timeFromApproximateAge(?string $approximateAge): ?Carbon
    {
        if (!$approximateAge) {
            return null;
        }

        $parts = explode(' ', $approximateAge);
        $value = (int)$parts[0];
        $unit = strtolower($parts[1]);

        return match ($unit) {
            'months' => Carbon::now()->subMonths($value),
            'years' => Carbon::now()->subYears($value),
            default => null,
        };
    }

    protected function approximateAge(): Attribute {
        return Attribute::make(
            get: fn () => $this->date_of_birth
                ? $this->approximateTimeSince($this->date_of_birth)
                : null,
            set: fn ($value) => [
                'date_of_birth' => $this->timeFromApproximateAge($value),
            ],
        );
    }

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
