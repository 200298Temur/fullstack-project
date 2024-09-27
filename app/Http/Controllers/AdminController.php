<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function index(Request $request){
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }

        if (!Auth::check() && $request->path() == 'login') {

            return view('welcome');
        }
        // you are already logged in... so check for if you are an admin user..
        $user = Auth::user();
        if ($user->role_id == 4) {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }
        // return view('welcome');
        return $this->checkForPermission($user,$request);
    }

    public function checkForPermission($user, $request)
    {
        $permission = json_decode($user->role->permission);
        $hasPermission = false;
        if (!$permission) {
            return view('welcome');
        }

        foreach ($permission as $p) {
            if ($p->name == $request->path()) {
                if ($p->read) {
                    $hasPermission = true;
                }
            }
        }
        if ($hasPermission) {
            return view('welcome');
        }

        // return view('welcome');
        return view('notfound');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

    public function upload(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);
        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }

    public function deleteImage(Request $request){
        $fileName=$request->imageName;
        $this->deleteFileFromServer($fileName,false);
        return "done";
    }

    public function deleteFileFromServer($fileName, $hasFullPath = false)
{
    // Fayl yo'lini to'g'ri yaratish
    $filePath = $hasFullPath ? $fileName : public_path('uploads') . '/' . $fileName;

    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}


    public function addCategory(Request $request){
        $this->validate($request,[
            'categoryName'=>'required',
            "IconImage"=>'required'
        ]);

        return Category::create([
            'categoryName'=>$request->categoryName,
            'IconImage'=>$request->IconImage
        ]);
    }

    public function getCategory(){
        return Category::orderBy('id','desc')->get();
    }
    public function editCategory(Request $request){
        $this->validate($request,[
            'IconImage'=>'required',
            'categoryName'=>'required'
        ]);

        return Category::where('id',$request->id)->update([
            'IconImage'=>$request->IconImage,           
            "categoryName"=>$request->categoryName
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'IconImage' => 'required'
        ]);
    
        // Faylni o'chirish
        $this->deleteFileFromServer($request->IconImage);
    
        // Kategoriya o'chirish
        return Category::where('id', $request->id)->delete();
    }
    public function createUser(Request $request){
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'role_id',
        ]);
        $password=bcrypt($request->password);
        $user=User::create([
            'fullName'=>$request->fullName,
            'email'=>$request->email,
            'password'=>$password,
            'role_id'=>$request->role_id,
        ]);
        return $user;
    }    

    public function getUser(){
        return User::where('role_id','!=','4')->get();
    }
    public function editUser(Request $request)
    {
        // validate request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'role_id'=>'required'
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'role_id'=>$request->role_id
        ];
        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }

    public function adminlogin(Request $request){
        $this->validate($request, [
            'email' => "bail|required|email",
            'password' => 'bail|required|min:6',
        ]); 
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user=Auth::user();
            if($user->role->isAdmin==0){
                Auth::logout();
                return response()->json([
                    'msg'=>'Incorrect login details'
                ],401);
            }
            return response()->json([
                'msg'=>'You are logged in',
                'user'=>$user
            ]);
        }else{
            return response()->json([
                'msg'=>'Incorrect login details'
            ],401);
        }
    }

    public function createRole(Request $request){
        $this->validate($request,[
            'roleName'=>'required',           
        ]);
        $role=Role::create([
            'roleName'=>$request->roleName,
        ]);
        return $role;
    }
    public function getRole(){
        return Role::orderBy('id','asc')->get();
    }
    public function editRole(Request $request){
        $this->validate($request,[
            'roleName'=>'required'
        ]);
        $data=[
            'roleNam'=>$request->roleName
        ];
        $role=Role::where('id',$request->id)->update($data);
        return $data;
    }
    public function deleteRole(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'roleName'=>'required'
        ]);
        return Role::where('id',$request->id)->delete();
    }  

    public function assignRoles(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'permission'=>'required',
        ]);
        return Role::where('id',$request->id)->update([
            'permission'=>$request->permission
        ]);
    }

    public function uploadEditorImage(Request $request){
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);
        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://fullstack.localhost/uploads/$picName",
            ],
        ]);
        return $picName;
    }

    public function slug() {
        $title = 'This is nice title';
    
       return Blog::create([
            'title' => $title,
            'post' => 'some post',
            'post_excerpt' => 'aead',
            'user_id' => 1, 
            'metaDescription' => 'aead',
            'featuredImage'=>'image.jpeg'
        ]);
        
        // return $title; 
    }

    public function createBlog(Request $request){
        
    }    
}