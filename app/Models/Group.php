<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
            "code",
            "name"
    ];


    public function group_users() : HasMany {

        return $this->hasMany(GroupUser::class);

    }

}
