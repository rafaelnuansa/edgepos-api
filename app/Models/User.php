<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function authAccessToken()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function getFileAttribute($avatar)
    {
        return $this->uploads . $avatar;
    }

    public static function getTotals($id)
    {
        User::where('role_id', $id)->count();
    }

    public static function getUsers($searchValue)
    {
        return User::join('customer_groups', 'users.customer_group', '=', 'customer_groups.id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email',  'customer_groups.discount as customer_group_discount')
            ->where('users.first_name', 'LIKE', '%' . $searchValue . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $searchValue . '%')
            ->orWhere('users.email', 'LIKE', '%' . $searchValue . '%')
            ->orderBy('id', 'DESC')->get();
    }

    public static function updatePassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    public static function checkUserToken($token)
    {
        return User::where('token', $token)->first();
    }

    public static function getUser($email)
    {
        return User::where('email', $email)->first();
    }

    public static function getUserList()
    {
        return User::select('id', FacadesDB::raw("CONCAT(users.first_name,' ',users.last_name)  as branch_manager"))->get();
    }

    public static function getUserById($userId)
    {
        return User::select('id', FacadesDB::raw("CONCAT(users.first_name,' ',users.last_name)  as branch_manager"))->where('id', $userId)->first();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
