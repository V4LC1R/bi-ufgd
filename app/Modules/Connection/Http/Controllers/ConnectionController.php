<?php

namespace App\Modules\Connection\Http\Controllers;

use App\Modules\Connection\Contracts\QueryExecutor;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Http\Requests\ConnectionRequest;
use App\Modules\Connection\Services\ConnectionService;
use App\Modules\Connection\Services\DimensionDataService;
use App\Modules\Querry\Models\Querry;
use Illuminate\Routing\Controller;

class ConnectionController extends Controller
{
    public function __construct(
        protected ConnectionService $service,
        protected QueryExecutor $excutor,
        protected DimensionDataService $dim,
        protected StructTable $struct_table
    ) {
    }

    public function index()
    {
        try {

            $conns = $this->service->getList();

            return response()->json($conns);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message" => "Err to save connection!",
                    "reason" => $th->getMessage()
                ], 500);
        }
    }

    public function store(ConnectionRequest $request)
    {
        try {
            $dto = new ConnectionDTO($request->all());

            $conn = $this->service->create($dto);

            return response()->json([
                "message" => "Connection Saved!",
                'id' => $conn->id
            ], 201);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message" => "Err to save connection!",
                    "reason" => $th->getMessage()
                ], 500);
        }
    }

    public function struct($connection_name)
    {
        try {
            $tables = $this
                ->struct_table
                ->setConnectionName($connection_name)
                ->getTablesNames();

            return response()->json($tables);
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
}
