<?php

namespace App\Repositories;

use App\Model\Entities\Contact;
use App\Repositories\Base\CustomRepository;
use DB;

class ContactRepository extends CustomRepository
{
    protected $_fieldsSearch = ['contact_name', 'email', 'phone_number', 'full_address', 'active'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Contact::class;
    }

    public function validator()
    {
        return \App\Validators\ContactValidator::class;
    }

    public function checkExistPhoneNumberAndName($phoneNumber, $contactName)
    {
        if ($phoneNumber && $contactName) {
            $entity = $this->search([
                'phone_number_eq' => $phoneNumber,
                'contact_name_eq' => $contactName
            ])->first();
            if ($entity != null)
                return true;
        }
        return false;
    }

}
