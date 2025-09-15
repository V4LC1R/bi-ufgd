<?php
namespace App\Modules\Querry\Http\Controllers;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Http\Requests\QuerryRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Routing\Controller;
use QuerryService;

class QuerryController extends Controller
{

    public function store(QuerryRequest $request,QuerryService $service)
    {
        try {
            $dto  = new PreSqlDTO($request->all());
            $service->savePreSql($dto);
            return response()->json(["message"=>"Querry was saved, await your execution!"]);
        } catch (\Throwable $th) {
            return response()->json([
                "message"=>"Querry not was saved!",
                "reason"=>$th->getMessage()
            ],500);
        }
    }
}
