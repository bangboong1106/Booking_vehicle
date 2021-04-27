<?php

namespace App\Model\Presenters;
/**
 * Trait AdminUserInfo
 * @package App\Model\Presenters
 */
/**
 * Trait UserInfo
 * @package App\Model\Presenters
 */
trait AdminUserInfo
{
    use Base;

    /**
     * @return string
     */
    public function getLastLoginTime()
    {
        return format($this->login_datetime, 'Y-m-d H:i');
    }

    /**
     * @return mixed|string
     */
    public function authTypeText()
    {
        return getTextValue('user_auth_types', $this->auth_type);
    }

    /**
     * @return mixed|string
     */
    public function groupInfoText()
    {
        return getTextValue('user_group_infos', $this->group_info_id);
    }

    /**
     * @return string
     */
    public function passwordText()
    {
        if ($this->getAttribute('password')) {
            //return '<input readonly="true" type="password" name="password" value="******" style="border: none;">';
            return "******";
        } else {
            return '--';
        }

    }

    /**
     * @return bool
     */
    public function isSupperAdmin()
    {
        return getConstant('SUPPER_ADMIN_TYPE') == $this->auth_type;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return getConstant('ADMIN_TYPE') == $this->auth_type;
    }

    /**
     * @return bool
     */
    public function isModerator()
    {
        return getConstant('MODERATOR_TYPE') == $this->auth_type;
    }

    /**
     * @return bool
     */
    public function isOwner()
    {
        return getCurrentUserId() == $this->id;
    }

    /**
     * @return bool
     */
    public function allowEdit()
    {
        return backendGuard()->user()->isSupperAdmin() || $this->isOwner();
    }

    /**
     * @return bool
     */
    public function allowDelete()
    {
        return backendGuard()->user()->isSupperAdmin() && !$this->isOwner();
    }

    public function getAuthTypes()
    {
        $currentUser = backendGuard()->user();
        $authTypes = getConfig('user_auth_types', []);
        switch (true) {
            case $currentUser->isSupperAdmin():
                break;
            case $this->isSupperAdmin():
                unset($authTypes[getConstant('ADMIN_TYPE')]);
                unset($authTypes[getConstant('MODERATOR_TYPE')]);
                break;
            case $currentUser->isAdmin():
                unset($authTypes[getConstant('SUPPER_ADMIN_TYPE')]);
                break;
            case $currentUser->isModerator():
                unset($authTypes[getConstant('SUPPER_ADMIN_TYPE')]);
                unset($authTypes[getConstant('ADMIN_TYPE')]);
                break;
        }
        return $authTypes;
    }
}