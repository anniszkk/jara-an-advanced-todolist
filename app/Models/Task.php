<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['list_id', 'title', 'description', 'priority', 'status', 'due_date', 'assignee_id'];

    public function list()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}