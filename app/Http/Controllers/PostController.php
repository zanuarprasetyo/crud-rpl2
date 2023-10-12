<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
    //get post
    $posts = Post::latest()->paginate(5);

    // render view with posts
    return view('posts.index', compact('posts'));
    //
}

public function create()
{
    return view('posts.create');
}
/**
 * store
 * @param Request $request
 * @return void
 */
public function store(Request $request)
{
    // validate form
    $this->validate($request, [
        'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svc|max:2048',
        'title'     => 'required|min:5',
        'content'   => 'required|min:10'
    ]);
    // upload image
    $image = $request->file('image');
    $image->storeAs('public/posts', $image->hashName());
    // create post
    Post::create([
        'image'     => $image->hashName(),
        'title'     => $request->title,
        'content'   => $request->content
    ]);
    return redirect()->route('posts.index')->with(['success' => 'Data berhasil disimpan!']);
} 
public function edit(Post $post)
{
    return view('posts.edit', compact('post'));
}

// public function update(Request $request, Post $post): RedirectResponse
// {
//     // validate form
//     $this->validate($request, [
//         'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svc|max:2048',
//         'title'     => 'required|min:5',
//         'content'   => 'required|min:10' 
//     ]);

//     // check if image is upload
//     if ($request->hasFile('image')) {

//         // upload new image
//         $image = $request->file('image');
//         $image->storeAs('public/posts', $image->hashName());

//         // delete old image
//         Storage::delete('public/posts/' . $post->image);


//         // update post with
//         $post->update([
//             'image'     => $image->hashName(),
//             'title'     => $request->title,
//             'content'   => $request->content
//         ]);
//     }else{
//         // update post without image
//         $post->update([
//             'title'   => $request->title,
//             'content' => $request->content  
//         ]);
//     }
//     // redirect to index
//     return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan']);
// }

public function update(Request $request, Post $post): RedirectResponse
{
    // Validate form
    $this->validate($request, [
        'title'   => 'required|min:5',
        'content' => 'required|min:10' 
    ]);

    if ($request->hasFile('image')) {
        // Upload new image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        // Delete old image
        Storage::delete('public/posts/' . $post->image);
        // Update post with new image
        $post->update([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);
    } else {
        // Update post without changing the image
        $post->update([
            'title'   => $request->title,
            'content' => $request->content
        ]);
    }
    // Redirect to index
    return redirect()->route('posts.index')->with(['success' => 'Data berhasil di edit']);
}


public function destroy(Post $post):RedirectResponse
{
    // delete image
    Storage::delete('public/posts/'.$post->image);
    // delete post
    $post->delete();
    // redirect to index
    return redirect()->route('posts.index')->with(['success' => 'Data berhasil dihapus']);
}
public function show(string $id):View
{
    // get post by ID
    $post = Post::findOrFail($id);
    // render view with post
    return view('posts.show', compact('post'));
}
} 