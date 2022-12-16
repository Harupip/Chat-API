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

class ParticipantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $participants = DB::table('users')
                            ->join('participants', 'users.id','=','participants.user_id')
                            ->where('participants.room_id','=',$id)
                            ->select('users.id','users.username')
                            ->get();
        return response()->json(['users' => $participants]);
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
        try {
        //  $header = $request->header('Token');
            $count = Participant::selectRaw('count(*) as total')
                    ->where('room_id',$id)
                    ->where('user_id',$request->user)
                    ->first();
            if ($count->total >= 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already in this room.',
                ], 401);
            }
           Participant::create([
                'room_id' => $id,
                'user_id' => $request->user,   
           ]);  
            return response()->json([
                'status' => true,
                'message' => 'Added user to room'
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
    public function show($id)
    {
        //
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
