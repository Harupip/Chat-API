<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use App\Models\Participant;
use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($room_id, $user_id)
    {
        $messages = DB::table('messages')
                        ->select('id','message', 'user_id as author', 'created_at as time')
                        ->where('room_id',$room_id)
                        ->where('user_id',$user_id)
                        ->get();
        return response()->json(['messages' => $messages]);
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
    public function store(Request $request, $room_id, $user_id)
    {
        try {
            request()->validate([
                'message' => 'required',
            ]);
            Message::create([
                'user_id' => $user_id,
                'room_id' => $room_id,
                'message' => $request->message,   
           ]);  
            return response()->json([
                'status' => true,
                'message' => 'Message sent'
            ], 200);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User OR Room Not Found'
            ], 500);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($room_id)
    {
        $messages = DB::table('messages')
                        ->select('id','message', 'user_id as author', 'created_at as time')
                        ->where('room_id',$room_id)
                        ->get();
        return response()->json(['messages' => $messages]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
