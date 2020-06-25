<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $guarded = [];

    public function list() {
        return $this->belongsTo(BoardList::class);
    }
}
