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
            $aggroupedData = $this->aggroupData($data, $req->input("groupBy", ""));
            return response()->json(
                $aggroupedData
            );
        } catch (\Throwable $th) {
            return response()->json(
                ['error' => $th->getMessage()]
            );
        }
    }

    public function getDataByAggroupment(string $counter, string $grouper, Request $req) {
        try {
            $data = $this->exchangeService->getDataWithFilters();
            $formattedData =
                collect($this->aggroupData($data, $grouper))->map(
                    function ($value, $key) use ($counter, $grouper) {
                        return [
                            $grouper => $key,
                            $counter =>
                                collect($value)->map(
                                    fn ($item) =>
                                        floatval(data_get($item, $counter, 0))
                                )->sum()
                        ];
                    }
                )->sortBy($grouper)->values();
            return response()->json(
                [ "data" => $formattedData ]
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

    private function aggroupData (array $data, string $grouper)
    {
        if ($grouper) {
            $data = collect($data)
                ->groupBy($grouper);
            return $data;
        }
        return $data;
    }
}
