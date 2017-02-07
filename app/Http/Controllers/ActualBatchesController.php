<?php

namespace App\Http\Controllers;

use App\ActualBatch;
use Illuminate\Http\Request;

use App\Http\Requests;

class ActualBatchesController extends Controller
{
    function show(ActualBatch $actual_batch)
    {
        return view('actual-batches.show', ['batch' => $actual_batch]);
    }

    function download(ActualBatch $actual_batch)
    {
        return \Response::download($actual_batch->file);
    }
}
