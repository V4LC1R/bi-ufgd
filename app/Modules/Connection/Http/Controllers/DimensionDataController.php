<?php

namespace App\Modules\Connection\Http\Controllers;

use App\Modules\Connection\Http\DTOs\DimensionFilterDTO;
use App\Modules\Connection\Http\Requests\DimensionFilterRequest;
use App\Modules\Connection\Models\Tables;
use App\Modules\Connection\Services\DimensionDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DimensionDataController extends Controller
{
    public function __construct(
        protected DimensionDataService $dimensionDataService
    ) {
    }

    public function search(int $conn_id, DimensionFilterRequest $request): JsonResponse
    {
        // O Spatie já validou e criou o DTO. Pegamos ele com o método dto().
        $filterDto = $request->toDTO();

        $table = Tables::where('name', $filterDto->table)
            ->where('connection_id', $conn_id)
            ->first();

        $paginatedData = $this->dimensionDataService->getFilteredPaginatedData($table, $filterDto);

        return response()->json($paginatedData);
    }
}