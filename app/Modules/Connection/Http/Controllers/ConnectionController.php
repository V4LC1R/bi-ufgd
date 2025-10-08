<?php

namespace App\Modules\Connection\Http\Controllers;
use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Http\Requests\ConnectionRequest;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Services\ConnectionService;
use App\Modules\Connection\Services\ExecuteSqlService;
use App\Modules\Querry\Models\Querry;
use Illuminate\Routing\Controller;

class ConnectionController extends Controller
{

    public function __construct(
        protected ConnectionService $service,
        protected ExecuteSqlService $excutor
    ){}
    public function store(ConnectionRequest $request)
    {
        try {
            $dto = new ConnectionDTO($request->all());

            $this->service->create($dto);

            return response()->json(["message"=>"Connection Saved!"]);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message"=>" Err to save connection!",
                    "reason"=>$th->getMessage()
                ],500);
        }
    }

     public function exec($query_id,$connection_id)
    {
        try {
            $conn =  Connection::findOrFail($connection_id);

            $query = Querry::findOrFail($query_id);

            $result = $this->excutor->executeAndCache($conn,$query->hash,true);

            return response()->json($result);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message"=>" Err to excute sql!",
                    "reason"=>$th->getMessage()
                ],500);
        }
    }
}
