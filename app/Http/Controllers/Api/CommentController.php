<?php

namespace App\Http\Controllers\Api;

use App\Events\CommentPosted;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, Task $task)
    {
        return response()->json(
            $task->comments()->with('user:id,name')->latest()->get()
        );
    }

    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);

        $comment->load('user:id,name');

        broadcast(new CommentPosted($comment));

        return response()->json($comment, 201);
    }
}
