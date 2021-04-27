<?php

namespace App\Repositories\Client;

use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\Customer;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Common\AppConstant;

class ClientUserClientRepository extends CustomerRepository
{
    protected function getIgnoreClientID()
    {
        return true;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            DB::raw('admin_users.username as username'),
            DB::raw('admin_users.email as email'),
            DB::raw('files.path'),
            DB::raw('files.file_type'),
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->leftJoin('admin_users', $table_name . '.user_id', '=', 'admin_users.id')
            ->leftJoin('files', $table_name . '.avatar_id', '=', 'files.file_id')
            ->where([
                [$table_name . '.del_flag', '=', 0],
                [$table_name . '.parent_id', '=', $customerID],
                [$table_name . '.customer_type', '=', 3],
            ])
            ->select($columns);
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.full_name', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.customer_code', 'like', '%' . $textSearch . '%')
                    ->orWhere('admin_users.username', 'like', '%' . $textSearch . '%')
                    ->orWhere('admin_users.email', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }

    protected function getClientItems($items)
    {
        if ($items instanceof Collection) {
            foreach ($items as $item) {
                $item->path_of_avatar_id = AppConstant::getImagePath($item->path, $item->file_type);
            }
        } else {
            $items->path_of_avatar_id = AppConstant::getImagePath($items->path, $items->file_type);
        }

        return $items;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Customer::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid entity id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 13/11/2020
    public function preSaveEntity($entity, $parameters)
    {
        $entity->parent_id = $parameters['customer_id'];
        $entity->customer_type = 3;
        $entity->type = $parameters['type'];
        return $entity;
    }

    // API lưu thêm thông tin tài khoản
    // CreatedBy nlhoang 13/11/2020
    public function saveRelationEntity($entity, $parameters)
    {
        if (empty($entity->user_id)) {
            $user = $entity->adminUser()->create([
                'username' => $parameters['username'],
                'password' => empty($parameters['password']) ? '' : genPassword($parameters['password']),
                'full_name' => $entity->full_name,
                'email' => $parameters['email'],
                'role' => 'customer'
            ]);
            $entity->user_id = $user->id;
            $entity->save();
        } else {
            $user = AdminUserInfo::where('id', $entity->user_id)->first();
            $user->update([
                'username' => $parameters['username'],
                'password' => empty($parameters['password']) ? $user->password : genPassword($parameters['password']),
                'full_name' => $entity->full_name,
                'email' => $parameters['email'],
            ]);
        }
    }
}
