<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\FcmToken;
use App\Repositories\Base\CustomRepository;
use DB;

class FcmTokenRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return FcmToken::class;
    }

    public function getFcmTokenByUserIdAndToken($userId, $token)
    {
        if ($userId && $token)
            return $this->search([
                'user_id_eq' => $userId,
                'fcm_token_eq' => $token,
                'del_flag_eq' => 0
            ])->first();
        return null;
    }

    public function getFcmTokenByUserIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        return $this->search(['user_id_in' => $userIds,
            'del_flag_eq' => 0])->pluck('fcm_token')->toArray();
    }

    public function getFcmTokenByDriverIds($driverIds)
    {
        if (empty($driverIds)) {
            return [];
        }
        return $this->search([
            'driver_id_in' => $driverIds,
            'platform_type_eq' => AppConstant::PLATFORM_TYPE_MOBILE,
            'del_flag_eq' => 0
        ])->pluck('fcm_token');
    }

    public function getFcmTokenAppByUserIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        return $this->search([
            'user_id_in' => $userIds,
            'platform_type_eq' => AppConstant::PLATFORM_TYPE_MOBILE,
            'del_flag_eq' => 0
        ])->pluck('fcm_token');
    }

    public function getFcmTokenWebByUserIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        return $this->search([
            'user_id_in' => $userIds,
            'platform_type_eq' => AppConstant::PLATFORM_TYPE_WEB,
            'del_flag_eq' => 0
        ])->pluck('fcm_token');
    }

    public function getFcmFullByToken($token)
    {
        if ($token)
            return $this->search(['fcm_token_eq' => $token,
                'del_flag_eq' => 0])->first();
        return null;
    }

    public function getFcmFullByTokens($tokens)
    {
        if (empty($tokens)) {
            return [];
        }
        return $this->search(['fcm_token_in' => $tokens,
            'del_flag_eq' => 0])->get();
    }

    public function checkExistUserIdAndToken($userId, $token)
    {
        if ($userId && $token) {
            $fcmToken = $this->search([
                'user_id_eq' => $userId,
                'fcm_token_eq' => $token,
                'del_flag' => '0'
            ])->first();
            if ($fcmToken != null)
                return true;
        }
        return false;
    }

    public function getFcmTokenByRole($role, $userIds)
    {
        if (empty($role)) {
            return [];
        }
        $query = DB::table('fcm_tokens')
            ->join('admin_users', 'admin_users.id', '=', 'fcm_tokens.user_id')
            ->where([
                ['fcm_tokens.del_flag', '=', '0'],
                ['admin_users.del_flag', '=', '0']
            ])
            ->whereIn('admin_users.role', $role);
        if (!empty($userIds)) {
            $query = $query->whereIn(
                'fcm_tokens.user_id', $userIds
            );
        }
        return $query->get(['fcm_token', 'user_id']);
    }
}