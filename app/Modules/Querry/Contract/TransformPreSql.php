<?php
namespace App\Modules\Querry\Contract;

use App\Modules\Querry\Http\DTOs\DimensionDTO;
use Illuminate\Database\Query\Builder;

interface TransformPreSql
{
    public function build(...$args):Builder;

    public static function fill(...$args):Builder;
}