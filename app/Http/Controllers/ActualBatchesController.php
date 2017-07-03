<?php

namespace App\Http\Controllers;

use App\ActualBatch;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Http\Response;

class ActualBatchesController extends Controller
{
    function show(ActualBatch $actual_batch)
    {
        return view('actual-batches.show', ['batch' => $actual_batch]);
    }

    function download(ActualBatch $actual_batch)
    {
        return \Response::download($actual_batch->file)->deleteFileAfterSend(true);
    }

    function excel(ActualBatch $actual_batch)
    {
        $content = view('actual-batches.excel', compact('actual_batch'));
        $response = new Response($content);

        $filename = slug($actual_batch->project->name) . '_data-upload_' . $actual_batch->created_at->format('Y-m-d') . '.xlsx';
        $response->header('Content-Type', 'octet/stream');
        $response->header('Content-Disposition', 'attachment; filename=' . $filename);

        return $response;
    }
}
