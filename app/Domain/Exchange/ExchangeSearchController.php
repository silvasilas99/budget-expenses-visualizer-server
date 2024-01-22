<?php

namespace App\Domain\Exchange;

use App\Domain\Exchange\ExchangeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExchangeSearchController extends Controller
{
    public function __construct(
        private ?ExchangeService $exchangeService = null
    ) {}

    public function index(Request $req)
    {
        try {
            $params = $this->getQueryStringFormatedAsParameters($req);
            $data = $this->exchangeService->getDataWithFilters($params);
            return response()->json(
                ['data' => $data]
            );
        } catch (\Throwable $th) {
            return response()->json(
                ['error' => $th->getMessage()]
            );
        }
    }

    private function getQueryStringFormatedAsParameters (Request $req) {
        return collect($req->all())->map(
            function ($paramValue, $paramKey) {
                return [
                    "value" => $paramValue,
                    "key" => $paramKey,
                ];
            }
        )->filter()->values()->toArray();
    }
}
