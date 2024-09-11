<?php

namespace App\Services;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

class TagService
{
  
    public function store(TagRequest $request)
    {
        $data = $request->validated();
        $tag=Tag::create($data);
        return $tag;
    }

    public function getAll(){
       return Tag::paginate(5);
    }
  
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
