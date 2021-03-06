<?php

namespace App\Models;

use App\Repositories\DepartmentClassRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class Student extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    UserInterface
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    const GENDER_BOY = false;
    const GENDER_GAIL = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_num',
        'student_name',
        'abbreviation_pinyin1',
        'abbreviation_pinyin2',
        'full_pinyin1',
        'full_pinyin2',
        'department_class_id',
        'department_id',
        'gender',
        'id_card'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id_card', 'remember_token', 'wx_openid'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'gender' => 'boolean'
    ];

    protected $dates = ['report_at', 'arrive_dorm_at', 'allow_report_at', 'created_at', 'updated_at'];

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function dormitorySelection()
    {
        return $this->hasOne(DormitorySelection::class);
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['id_card'];
    }

    private $idCardWithMosaic = null;

    public function getIdCardWithMosaicAttribute()
    {
        if (is_null($this->idCardWithMosaic)) {
            $this->idCardWithMosaic = preg_replace('/(\d{6})\d{8}([\dxX]{4})/', '$1********$2', $this->attributes['id_card']);
        }
        return $this->idCardWithMosaic;
    }

    public function hasBeenReport()
    {
        return !is_null($this->report_at);
    }

    public function hasBeenArriveDorm()
    {
        return !is_null($this->arrive_dorm_at);
    }

    public function isAllowReport()
    {
        return !is_null($this->allow_report_at);
    }

    /**
     * @return DepartmentClass
     */
    public function getDepartmentClass()
    {
        return app(DepartmentClassRepositoryInterface::class)->getDepartmentClass($this->department_class_id);
    }

    public function authorize($ability, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->authorize($ability, $arguments);
    }

    public function scopeByDepartment($query, $department)
    {
        if ($department instanceof DepartmentClass)
            $department = $department->id;
        return $query->where('department_id', $department);
    }

    public function scopeByDepartmentClass($query, $departmentClass)
    {
        if ($departmentClass instanceof DepartmentClass)
            $departmentClass = $departmentClass->id;
        return $query->where('department_class_id', $departmentClass);
    }

    public function scopeByReport($query, $isReport)
    {
        if ($isReport) {
            return $query->whereNotNull('report_at');
        } else {
            return $query->whereNull('report_at');
        }
    }

    public function scopeNotReport($query)
    {
        return $query->whereNull('reported_at');
    }

    public function getDepartmentId()
    {
        return $this->department_id;
    }

}
