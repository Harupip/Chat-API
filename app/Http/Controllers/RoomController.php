<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use App\Models\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::all();
        return response()->json(['rooms'=>$rooms]);
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $header = $request->header('Token');
            $userid = User::findOrFail(PersonalAccessToken::findOrFail($header)->tokenable_id);
            request()->validate([
                'name' => 'required',
            ]);
            $count = Room::selectRaw('count(*) as total')
                    ->where('name', $request->name)
                    ->first();
            if ($count->total >= 1) {
                return response()->json([
                    'status' => false,
                    'message' => "This room's name already exist.",
                ], 401);
            }
           Room::create([
                'admin' => $userid->id,
                'name' => $request->name,   
           ]);  
           Participant::create([
                'user_id' => $userid->id,
                'room_id' => Room::all()->last()->id,
           ]);
            return response()->json([
                'status' => true,
                'message' => 'Room created'
            ], 200);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 500);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $room = Room::select('id','admin','name')->where('id',$id)->get();
            $user_room = Participant::select('user_id')
                        ->where('room_id',$id)->get();
            $mess = DB::table('users')->join('messages','users.id','=','messages.user_id')
                        ->select('users.username as sender','messages.message')
                        ->where('messages.room_id',$id)->get();

            return response()->json([
                'room' => $room,    
                'users'=>$user_room, 
                'message'=>$mess,
            ],200);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 500);
        }       
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
        try {
            $room = Room::findOrFail($id);
            $room->delete();
            return response()->json([
                'status' => true,
                'message' => 'Room deleted'
            ], 200);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found'
            ], 500);
        }  
    }
}
