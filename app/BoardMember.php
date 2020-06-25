<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    protected $guarded = [];

    public function board() {
        return $this->belongsTo(Board::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
