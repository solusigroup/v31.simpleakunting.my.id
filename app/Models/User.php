<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id_user';
    }
    
    // Legacy schema uses 'nama_user' instead of 'name' or 'email' for login
    // and 'password_hash' for password
    
    protected $fillable = [
        'nama_user',
        'password_hash',
        'role',
        'jabatan',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the name attribute (alias for nama_user).
     */
    public function getNameAttribute()
    {
        return $this->nama_user;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array|string $roles
     * @return bool
     */
    public function hasRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is superuser.
     */
    public function isSuperuser(): bool
    {
        return $this->role === 'superuser';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manajer.
     */
    public function isManajer(): bool
    {
        return $this->role === 'manajer';
    }

    /**
     * Check if user is staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user can manage other users.
     * Only superuser and admin can manage users.
     */
    public function canManageUsers(): bool
    {
        return $this->hasRole(['superuser', 'admin']);
    }

    /**
     * Check if user can manage master data (create/edit/delete).
     * Only superuser and admin can manage master data.
     */
    public function canManageMasterData(): bool
    {
        return $this->hasRole(['superuser', 'admin']);
    }

    /**
     * Check if user can view master data.
     * Superuser, admin, and manajer can view master data.
     */
    public function canViewMasterData(): bool
    {
        return $this->hasRole(['superuser', 'admin', 'manajer']);
    }

    /**
     * Check if user can view reports.
     * Superuser, admin, and manajer can view reports.
     */
    public function canViewReports(): bool
    {
        return $this->hasRole(['superuser', 'admin', 'manajer']);
    }

    /**
     * Check if user can access database management.
     * Only superuser can access database management.
     */
    public function canAccessDatabase(): bool
    {
        return $this->isSuperuser();
    }

    /**
     * Check if user can manage company settings.
     * Only superuser and admin can manage company settings.
     */
    public function canManageCompany(): bool
    {
        return $this->hasRole(['superuser', 'admin']);
    }

    /**
     * Check if user can approve transactions.
     * Superuser, admin, and manajer can approve.
     */
    public function canApprove(): bool
    {
        return $this->hasRole(['superuser', 'admin', 'manajer']);
    }

    /**
     * Check if user can import/export data.
     * Superuser, admin, and manajer can import/export.
     */
    public function canImportExport(): bool
    {
        return $this->hasRole(['superuser', 'admin', 'manajer']);
    }

    /**
     * Get role level (lower = more privileged).
     */
    public function getRoleLevel(): int
    {
        return match($this->role) {
            'superuser' => 1,
            'admin' => 2,
            'manajer' => 3,
            'staff' => 4,
            default => 99,
        };
    }

    /**
     * Check if user can edit another user based on role hierarchy.
     */
    public function canEditUser(User $targetUser): bool
    {
        // Cannot edit users with higher or equal privilege
        return $this->getRoleLevel() < $targetUser->getRoleLevel();
    }
}
