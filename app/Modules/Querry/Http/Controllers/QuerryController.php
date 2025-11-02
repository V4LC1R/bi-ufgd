<?php
namespace App\Modules\Querry\Http\Controllers;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Http\Requests\QuerryRequest;
use App\Modules\Querry\Services\BuildQuerryService;
use App\Modules\Querry\Services\QuerryService;
use App\Modules\Querry\Services\ResultQueryService;
use Illuminate\Routing\Controller;
class QuerryController extends Controller
{

    public function __construct(
        protected QuerryService $service,
        protected BuildQuerryService $build,
        protected ResultQueryService $result_service
    ) {
    }

    public function store(QuerryRequest $request)
    {
        try {
            $dto = new PreSqlDTO($request->all());
            $query = $this->service->savePreSql($dto);
            return response()->json(["message" => "Querry was saved, await your execution!", "hash" => $query->hash]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Querry not was saved!",
                "reason" => $th->getMessage()
            ], 500);
        }
    }

    public function edit(QuerryRequest $request, $query_id)
    {
        try {
            $dto = new PreSqlDTO($request->all());
            $this->service->savePreSql($dto, $query_id);
            return response()->json(["message" => "Querry was saved, await your execution!"]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Querry not was saved!",
                "reason" => $th->getMessage()
            ], 500);
        }
    }

    public function result($hash)
    {
        try {

            $response = $this
                ->result_service
                ->getResultByHash($hash);
            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Querry not was saved!",
                "reason" => $th->getMessage()
            ], 500);
        }
    }

    public function show($query_id)
    {
        try {

            $response = $this->service->getQuery($query_id);
            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Querry not was saved!",
                "reason" => $th->getMessage()
            ], 500);
        }
    }
}