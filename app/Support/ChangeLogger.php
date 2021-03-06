<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 3/27/17
 * Time: 10:49 AM
 */

namespace App\Support;


use App\ChangeLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class ChangeLogger
{
    /** @var ChangeLog */
    protected $changeRequest;

    protected $request;

    function __construct()
    {
        $this->request = request();
        if (!$this->request->isMethodSafe(false) && $this->request->user()) {
            $this->initiateRequest();
        }
    }

    function initiateRequest()
    {
        if (!$this->changeRequest) {

            $this->changeRequest = new ChangeLog([
                'url' => $this->request->url(),
                'method' => $this->request->method(),
                'user_id' => $this->request->user()->id ?? '0'
            ]);

            if ($this->request->files->count()) {
                $this->changeRequest->files = $this->handleFiles();
            } else {
                $this->changeRequest->files = [];
            }

            $this->changeRequest->save();
        }
    }

    function record(Model $model, $delete = false)
    {
        $this->initiateRequest();

        $original = [];
        if ($delete) {
            $original = $model->getAttributes();
            $original['descriptor'] = $model->descriptor;
            $updated = [];
        } else if ($model->wasRecentlyCreated) {
            $updated = $model->getAttributes();
            $original['descriptor'] = $model->descriptor;
        } else {
            $updated = $model->getDirty();
            foreach ($updated as $field => $value) {
                $original[$field] = $model->getOriginal($field, '');
            }
            $original['descriptor'] = $model->descriptor;
        }

        $this->changeRequest->changes()->create([
            'model' => get_class($model),
            'original' => $original,
            'updated' => $updated,
            'model_id' => $model->id,
        ]);
    }

    protected function handleFiles()
    {
        $files = [];

        foreach ($this->request->files as $file) {
            /** @var UploadedFile $file */
            if (!$file->getError()) {
                $originalName = $file->getClientOriginalName();
                \File::copy($file->getRealPath(), storage_path('uploads/' . uniqid() . '_' . $originalName));
                $files[] = $originalName;
            }
       }

        return $files;
    }
}