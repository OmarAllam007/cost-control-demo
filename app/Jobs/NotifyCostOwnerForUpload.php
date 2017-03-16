<?php

namespace App\Jobs;

use App\ActualBatch;
use App\User;
use Illuminate\Mail\Message;

class NotifyCostOwnerForUpload
{
    /** @var ActualBatch */
    protected $actualBatch;

    /** @var  User */
    protected $owner;

    /** @var  User */
    protected $user;

    public function __construct(ActualBatch $actualBatch)
    {
        $this->actualBatch = $actualBatch;
        $this->owner = $this->actualBatch->project->cost_owner;
        $this->user = $this->actualBatch->user;
    }

    public function handle()
    {
        $data = ['batch' => $this->actualBatch, 'owner' => $this->owner, 'user' => $this->user];

        \Mail::send('mail.cost-upload', $data, function(Message $msg) {
            $msg->to($this->owner->email);
            $msg->subject('[KPS] Data upload notification');
            $ext = \File::extension($this->actualBatch->file);
            $msg->attach($this->actualBatch->file, [
                'as' => slug($this->actualBatch->project->name) . '-data-upload-' . date('YmdHi') . '.' . $ext
            ]);
        });
    }
}
