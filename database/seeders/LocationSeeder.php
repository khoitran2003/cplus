<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7769,
                'longitude' => 106.7009,
                'description' => 'Trung tâm kinh tế, tài chính của TP. Hồ Chí Minh',
                'is_active' => true
            ],
            [
                'name' => 'Quận 3',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7829,
                'longitude' => 106.6870,
                'description' => 'Khu vực trung tâm với nhiều trường học và bệnh viện',
                'is_active' => true
            ],
            [
                'name' => 'Quận 7',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7326,
                'longitude' => 106.7229,
                'description' => 'Khu đô thị mới phát triển với nhiều khu công nghiệp',
                'is_active' => true
            ],
            [
                'name' => 'Quận Bình Thạnh',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8142,
                'longitude' => 106.7108,
                'description' => 'Khu vực phát triển với nhiều dự án bất động sản',
                'is_active' => true
            ],
            [
                'name' => 'Quận Gò Vấp',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8396,
                'longitude' => 106.6792,
                'description' => 'Khu vực dân cư đông đúc với nhiều tiện ích',
                'is_active' => true
            ],
            [
                'name' => 'Quận Tân Bình',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8015,
                'longitude' => 106.6525,
                'description' => 'Gần sân bay Tân Sơn Nhất, thuận tiện giao thông',
                'is_active' => true
            ],
            [
                'name' => 'Quận Phú Nhuận',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7944,
                'longitude' => 106.6780,
                'description' => 'Khu vực trung tâm với nhiều dịch vụ cao cấp',
                'is_active' => true
            ],
            [
                'name' => 'Quận Thủ Đức',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8700,
                'longitude' => 106.7600,
                'description' => 'Thành phố Thủ Đức - Khu đô thị sáng tạo',
                'is_active' => true
            ],
            [
                'name' => 'Quận 2',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7870,
                'longitude' => 106.7498,
                'description' => 'Khu đô thị mới với nhiều khu phức hợp cao cấp',
                'is_active' => true
            ],
            [
                'name' => 'Quận 9',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8428,
                'longitude' => 106.8095,
                'description' => 'Khu vực phát triển công nghiệp và công nghệ cao',
                'is_active' => true
            ],
            [
                'name' => 'Quận Bình Tân',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.7654,
                'longitude' => 106.6031,
                'description' => 'Khu vực phát triển với nhiều khu công nghiệp',
                'is_active' => true
            ],
            [
                'name' => 'Quận Hóc Môn',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.8833,
                'longitude' => 106.5833,
                'description' => 'Khu vực ngoại thành với tiềm năng phát triển',
                'is_active' => true
            ],
            [
                'name' => 'Quận Củ Chi',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.9700,
                'longitude' => 106.4900,
                'description' => 'Khu vực ngoại thành với nhiều khu công nghiệp',
                'is_active' => true
            ],
            [
                'name' => 'Quận Nhà Bè',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.6833,
                'longitude' => 106.7333,
                'description' => 'Khu vực ven sông với tiềm năng phát triển cảng biển',
                'is_active' => true
            ],
            [
                'name' => 'Quận Cần Giờ',
                'city' => 'TP. Hồ Chí Minh',
                'province' => 'TP. Hồ Chí Minh',
                'country' => 'Việt Nam',
                'latitude' => 10.4167,
                'longitude' => 106.9500,
                'description' => 'Khu vực sinh thái với rừng ngập mặn',
                'is_active' => true
            ]
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
