<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoardList extends Model
{
    protected $guarded = [];

    protected $with = ['card'];

    public function Board() {
        return $this->belongsTo(Board::class);
    }

    public function card() {
        return $this->hasMany(Card::class, 'list_id');
    }

}
