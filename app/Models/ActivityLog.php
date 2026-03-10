<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'subject_type',
        'subject_id', 'properties', 'ip_address'
    ];

    protected $casts = [
        'properties' => 'array', // auto-convert JSON to PHP array
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    // User → kung sino ang gumawa ng action
    // Usage: $log->user
    //        $log->user->name
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =============================
    // HELPER METHODS
    // =============================

    // I-record ang activity — static method para madaling gamitin
    // Usage: ActivityLog::record('placed_order', 'Order', $order->id, ['total' => 500])
    public static function record(
        string $action,
        string $subjectType = null,
        int $subjectId = null,
        array $properties = []
    ): void {
        static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'properties'   => $properties,
            'ip_address'   => request()->ip(),
        ]);
    }
}