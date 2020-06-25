<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $guarded = [];

    protected $with = ['member'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function list() {
        return $this->hasMany(BoardList::class);
    }

    public function member() {
        return $this->hasMany(BoardMember::class);
    }
}
