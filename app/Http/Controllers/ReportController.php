<?php

namespace App\Http\Controllers;

use App\Actions\Report\CreateReportAction;
use App\Actions\Report\DeleteReportAction;
use App\Http\Requests\Report\CreateReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function createReport(CreateReportRequest $request): JsonResponse
    {
        $reportedPet = app(CreateReportAction::class)->run($request);

        return response()->json($reportedPet);
    }

    public function deleteReport(Request $request, Report $report): Response
    {
        app(DeleteReportAction::class)->run($report);

        return response()->noContent();
    }
}
