<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use Illuminate\Support\Facades\Storage;

class CharacterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $characters = Character::all();
        return view('characters.index', compact('characters'));
    }

    public function create()
    {
        return view('characters.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'description' => 'required',
            'rol_order' => 'required',
        ]);
        $character = new Character;
        $character->name = $request->name;
        $character->description = $request->description;
        $character->genre = $request->genre;
        $character->rol_order = $request->rol_order;
        $character->save();
        return redirect('/characters');
    }

    public function show($id)
    {
        $character = Character::findOrFail($id);
        return view('characters.show', compact('character'));
    }

    public function edit($id)
    {
        $character = Character::findOrFail($id);
        return view('characters.edit', compact('character'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',

        ]);
        $form_data = array(
            'name' => $request->name,
            'description' => $request->description,
            'rol_order' => $request->rol_order,
            'genre' => $request->genre

        );
        Character::whereId($id)->update($form_data);
        return redirect('characters')->with('success', 'Data is successfully updated');
    }

    public function destroy($id)
    {
        $character = Character::findOrFail($id);
        $character->delete();
        return redirect('characters')->with('success', 'Data is successfully deleted');
    }
}
