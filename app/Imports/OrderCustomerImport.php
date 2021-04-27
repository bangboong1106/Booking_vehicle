<?php

namespace App\Imports;

use App\Model\Entities\OrderCustomer;
use Illuminate\Support\Str;

class OrderCustomerImport extends BaseImport
{
    protected $_customerDelegate;
    protected $_customerPhone;

    protected $indexRow = 1;

    public function __construct($customerList)
    {
        if (!empty($customerList)) {
            $this->_customerDelegate = $customerList->pluck('delegate', 'customer_code');
            $this->_customerPhone = $customerList->pluck('mobile_no', 'customer_code');
        }
    }

    public function map($row, $excelColumnConfig = null, $dataEx = null): array
    {
        $result = parent::map($row, $excelColumnConfig, $dataEx);

        //Lấy người đại diện, SDT khách hàng mặc định nếu ko nhập
        $customer = isset($result['customer_code']) ? $result['customer_code'] : '';
        $customerName = isset($result['customer_name']) ? $result['customer_name'] : '';
        $customerPhone = isset($result['customer_mobile_no']) ? $result['customer_mobile_no'] : '';
        if (!empty($customer)) {
            if (empty($customerName) && isset($this->_customerDelegate[$customer])) {
                $result['customer_name'] = $this->_customerDelegate[$customer];
            }
            if (empty($customerPhone) && isset($this->_customerPhone[$customer])) {
                $result['customer_mobile_no'] = $this->_customerPhone[$customer];
            }
        }

        return $result;
    }

    public function model(array $row)
    {
        return new OrderCustomer($row);
    }

    public function sheets(): array
    {
        return [
            0 => new OrderCustomerImport(),
        ];
    }

    public function convertCommissionType($commissionType)
    {
        if (empty($commissionType)) {
            return config('constant.TONG_TIEN_HOA_HONG');
        }
        $text = mb_strtoupper(Str::slug($commissionType));
        if ($text == 'PHAN-TRAM') {
            return config('constant.PHAN_TRAM_HOA_HONG');
        }
        return config('constant.TONG_TIEN_HOA_HONG');
    }

    public function convertPaymentType($paymentType)
    {
        if (empty($paymentType)) {
            return config('constant.CHUYEN_KHOAN');
        }
        $text = mb_strtoupper(Str::slug($paymentType));
        if ($text == 'TIEN-MAT') {
            return config('constant.TIEN_MAT');
        }
        return config('constant.CHUYEN_KHOAN');
    }

    public function headingRow(): int
    {
        return 10;
    }
}
