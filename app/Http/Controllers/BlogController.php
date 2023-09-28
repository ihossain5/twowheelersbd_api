<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
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
}
