<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Comment $comment) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('task.' . $this->comment->task_id),
            new Channel('comments'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->comment->id,
            'body'       => $this->comment->body,
            'task_id'    => $this->comment->task_id,
            'task_name'  => $this->comment->task->name,
            'created_at' => $this->comment->created_at,
            'user'       => [
                'id'   => $this->comment->user->id,
                'name' => $this->comment->user->name,
            ],
        ];
    }
}
