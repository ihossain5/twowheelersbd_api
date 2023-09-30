<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogController extends Controller {
    public function blogs(Request $request) {
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $blogs = Blog::query()
            ->select('id', 'title', 'description', 'photos', 'date', 'admin_id')
            ->where('status', 1)
            ->latest()
            ->paginate($this->pagination);

        return $this->success(BlogResource::collection($blogs)->response()->getData(true));
    }

    public function details($id) {
        $blog = Blog::query()
            ->with('admin:id,name', 'comments')
            ->select('id', 'title', 'description', 'photos', 'date', 'admin_id', 'tag_id')
            ->where('status', 1)
            ->findOrFail($id);

        return $this->success(new BlogResource($blog));
    }

    public function addComment($id, Request $request){
        $this->validate($request,['comment' => 'required']);

        $blog_comment = new BlogComment();
        $blog_comment->blog_id = $id;
        $blog_comment->user_id = auth()->user()->id;
        $blog_comment->comment = $request->comment;
        $blog_comment->save();

        return $this->success('Comment has been added successfully');
    }
}
