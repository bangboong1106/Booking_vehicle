<?php

use App\Model\Entities\ReceiptPayment;
use Illuminate\Database\Seeder;

class ReceiptPaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $datas = [
            ['type' => 2, 'name' => 'Thầu phụ', 'is_system' => 1],
            ['type' => 2, 'name' => 'Nhiên liệu', 'is_system' => 1],
            ['type' => 2, 'name' => 'Phí cầu phà', 'is_system' => 1],
            ['type' => 2, 'name' => 'Lương cố định theo chuyến', 'is_system' => 1],
            ['type' => 2, 'name' => 'Lương doanh số theo chuyến', 'is_system' => 1],
            ['type' => 2, 'name' => 'Sửa chữa, bảo dưỡng', 'is_system' => 1],
            ['type' => 2, 'name' => 'Ăn nghỉ', 'is_system' => 1],
            ['type' => 2, 'name' => 'Bãi đỗ xe', 'is_system' => 1],
            ['type' => 2, 'name' => 'Đặc thù tuyến', 'is_system' => 1],
            ['type' => 2, 'name' => 'Bốc xếp, nâng hạ', 'is_system' => 1],
            ['type' => 2, 'name' => 'Chi phí khác', 'is_system' => 1]
        ];
        ReceiptPayment::buildTree($datas);
    }
}
