<?php

namespace App\Http\Controllers;

use App\Forum;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $content = Forum::All();
        return view('home', ['content' => $content, 'title' => 'index']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create', ['title' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $myDb = new Forum();
        $myDb->content = $request->get('content');
        $myDb->save();
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $content = Forum::find($id);
        return view('show', ['content' => $content, 'title' => 'Read a single item']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $content = Forum::find($id);
        return view('edit', ['content' => $content, 'title' => 'edit a single item']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Forum $forum)
    {
        $id = $request->input('id');
        $content = Forum::find($id);
        $content->content = $request->input('content');
        $content->save();
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Forum  $forum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $content = Forum::find($id);
        $content->delete();
        return redirect('/');
    }
}
