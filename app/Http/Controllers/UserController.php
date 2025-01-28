<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classess;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(){
        $joinClassess = DB::table('class_members')
        ->where('user_id', auth()->user()->id)
        ->pluck('class_id');
        $classess = Classess::whereIn('id', $joinClassess)->where('status','ok')->get(); 
        return view('user.index', ['classess' => $classess]);
    }
    public function archive(){
        $joinClassess = DB::table('class_members')
        ->where('user_id', auth()->user()->id)
        ->pluck('class_id');
        $classess = Classess::whereIn('id', $joinClassess)->where('status','archive')->get(); 
        return view('user.archiveclass', ['classess' => $classess]);
    }

    public function setting(){
        $userID = auth()->user()->id;
        $user = User::where('id' , $userID)->first();
        return view('user.setting.setting', ['user' => $user]);
    }

    public function changeName(){
        $userID = auth()->user()->id;
        $user = User::where( 'id' , $userID)->first();
        return view('user.setting.namesetting', ['user' => $user]);
    }

    public function changeNamePost(Request $request, string $id):JsonResponse{
        $request->validate([
            'name' => 'required|string'
        ]);
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->save();
        return response()->json(['success' => 'Name Updated Successfully!','redirectUrl' => route('user.setting')]);
    }

    public function accountSetting(){
        $userID = auth()->user()->id;
        $user = User::where( 'id' , $userID)->first();
        return view('user.setting.accountsetting', ['user' => $user]);
    }
    public function changePassword(Request $request, string $id):JsonResponse{
        $request->validate([
            'newPass' => 'required|min:6|string|confirmed'
        ]);

        User::where('id', $id)->update(['password' => Hash::make($request->newPass)]);

        return response()->json(['success' => 'Password Changed Successfully!','redirectUrl' => route('user.setting')]);
    }
    public function changeProfile(Request $request):JsonResponse{
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $user_id = auth()->user()->id;
        $currentUser = DB::table('users')->where('id', $user_id)->first();

        if ($image = $request->file('profile_picture')) {
            $destinationPath = public_path('images/');
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            if(!empty($currentUser->profile)){
                $oldImage = $destinationPath . $currentUser->profile;
                if($oldImage){
                    unlink($oldImage);
                }
            }
            $image->move($destinationPath, $profileImage);
        }
        DB::table('users')->where('id', $user_id)->update(['profile' => $profileImage]);
        return response()->json(['success' => 'Profile Change Successfully!', 'redirectUrl' => route('user.index')]);
    }
}
