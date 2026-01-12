<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\GeneralTrait;

class AuthController extends Controller
{

    use GeneralTrait;

    // تسجيل المستخدم
    public function register(Request $request)
    {
        // التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
        ]);
    
        // إنشاء التوكن
        $token = $user->createToken('MyApp')->plainTextToken;
    
        // تجهيز الرد مع الحقول المطلوبة فقط
        return response()->json([
            'message' => 'تم انشاء الحساب بنجاح.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image, // تأكد أنه موجود بالجدول
            ],
            'token' => $token
        ], 201);
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        // التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // إذا كانت المدخلات غير صالحة
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        // نحاول نحصل على المستخدم سواء محذوف أو لا
        $user = User::withTrashed()->where('email', $request->input('email'))->first();
    
        // إذا المستخدم موجود ولكن محذوف ناعم
        if ($user && $user->trashed()) {
            return response()->json([
                'message' => 'تم حذف هذا الحساب. لا يمكنك تسجيل الدخول.'
            ], 403);
        }
    
        // التحقق من صحة البريد وكلمة المرور
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة.'
            ], 401);
        }
    
        // إنشاء توكن للمستخدم
        $token = $user->createToken('MyApp')->plainTextToken;
    
        // الرد بالـ token
        return response()->json([
         'message' => 'تم تسجيل الدخول بنجاح.',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_image' => $user->profile_image, // تأكد أنه موجود بالجدول
        ],
        'token' => $token
    ], 201);
    }



////////////جبلي المستخدمين ////////////


public function getUser(Request $request)
{
    // تحقق من وجود الـ ID
    if (!$request->has('id')) {
        return $this->returnValidationError(collect(['id' => ['رقم المستخدم مطلوب']]));
    }

    // البحث عن المستخدم
    $user = User::select('name', 'email')->find($request->id);

    if (!$user) {
        return $this->returnUnauthorized("يجب تسجيل الدخول أولاً");
    }

    return $this->returnData('user', $user, 'تم جلب بيانات المستخدم');
}

/////////////// log out ////////////

public function logout(Request $request){

    
    $user = $request->user();  /////// يحصل على المستخدم الحالي المصادق عليه عبر توكن Sanctum.

    if ($user?->currentAccessToken()) {        ////////يتأكد أولاً من وجود مستخدم وأن لديه توكن حالي نشط.

        $user->currentAccessToken()->delete();   //////يحذف فقط التوكن الحالي (مو كل التوكنات).


        return $this->returnSuccessMessage('تم تسجيل الخروج بنجاح.', 200);
    }

    return $this->returnError('لم يتم العثور على جلسة مصادق عليها.', 401);   //////لو ما كان في توكن نشط، نرجع Unauthorized برسالة مفهومة.
}



/////////////////////////GHANGE PASSWORD///////////////////
  
public function changePassword(Request $request)
{
    $user = auth()->user();

    $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required|min:6|confirmed', // هذا يتطلب وجود حقل new_password_confirmation
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // التحقق من كلمة السر القديمة
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'كلمة السر القديمة غير صحيحة.',
        ], 403);
    }

    // تغيير كلمة السر
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'تم تغيير كلمة السر بنجاح.',
    ]);
}



public function deleteAccount(Request $request)
{
    $user = auth()->user();

    // حذف ناعم
    $user->delete();

    return response()->json([
        'status' => true,
        'message' => 'تم حذف الحساب بنجاح (Soft Delete).',
    ]);
}
 
}

