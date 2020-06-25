<?php

namespace App\Http\Controllers;

use App\Card;
use App\BoardList;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $boardId, $listId)
    {
        $task = $request->task;

        $validate = Validator::make($request->all(), [
            'task' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid input'], 422);
        }

        $order = Card::where('list_id', $listId)->max('order') + 1;

        $listCheckCard = BoardList::find($listId)->card;
        if($listCheckCard->isEmpty()) {
            $order = 1;
        }

        Card::create([
            'list_id' => $listId,
            'order' => $order,
            'task' => $task
        ]);

        return response()->json(['message' => 'create card success'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $boardId, $listId, $cardId)
    {
        $task = $request->task;

        $validate = Validator::make($request->all(), [
            'task' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 200);
        }

        $card = Card::where([
            'id' => $cardId,
            'list_id' => $listId
        ]);

        $card->update([
            'task' => $task
        ]);

        return response()->json(['message' => 'update card success'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy($boardId, $listId, $cardId)
    {
        $card = Card::where([
            'id' => $cardId,
            'list_id' => $listId
        ]);

        $card->delete();

        return response()->json(['message' => 'delete card success'], 200);

    }

    public function up($cardId) {
        $card = Card::find($cardId);
        $order = $card->order;
        $listId = $card->list_id;

        $cardDown = Card::where('order','<',$order)
                        ->where('list_id', $listId)
                        ->orderBy('order','desc')
                        ->first();
        if(!$cardDown) return response()->json(['message' => 'move offset'], 422);

        $cardDownOrder = $cardDown->order;

        $cardDown->update(['order' => $order]);
        $card->update(['order' => $cardDownOrder]);

        return response()->json(['message' => 'move success'], 200);
    }

    public function down($cardId) {
        $card = Card::find($cardId);
        $order = $card->order;
        $listId = $card->list_id;

        $cardUp = Card::where('order', '>', $order)
                        ->where('list_id', $listId)
                        ->orderBy('order', 'asc')
                        ->first();
        if(!$cardUp) return response()->json(['message' => 'move offset'], 422);

        $upCardOrder = $cardUp->order;
        $cardUp->update(['order' => $order]);

        $card->update(['order' => $upCardOrder]);

        return response()->json(['message' => 'move success'], 200);
    }

    public function move($cardId, $listId) {
        $card = Card::find($cardId);

        $cardBoardId = $card->list()->first()->board_id;

        $listBoardId = BoardList::find($listId)->board_id;

        if($cardBoardId != $listBoardId) {
            return response()->json(['message' => 'move list invalid'], 200);
        }

        $listCheck = BoardList::find($listId)->card;
        $order = $listCheck->where('list_id', $listId)->max('order') + 1;

        if($listCheck->isEmpty()) {
            $order = 1;
        }

        $card->update([
            'list_id' => $listId,
            'order' => $order
        ]);

        return response()->json(['message' => 'move success'], 200);
    }

}