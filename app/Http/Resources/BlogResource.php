<?php

namespace App\Http\Resources;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($request->routeIs('blog.details')){
            $related_bolgs = Blog::query()
            ->select('id','title','photos')
            ->where('id','!=', $this->id)
            ->where('tag_id', $this->tag_id)
            ->where('status',1)
            ->get();

            $tags= Tag::query()->select('id','title')->get();
            
            return [
                'id'=> $this->id,
                'created_by'=> $this->admin?->name,
                'title'=> $this->title,
                'description'=> $this->description,
                'date'=> $this->date,
                'photo'=> addUrl(collect(json_decode($this->photos))),
                'comments'=> BlogCommentResource::collection($this->comments),
                'tags'=> $tags,
                'related_bolgs'=> RelatedBlogResource::collection($related_bolgs),
            ];
        }else{
            return [
                'id'=> $this->id,
                'created_by'=> $this->admin?->name,
                'title'=> $this->title,
                'description'=> $this->description,
                'date'=> $this->date,
                'photo'=> addUrl(collect(json_decode($this->photos))->take(1)),
            ];
        }

    }
}
