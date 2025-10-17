<?php

namespace App\Modules\Connection\Http\Controllers;

use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Http\Requests\ConnectionRequest;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Services\ConnectionService;
use App\Modules\Connection\Services\DimensionDataService;
use App\Modules\Connection\Services\ExecuteSqlService;
use App\Modules\Querry\Models\Querry;
use Illuminate\Routing\Controller;

class ConnectionController extends Controller
{
    public function __construct(
        protected ConnectionService $service,
        protected ExecuteSqlService $excutor,
        protected DimensionDataService $dim
    ) {
    }
    public function store(ConnectionRequest $request)
    {
        try {
            $dto = new ConnectionDTO($request->all());

            $this->service->create($dto);

            return response()->json(["message" => "Connection Saved!"], 201);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message" => "Err to save connection!",
                    "reason" => $th->getMessage()
                ], 500);
        }
    }

    public function edit(ConnectionRequest $request, $conn_id)
    {
        try {
            $dto = new ConnectionDTO($request->all());

            $this->service->edit($dto, $conn_id);

            return response()->json(["message" => "Connection Saved!"]);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message" => "Err to save connection!",
                    "reason" => $th->getMessage()
                ], 500);
        }
    }

    public function exec($query_id)
    {
        try {
            $query = Querry::findOrFail($query_id);

            $conn = Connection::findOrFail($query->connection_id);

            $this->excutor->executeAndCache($conn, $query, true);

            return response()->json();
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message" => " Err to excute sql!",
                    "reason" => $th->getMessage()
                ], 500);
        }
    }
}
