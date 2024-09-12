<?php

namespace App\Services;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class TagService
{
  
    public function store(TagRequest $request)
    {
        $data = $request->validated();
        $tag=Tag::create($data);
        return $tag;
    }
    public function getAll(Request $request)
    {
        $page = $request->input('page', 1); 
        $perPage = $request->input('per_page', 5);

        $total = Tag::count();

        $tags = Tag::skip(($page - 1) * $perPage)->take($perPage)->get();

        $last_page = ceil($total / $perPage);

        $data = [
            'data' => $tags, 
            'total' => $total, 
            'last_page' => $last_page 
        ];
        return $data;
    }


    // ----
    public function delete(Tag $tag)
    {
       return $tag->delete();
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $data = $request->validated();
        $tag->update($data);
        return $tag;
    }

}
