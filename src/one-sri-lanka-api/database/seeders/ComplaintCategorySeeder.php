<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Customer Service',
                'slug' => 'customer-service',
                'parent_id' => null,
                'category_type' => 'general',
                'description' => 'Issues related to customer service and support',
                'status' => 'active'
            ],
            [
                'name' => 'Technical Issues',
                'slug' => 'technical-issues',
                'parent_id' => null,
                'category_type' => 'technical',
                'description' => 'Technical problems and system issues',
                'status' => 'active'
            ],
            [
                'name' => 'Billing',
                'slug' => 'billing',
                'parent_id' => null,
                'category_type' => 'financial',
                'description' => 'Billing and payment related issues',
                'status' => 'active'
            ],
            [
                'name' => 'Product Quality',
                'slug' => 'product-quality',
                'parent_id' => null,
                'category_type' => 'quality',
                'description' => 'Issues with product quality and defects',
                'status' => 'active'
            ],
            [
                'name' => 'Delivery',
                'slug' => 'delivery',
                'parent_id' => null,
                'category_type' => 'logistics',
                'description' => 'Delivery and shipping related complaints',
                'status' => 'active'
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\ComplaintCategory::create($category);
        }
    }
}
