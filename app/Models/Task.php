<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected $appends = ['attachment_url'];
    public const ATTACHMENT = 'uploads/tasks/';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    protected function getAttachmentUrlAttribute(){
        if($this->attachment){
            return asset(self::ATTACHMENT.$this->attachment);
        }else{
            return "";
        }
    }

    protected static function booted()
    {
        static::saved(fn () => self::clearTaskCache());
        static::deleted(fn () => self::clearTaskCache());
        static::restored(fn () => self::clearTaskCache());
    }

    private static function clearTaskCache()
    {
        $keys = Cache::get('task_cache_keys',[]);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('task_cache_keys');
        Cache::forget('dashboard_analytics');
    }
}
