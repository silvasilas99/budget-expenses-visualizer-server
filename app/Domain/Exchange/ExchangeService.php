<?php

namespace App\Domain\Exchange;
use Illuminate\Support\Facades\Http;

class ExchangeService {
    public const RESERVED_NAMES = ["groupBy", "orderBy", "only"];
    private const RESOURCE_ID = "d4d8a7f0-d4be-4397-b950-f0c991438111";

    public function getDataWithFilters (
        array $params = []
    ) : array {
        $data = $this->getDataFromExternalApi();
        return collect($params)->reduce(
            function ($carry, $param) use ($data) {
                !$carry &&
                    $carry = collect($data);
                if (
                    !$param
                ||  !data_get($param, "value")
                ||  empty($param)
                ||  empty(data_get($param, "value", []))
                ||  in_array(data_get($param, "key"), self::RESERVED_NAMES)
                ) {
                    return $carry;
                }

                return $carry->where(
                    data_get($param, "key", ""),
                    data_get($param, "value")
                );
            }
        )->filter()->values()->toArray();
    }

    private function getDataFromExternalApi () : array
    {
        $response =
            Http::get("http://dados.recife.pe.gov.br/api/3/action/datastore_search?resource_id=" . self::RESOURCE_ID);
        $decodedResponse = json_decode($response->getBody()->getContents());
        return
            data_get($decodedResponse, "result.records", []);
    }
}
