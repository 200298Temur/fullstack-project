<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
     protected $fillable = ['title', 'post', 'post_excerpt', 'slug', 'user_id', 'featuredImage', 'metaDescription', 'views','jsonData'];
     
     public function setSlugAttribute($title)
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

     public function cat(){
        return $this->belongsToMany(Category::class,'blogcategories');
     }
     public function tag(){
        return $this->belongsToMany(Tag::class,'blogtags');
     }
}
