<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // alapfüggvények

    public function index()
    {
        return Lending::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $record = new Lending();
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Display the specified resource.
     */
    public function show ($user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)
        ->where('copy_id', $copy_id)
        ->where('start', $start)
        ->get();
        return $lending[0];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $copy_id, $start)
    {
        $record = $this->show($user_id, $copy_id, $start);
        $record->fill($request->all());
        $record->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id, $copy_id, $start)
    {
        $this->show($user_id, $copy_id, $start)->delete();
    }


    // lekérdezések
    public function lendingsFilterByUser(){
        // $user = Auth::user();	//bejelentkezett felhasználó
        // copies: függvény neve!!
        return Lending::with('copies')
        // ->where('user_id','=',$user->id)
        ->get();
    }

    public function lendingsCountDistinct() {
        $user = Auth::user();
        $lendings = DB::table('lendings as l')
        ->join('copies as c', 'l.copy_id', '=', 'c.copy_id')
        ->where('user_id', $user->id)
        ->distinct('c.book_id')
        ->count();
        return $lendings;
    }

    public function activeLendingsData() {
        $user = Auth::user();
        $lendings = DB::table('lendings as l')
        ->select('book_id', 'author', 'title')
        ->join('copies as c', 'l.copy_id', '=', 'c.copy_id')
        ->join('books as b', 'c.book_id', '=', 'b.book_id')
        ->where('user_id', $user->id)
        ->whereNull("end")
        ->groupBy('book_id')
        ->get();
        return $lendings;
    }

    public function bringBack($copy_id, $start) {
        // bejelentkezett felhasználó
        $user = Auth::user();
        // kölcsönzés rekordja
        $lending = $this->show($user->id, $copy_id, $start);
        // mai dátumot kap az end mező
        $lending->end = date(now());
        $lending->save();
        // másik események
        /*
        DB::table('copies')
        ->where('copy_id', $copy_id)
        // update, insert paramétere lista!
        ->update(['status' => 0]);
        */
        DB::select('CALL toStore(?)', array($copy_id));
    }
}
