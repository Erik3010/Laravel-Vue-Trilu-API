<?php

namespace App\Http\Controllers;

use App\Board;
use App\Token;
use App\BoardMember;
use App\User;
use App\Card;
use App\BoardList;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $boards = Board::all();
        $data = [];

        foreach($boards as $board) {
            $data[] = [
                'id' => $board->id,
                'name' => $board->name,
                'creator_id' => $board->creator_id
            ];
        }

        return response()->json($data, 200);
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
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid input'], 422);
        }

        $token = $request->token;
        if($token) {
            $isToken = Token::where('token', $token)->first();
            if(!$isToken) {
                return response()->json(['message' => 'unauthorized user'], 401);
            }
            $userId = $isToken->user_id;

            $board = Board::create([
                'creator_id' => $userId,
                'name' => $request->name
            ]);

            BoardMember::create([
                'board_id' => $board->id,
                'user_id' => $userId
            ]);

            return response()->json(['message' => 'create board success'], 200);
        }

        return response()->json(['message' => 'unauthorized user'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Board $board)
    {
        $data = [];

        $members = $board->member()->get();

        $listCard = BoardList::where('board_id', $board->id)->orderBy('order', 'asc')->get();

        $listCardData = [];
        foreach($listCard as $list) {

            $listCardData[] = [
                'id' => $list->id,
                'name' => $list->name,
                'order' => $list->order,
                'cards' => Card::where('list_id', $list->id)->orderBy('order', 'asc')->get([
                    'id as card_id',
                    'task',
                    'order'
                ])
            ];
        }

        $memberData = [];
        foreach($members as $member) {
            $firstName = User::find($member->user_id)->first_name;
            $lastName = User::find($member->user_id)->last_name;
            $initial = $firstName[0] . $lastName[0];

            $memberData[] = [
                'id' => $member->user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'initial' => $initial
            ];
        }

        $data[] = [
            'id' => $board->id,
            'name' => $board->name,
            'creator_id' => $board->creator_id,
            'members' => $memberData,
            'lists' => $listCardData
        ];

        return response()->json($data[0], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $board->update([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'update board success']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $boardId = $id;

        BoardMember::where('board_id', $boardId)->delete();

        $boardList = BoardList::where('board_id', $boardId)->get();

        foreach($boardList as $board) {
            $listId = $board->id;
            Card::where('list_id', $listId)->delete();
        }
        BoardList::where('board_id', $boardId)->delete();
        Board::find($boardId)->delete();

        return response()->json(['message' => 'delete board success']);
    }

    public function addMember(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            'username' => 'required|exists:users'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid filed'], 200);
        }

        $userId = User::where('username', $request->username)->first()->id;

        BoardMember::create([
            'board_id' => $id,
            'user_id' => $userId
        ]);

        return response()->json(['message' => 'add member success']);
    }

    public function removeMember(Request $request, $boardId, $userId) {
        $board = BoardMember::where(['board_id' => $boardId, 'user_id' => $userId]);
        $board->delete();

        return response()->json(['message' => 'remove member success'], 200);
    }

}
