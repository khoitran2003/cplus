<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Criteria;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Criteria Types first
        $criteriaTypes = [
            ['type_name' => 'cost'],
            ['type_name' => 'distance'],
            ['type_name' => '2H4R/4H9R'],
            ['type_name' => 'yes/no'],
            ['type_name' => 'condition'],
            ['type_name' => 'year']
        ];

        foreach ($criteriaTypes as $type) {
            \App\Models\CriteriaType::create($type);
        }

        // Create Industries
        $industries = [
            ['industry_name' => 'Manufacturing'],
            ['industry_name' => 'Technology'],
            ['industry_name' => 'Real Estate'],
            ['industry_name' => 'Logistics'],
            ['industry_name' => 'Healthcare'],
            ['industry_name' => 'Education']
        ];

        foreach ($industries as $industry) {
            \App\Models\Industry::create($industry);
        }

        // Create Criteria with hierarchy structure
        $criteria = [
            [
                'criteria_name' => 'Land cost',
                'criteriaTypeId' => null,
                'parentId' => null,
                'clientId' => null,
                'criteriaPercent' => null
            ],
            [
                'criteria_name' => 'Land lease price',
                'criteriaTypeId' => 1, // cost
                'parentId' => 1, // Land cost
                'clientId' => null,
                'criteriaPercent' => 25.00
            ],
            [
                'criteria_name' => 'Land-use Right Tax',
                'criteriaTypeId' => 1, // cost
                'parentId' => 1, // Land cost
                'clientId' => null,
                'criteriaPercent' => 15.00
            ],
            [
                'criteria_name' => 'other charges',
                'criteriaTypeId' => 1, // cost
                'parentId' => 1, // Land cost
                'clientId' => null,
                'criteriaPercent' => 10.00
            ],
            [
                'criteria_name' => 'Sublease duration',
                'criteriaTypeId' => 6, // year
                'parentId' => 1, // Land cost
                'clientId' => null,
                'criteriaPercent' => 20.00
            ],
            [
                'criteria_name' => 'Site connection',
                'criteriaTypeId' => null,
                'parentId' => null,
                'clientId' => null,
                'criteriaPercent' => null
            ],
            [
                'criteria_name' => 'Distance from Seaport',
                'criteriaTypeId' => 2, // distance
                'parentId' => 6, // Site connection
                'clientId' => null,
                'criteriaPercent' => 30.00
            ],
            [
                'criteria_name' => 'Distance from Airport',
                'criteriaTypeId' => 2, // distance
                'parentId' => 6, // Site connection
                'clientId' => null,
                'criteriaPercent' => 25.00
            ],
            [
                'criteria_name' => 'Distance from highways',
                'criteriaTypeId' => 2, // distance
                'parentId' => 6, // Site connection
                'clientId' => null,
                'criteriaPercent' => 20.00
            ],
            [
                'criteria_name' => 'Distance from Big Cities',
                'criteriaTypeId' => 2, // distance
                'parentId' => 6, // Site connection
                'clientId' => null,
                'criteriaPercent' => 15.00
            ],
            [
                'criteria_name' => 'Distance from existing office/factory',
                'criteriaTypeId' => 2, // distance
                'parentId' => 6, // Site connection
                'clientId' => null,
                'criteriaPercent' => 10.00
            ],
            [
                'criteria_name' => 'Geotechnical',
                'criteriaTypeId' => null,
                'parentId' => null,
                'clientId' => null,
                'criteriaPercent' => null
            ]
        ];

        foreach ($criteria as $criterion) {
            Criteria::create($criterion);
        }
    }
}
