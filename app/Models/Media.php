<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        "url",
        "media_type",
        "name",
        "sent_by",
        "group",
        "sent_to"
    ];

    public function sent_by() : BelongsTo {

        return $this->belongsTo(User::class);

    }

    public function group() : BelongsTo {

        return $this->belongsTo(Group::class);

    }

    public function sent_to() : BelongsTo {

        return $this->belongsTo(User::class);

    }


}
