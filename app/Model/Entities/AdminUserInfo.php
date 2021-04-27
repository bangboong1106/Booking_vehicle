<?php

namespace App\Model\Entities;

use App\Http\Controllers\Backend\Auth\MailResetPasswordNotification;
use App\Model\Base\Auth\User;
use Exception;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed vehicleTeams
 * @property mixed id
 * @property mixed listCustomer
 * @property mixed customers
 */
class AdminUserInfo extends User implements JWTSubject, Auditable
{
    protected $table = "admin_users";
    use Notifiable;
    use \App\Model\Presenters\AdminUserInfo;
    use HasRoles;
    protected $_alias = 'admin';
    protected $fillable = ['username', 'email', 'password', 'avatar_id','partner_id', 'role', 'full_name', 'last_login_time', 'active', 'remember_token'];
    public $listRole;
    public $listVehicleTeam;
    public $listCustomerGroup;
    protected $guard_name = 'admins';

    protected $hidden = ['password', 'ins_id', 'ins_date', 'upd_date', 'del_flag'];
    protected static $_destroyRelations = ['avatarFile'];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'username',
        'email',
        'password',
        'full_name'
    ];

    protected $_detailNameField = 'username';

    public function vehicleTeams()
    {
        return $this->belongsToMany('App\Model\Entities\VehicleTeam', 'admin_users_vehicle_teams', 'admin_user_id', 'vehicle_team_id');
    }

    public function customerGroups()
    {
        return $this->belongsToMany('App\Model\Entities\CustomerGroup', 'admin_users_customer_group', 'admin_user_id', 'customer_group_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id', 'id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = genPassword($value);
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {

        return null; // not supported
    }

    public function setRememberToken($value)
    {
        // not supported
    }


    /**
     * @return string
     */
    public function getRoleText()
    {
        if (empty($this->roles)) {
            return '';
        }

        $roles = $this->roles->pluck('title', 'name')->transform(function ($item, $key) {
            return "<span class='grid-tag'>" . $item . "</span>";
        })->implode("");
        return '<div class="list-tag-column">' . $roles . '</div>';
    }

    public function avatarFile()
    {
        return $this->hasOne(File::class, 'file_id', 'avatar_id');
    }

    // Start JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // End JWT

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    public function generateTags(): array
    {
        $data = $this->getExtendData();
        $currentVehicleTeams = empty($data['current_vehicle_teams']) || $data['current_vehicle_teams'] == $data['vehicle_teams'] ? null :
            VehicleTeam::whereIn('id', $data['current_vehicle_teams'])->get()->pluck('name')->toArray();
        $listVehicleTeam = empty($data['vehicle_teams']) || $data['current_vehicle_teams'] == $data['vehicle_teams'] ? null :
            VehicleTeam::whereIn('id', $data['vehicle_teams'])->get()->pluck('name')->toArray();

        $currentCustomerGroups = empty($data['current_customer_groups']) || $data['current_customer_groups'] == $data['customer_groups'] ? null :
            CustomerGroup::whereIn('id', $data['current_customer_groups'])->get()->pluck('name')->toArray();
        $listCustomerGroup = empty($data['customer_groups']) || $data['current_customer_groups'] == $data['customer_groups'] ? null :
            CustomerGroup::whereIn('id', $data['customer_groups'])->get()->pluck('name')->toArray();

        return [
            'current_roles' => empty($data['current_roles']) || $data['current_roles'] == $data['roles'] ? '' : implode('| ', $data['current_roles']),
            'roles' => empty($data['roles']) || $data['current_roles'] == $data['roles'] ? '' : $this->_getRolesTitle($data['roles']),
            'current_vehicle_teams' => isset($currentVehicleTeams) ? implode('| ', $currentVehicleTeams) : '',
            'vehicle_teams' => isset($listVehicleTeam) ? implode('| ', $listVehicleTeam) : '',
            'current_customer_groups' => isset($currentCustomerGroups) ? implode('| ', $currentCustomerGroups) : '',
            'customer_groups' => isset($listCustomerGroup) ? implode('| ', $listCustomerGroup) : '',
        ];
    }

    protected function _getRolesTitle($roles = [])
    {
        return Role::whereIn('name', $roles)->get()->implode('title', '|');
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.password')) {
                $data['old_values']['password'] = '******';
                $data['new_values']['password'] = '******';
            }

            if (isset($data['tags'])) {
                $extendData = explode(',', $data['tags']);

                if (!empty($extendData[0]) || !empty($extendData[1])) {
                    $data['old_values']['role'] = str_replace('|', ',', $extendData[0]);
                    $data['new_values']['role'] = str_replace('|', ',', $extendData[1]);
                }
                if (!empty($extendData[2]) || !empty($extendData[3])) {
                    $data['old_values']['driver_team'] = str_replace('|', ',', $extendData[2]);
                    $data['new_values']['driver_team'] = str_replace('|', ',', $extendData[3]);
                }
            }
        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }
}

