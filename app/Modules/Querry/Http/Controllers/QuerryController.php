<?php
namespace App\Modules\Querry\Http\Controllers;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Http\Requests\QuerryRequest;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Services\BuildQuerryService;
use App\Modules\Querry\Services\QuerryService;
use Illuminate\Routing\Controller;
class QuerryController extends Controller
{

    public function __construct(
        protected QuerryService $service,
        protected BuildQuerryService $build
    ) {
    }

    public function store(QuerryRequest $request, )
    {
        try {
            $dto = new PreSqlDTO($request->all());
            $this->service->savePreSql($dto);
            return response()->json(["message" => "Querry was saved, await your execution!"]);
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

    public function result(QuerryRequest $request, $query_id)
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

    public function teste($id)
    {
        try {
            $pre_sql = Querry::find($id);

            $this->build->makeQuerry($pre_sql, false);

            return response()->json(["message" => "Querry is ok, await your execution!"]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Querry not was saved!",
                "reason" => $th->getMessage()
            ], 500);
        }
    }
}