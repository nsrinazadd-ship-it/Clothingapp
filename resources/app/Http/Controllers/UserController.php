<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();

        return response()->json([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'profile_image' => $user->profile_image ?? null, // تأكد إنو عندك عمود "image" بجدول users
        ]);
    }
    
    public function updateProfileImage(Request $request)
    {
        $user = Auth::user();
    
        // التحقق من أن الملف صورة
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // حذف الصورة القديمة إن وُجدت
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }
    
        // حفظ الصورة الجديدة
        $path = $request->file('image')->store('profile_images', 'public');
    
        // تحديث اسم الملف بقاعدة البيانات
        $user->profile_image = $path;
        $user->save();
    
        return response()->json([
            'message' => 'تم تحديث الصورة بنجاح',
            'image_url' => asset('storage/' . $path),
        ]);
    }
    
}
