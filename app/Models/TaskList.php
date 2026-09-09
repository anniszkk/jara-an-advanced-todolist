<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    protected $table = 'lists';

    protected $fillable = ['name', 'description', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}