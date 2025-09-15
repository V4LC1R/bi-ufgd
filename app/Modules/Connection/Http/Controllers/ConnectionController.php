<?php

namespace App\Modules\Connection\Http\Controllers;
use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Http\Requests\ConnectionRequest;
use App\Modules\Connection\Services\ConnectionService;
use Illuminate\Http\Client\Request;
use Illuminate\Routing\Controller;

class ConnectionController extends Controller
{
    public function store(ConnectionRequest $request,ConnectionService $service)
    {
        try {
            $dto = new ConnectionDTO($request->all());

            $service->create($dto);

            return response()->json(["message"=>"Connection Saved!"]);
        } catch (\Exception $th) {
            return response()
                ->json([
                    "message"=>" Err to save connection!",
                    "reason"=>$th->getMessage()
                ],500);
        }
    }
}
