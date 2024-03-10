<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCharacterRequest;
use App\Http\Resources\CharactersResource;
use App\Models\Character;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{

    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CharactersResource::collection(
            Character::where('user_id', Auth::user()->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCharacterRequest $request)
    {
        $request->validated($request->all());

        $character = Character::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'sex' => $request->sex
        ]);

        return new CharactersResource($character);
    }
    /**
     * Show a resource in storage.
     */
    public function show(Character $character) {

        return $this->isNotAuthorized($character) ? $this->isNotAuthorized($character) : new CharactersResource($character);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Character $character)
    {

        if(Auth::user()->id !== $character->user_id) {
            return $this->error("", "Forbidden.", 403);
        } 

        $character->update($request->all());

        return new CharactersResource($character);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character)
    {
        return $this->isNotAuthorized($character) ? $this->isNotAuthorized($character) : $character->delete();
    }

    private function isNotAuthorized($character) {
        if(Auth::user()->id !== $character->user_id) {
            return $this->error("", "Forbidden.", 403);
        } 
    }
}
