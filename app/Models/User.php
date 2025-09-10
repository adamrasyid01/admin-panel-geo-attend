<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'position_id',
        'face_embedding_id'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // JWT Methods
     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function role() : BelongsTo{
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function userShifts() : HasMany
    {
        return $this->hasMany(UserShift::class, 'user_id');
    }

    
    public function companyLocations() : HasMany
    {
        return $this->hasMany(CompanyLocation::class, 'user_id');
    }
    public function attendances() : HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
    public function leaveRequests() : HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }
    public function approvedLeaveRequests() : HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function overtimeRequests() : HasMany
    {
        return $this->hasMany(OvertimeRequest::class, 'user_id');
    }

    public function approvedOvertimeRequests() : HasMany
    {
        return $this->hasMany(OvertimeRequest::class, 'approved_by');
    }

    public function announcements() : HasMany
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }

    public function notes() : HasMany
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    public function wfhRequests() : HasMany
    {
        return $this->hasMany(WfhRequest::class, 'user_id');
    }

    public function approvedWfhRequests() : HasMany
    {
        return $this->hasMany(WfhRequest::class, 'approved_by');
    }

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function position() : BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function notesBy() : HasMany
    {
        return $this->hasMany(Note::class, 'notes_by');
    }
    public function tasksCreated() : HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function userCompanies() : HasMany
    {
        return $this->hasMany(UserCompany::class, 'user_id');
    }
}