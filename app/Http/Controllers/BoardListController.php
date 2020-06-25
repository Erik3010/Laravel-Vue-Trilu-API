<?php

namespace App\Http\Controllers;

use App\BoardList;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class BoardListController extends Controller
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
    public function store(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $order = BoardList::max('order') + 1;

        BoardList::create([
            'board_id' => $id,
            'order' => $order,
            'name' => $request->name
        ]);

        return response()->json(['message' => 'create list success'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BoardList  $boardList
     * @return \Illuminate\Http\Response
     */
    public function show(BoardList $boardList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BoardList  $boardList
     * @return \Illuminate\Http\Response
     */
    public function edit(BoardList $boardList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BoardList  $boardList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $boardId, $listId)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $list = BoardList::where([
            'board_id' => $boardId,
            'id' => $listId
        ]);

        $list->update([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'update list success'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BoardList  $boardList
     * @return \Illuminate\Http\Response
     */
    public function destroy($boardId, $listId)
    {
        $list = BoardList::where([
            'id' => $listId,
            'board_id' => $boardId
        ]);

        $listGet = $list->first();
        $listGetCard = $listGet->card()->delete();

        $list->delete();

        return response()->json(['message' => 'delete list success'], 200);
    }

    public function right($boardId, $listId) {
        $list = BoardList::where([
            'id' => $listId,
            'board_id' => $boardId
        ])->first();

        $nextOrder = $list->order + 1;
        $reverseOrder = $nextOrder - 1;

        $maxOrder = BoardList::max('order');


        if($nextOrder <= $maxOrder) {
            $checkOrder = BoardList::where('order', $nextOrder)->first();

            if(!$checkOrder) {
                $listCheck = BoardList::where('order', '>', $list->order)->orderBy('order', 'asc')->first();
                $nextOrder = $listCheck->order;
                $reverseOrder = $nextOrder - ($nextOrder - $list->order);
            }

            $nextList = BoardList::where('order', $nextOrder)->first();
            $nextList->update([
                'order' => $reverseOrder
            ]);

            $list->update([
                'order' => $nextOrder
            ]);

            return response()->json(['message' => 'move success'], 200);
        }

        return response()->json(['message' => 'move offset'], 422);
    }

    public function left($boardId, $listId) {
        $list = BoardList::where([
            'id' => $listId,
            'board_id' => $boardId
        ])->first();

        $beforeOrder = $list->order - 1;
        $reverseOrder = $beforeOrder + 1;

        $minOrder = BoardList::min('order');

        if($beforeOrder >= $minOrder) {
            $checkOrder = BoardList::where('order', $beforeOrder)->first();

            if(!$checkOrder) {
                $listCheck = BoardList::where('order', '<', $beforeOrder)->orderBy('order', 'desc')->first();
                $beforeOrder = $listCheck->order;
                $reverseOrder = $beforeOrder - ($beforeOrder - $list->order);
            }

            $beforeList = BoardList::where('order', $beforeOrder)->first();
            $beforeList->update([
                'order' => $reverseOrder
            ]);

            $list->update([
                'order' => $beforeOrder
            ]);

            return response()->json(['message' => 'move success'], 200);
        }

        return response()->json(['message' => 'move offset'], 422);

    }

}
