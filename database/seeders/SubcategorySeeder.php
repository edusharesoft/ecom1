<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $home_gadgets = \DB::table('categories')->where('slug', 'home-gadgets')->value('id');
        $health_beauty = \DB::table('categories')->where('slug', 'health-beauty')->value('id');
        $hot_offer = \DB::table('categories')->where('slug', 'hot-offer')->value('id');
        $kitchen_gadgets = \DB::table('categories')->where('slug', 'kitchen-gadgets')->value('id');
        $security = \DB::table('categories')->where('slug', 'security')->value('id');
        $all_kinds_of_rack = \DB::table('categories')->where('slug', 'all-kinds-of-rack')->value('id');
        $footware = \DB::table('categories')->where('slug', 'footwear')->value('id');
        $cream = \DB::table('categories')->where('slug', 'cream')->value('id');

        // Generate and insert subcategories
        $home_gadgets_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $home_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $home_gadgets, 'status' => 1],            
        ];
        $health_beauty_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $health_beauty, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $health_beauty, 'status' => 1],            
        ];
        $hot_offer_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $hot_offer, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $hot_offer, 'status' => 1],            
        ];
        $kitchen_gadgets_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $kitchen_gadgets, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $kitchen_gadgets, 'status' => 1],            
        ];
        $security_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $security, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $security, 'status' => 1],            
        ];
        $all_kinds_of_rack_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $all_kinds_of_rack, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $all_kinds_of_rack, 'status' => 1],            
        ];
        $footware_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $footware, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $footware, 'status' => 1],            
        ];
        $cream_subs = [
            ['subcategoryName' => 'Subcategory 1', 'slug' => 'subcategory-1', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 2', 'slug' => 'subcategory-2', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 3', 'slug' => 'subcategory-3', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 4', 'slug' => 'subcategory-4', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 5', 'slug' => 'subcategory-5', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 6', 'slug' => 'subcategory-6', 'category_id' => $cream, 'status' => 1],
            ['subcategoryName' => 'Subcategory 7', 'slug' => 'subcategory-7', 'category_id' => $cream, 'status' => 1],            
        ];

        Subcategory::insert($home_gadgets_subs);
        Subcategory::insert($health_beauty_subs);
        Subcategory::insert($hot_offer_subs);
        Subcategory::insert($kitchen_gadgets_subs);
        Subcategory::insert($security_subs);
        Subcategory::insert($all_kinds_of_rack_subs);
        Subcategory::insert($footware_subs);
        Subcategory::insert($cream_subs);
    }
}
