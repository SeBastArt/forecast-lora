<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Services\AlertService;
use App\Services\FieldService;
use App\Services\NodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NodeDataApiController extends Controller
{
    const TIMEFORMAT = 'Y.m.d_H:i:s';
    private $nodeService;

    public function __construct(NodeService $nodeService, FieldService $fieldService, AlertService $alertService)
    {
        //$this->middleware('auth:sanctum');
        $this->nodeService = $nodeService;
    }

    /**
     * @param Node $node
     * @param Request $request
     * @return StreamedResponse
     */
    public function csvdata(Node $node, Request $request): StreamedResponse
    {
        //without Request the last 24h
        $start = Carbon::now()->subHours(24);
        $end = Carbon::now();

        if (isset($request['startDate'])) {
            $start = Carbon::parse($request['startDate']);
        }

        if (isset($request['endDate'])) {
            $end = Carbon::parse($request['endDate']);
        }
        $columns = $this->nodeService->getCSVColumnsName($node);
        $csvExport =  $this->nodeService->getCSVArray($node, $start, $end);

        $callback = function() use ($csvExport, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($csvExport as $data) {
                $tempArray = array();
                foreach ($columns as $column) {
                    array_push($tempArray, $data[$column]);
                }
                fputcsv($file, $tempArray, ";");
            }
            fclose($file);
        };

        //Build Header and Filename
        $companyName = $node->facility->company->name;
        $facilityName = $node->facility->name;
        $nodeName = $node->name;
        $file_name = $companyName . '_' . $facilityName . '_' . $nodeName . '_' . Carbon::parse($start)->format('Y.m.d_H:i:s') . '-' . Carbon::parse($end)->format('Y.m.d_H:i:s') . '.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $file_name,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Node $node
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Node $node, Request $request): JsonResponse
    {
        //without Request the last 24h
        $start = Carbon::now()->subHours(24);
        $end = Carbon::now();

        if (isset($request['startDate'])) {
            $start = Carbon::parse($request['startDate']);
        }

        if (isset($request['endDate'])) {
            $end = Carbon::parse($request['endDate']);
        }

        $nodeData = $this->nodeService->getData($node, $start, $end);
        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Node $node
     * @return JsonResponse
     */
    public function meta(Node $node): JsonResponse
    {
        $nodeMeta = $this->nodeService->getMeta($node);
        return response()->json($nodeMeta, 200, [], JSON_PRETTY_PRINT);
    }
}
