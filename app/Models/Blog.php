<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
     protected $fillable = ['title', 'post', 'post_excerpt', 'slug', 'user_id', 'featuredImage', 'metaDescription', 'views'];
     
     public function setTitleAttribute($title)
     {
         $this->attributes['title'] = $title;
         $this->attributes['slug'] =$this->uniqSlug($title);
     }
     
     private function uniqSlug($title){
        $slug=Str::slug($title, '-');
        $count=Blog::where('slug','LIKE',"{$slug}%  ")->count();
        $newCount=$count>0? ++$count :'';
        return $newCount >0? "$slug-$newCount":"$slug";
     }

}
