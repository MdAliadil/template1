<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $dates = ['created_at', 'updated_at']; 
    protected $fillable = ['name','email','mobile','password','exupiwallet','vyparToken','updatemerchantcharge','remember_token','nsdlwallet','lockedamount','role_id','parent_id','company_id','scheme_id','status','address','shopname','gstin','city','state','pincode','pancard','aadharcard','pancardpic','aadharcardpic','gstpic','profile','kyc','callbackurl','remark','resetpwd','otpverify','otpresend','account','bank','ifsc','contact_id1','contact_id2','contact_id3','account2','bank2','ifsc2','account3','bank3','ifsc3','apptoken','disputewallet','clientId','clientSecret','pclientId','pclientSecret','payoutcallbackurl'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected static $logAttributes = ['id','name','email','mobile','password','scheme_id','status','address','shopname','gstin','city','state','pincode','pancard','aadharcard','callbackurl','otpverify','otpresend','account','bank','ifsc','apptoken'];

    protected static $logOnlyDirty = true;

    public $with = ['role', 'company'];
    protected $appends = ['parents'];

    public function role(){
        return $this->belongsTo('App\Model\Role');
    }
    
    public function company(){
        return $this->belongsTo('App\Model\Company');
    }

    public function getParentsAttribute() {
        $user = User::where('id', $this->parent_id)->first(['id', 'name', 'mobile', 'role_id']);
        if($user){
            return $user->name." (".$user->id.")<br>".$user->mobile."<br>".$user->role->name;
        }else{
            return "Not Found";
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}