<?php

namespace Modules\Auth\Data;

use Carbon\Carbon;
use Modules\Auth\Enums\KeyTypeEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class CreateKeyData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly KeyTypeEnum $type,
        #[WithCast(DateTimeInterfaceCast::class, timeZone: 'UTC')]
        public Carbon $expires_at
    ) {
    }
}