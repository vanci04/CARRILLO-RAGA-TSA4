<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'status', 'task_date', 'is_archived'];

    protected function casts(): array
    {
        return ['task_date' => 'date', 'is_archived' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeForToday(Builder $query): Builder
    {
        return $query->whereDate('task_date', today()->toDateString());
    }

    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('task_date')->orderBy('id');
    }
}
