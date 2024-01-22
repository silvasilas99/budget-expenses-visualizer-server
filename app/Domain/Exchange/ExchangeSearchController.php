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
            $formattedData = $this->formatDataToResponse($data, $req);
            return response()->json(
                $formattedData
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
                if (in_array($paramValue, ExchangeService::RESERVED_NAMES))
                    return;
                return [
                    "value" => $paramValue,
                    "key" => $paramKey,
                ];
            }
        )->filter()->values()->toArray();
    }

    private function formatDataToResponse (array $data, Request $req)
    {
        if ($req->input("groupBy", "")) {
            $data = collect($data)
                ->groupBy($req->input("groupBy", ""));
            return $data;
        }
        return $data;
    }
}
