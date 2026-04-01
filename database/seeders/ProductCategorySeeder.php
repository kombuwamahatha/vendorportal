<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Gourmet', 'children' => [
                ['name' => 'Spices & Seasonings', 'children' => [
                    'Cinnamon (Sticks / Powder / Oil)', 'Pepper (Black / White)',
                    'Cardamom & Cloves', 'Curry Powders (Roasted / Raw)', 'Spice Gift Sets',
                ]],
                ['name' => 'Tea Collection', 'children' => [
                    'Black Tea (High / Mid / Low Grown)', 'Green Tea', 'White Tea',
                    'Herbal Tea', 'Iced Tea Blends', 'Tea Gift Boxes',
                ]],
                ['name' => 'Traditional Foods', 'children' => [
                    'Kithul Treacle & Jaggery', 'Traditional Sweets (Kavum Mix, Aluwa Mix)',
                    'Sambols & Chutneys', 'Achcharu & Pickles',
                ]],
                ['name' => 'Superfoods', 'children' => [
                    'Moringa Powder', 'Turmeric Powder', 'Jackfruit Flour',
                    'Wood Apple Powder', 'Gotukola Powder',
                ]],
                ['name' => 'Coconut Products', 'children' => [
                    'Virgin Coconut Oil', 'Coconut Flour', 'Coconut Sugar', 'Coconut Milk Powder',
                ]],
            ]],
            ['name' => 'Wellness & Ayurveda', 'children' => [
                ['name' => 'Herbal Wellness', 'children' => [
                    'Immunity Blends', 'Detox Teas', 'Relaxation & Sleep Teas',
                ]],
                ['name' => 'Ayurvedic Products', 'children' => [
                    'Herbal Oils', 'Balms & Liniments', 'Medicinal Powders',
                ]],
                ['name' => 'Natural Beauty', 'children' => [
                    'Sandalwood Powder', 'Face Packs', 'Herbal Soaps',
                    'Essential Oils', 'Clay Masks',
                ]],
                ['name' => 'Spa & Gift Sets', 'children' => [
                    'Ayurveda Starter Kits', 'Spa Hampers',
                ]],
            ]],
            ['name' => 'Home & Living', 'children' => [
                ['name' => 'Wood & Carvings', 'children' => [
                    'Masks (Raksha / Kolam)', 'Buddha Sculptures', 'Decorative Panels',
                ]],
                ['name' => 'Paintings', 'children' => [
                    'Cultural Paintings', 'Nature & Wildlife', 'Religious Art',
                    'Contemporary Art', 'Batik Paintings',
                ]],
                ['name' => 'Clay & Terracotta', 'children' => [
                    'Clay Pots', 'Terracotta Décor', 'Handmade Tableware',
                ]],
                ['name' => 'Metal & Brassware', 'children' => [
                    'Oil Lamps', 'Bowls', 'Traditional Utensils',
                ]],
                ['name' => 'Natural Fiber', 'children' => [
                    'Reed & Rattan', 'Coir Products', 'Palmyrah Crafts', 'Coconut Shell Crafts',
                ]],
                ['name' => 'Kitchen & Dining', 'children' => [
                    'Wooden Spoons', 'Spice Boxes', 'Serving Boards',
                ]],
            ]],
            ['name' => 'Apparel & Textiles', 'children' => [
                ['name' => 'Women', 'children' => [
                    'Batik Dresses', 'Sarees', 'Handloom Blouses', 'Resort Wear',
                ]],
                ['name' => 'Men', 'children' => [
                    'Batik Shirts', 'Sarongs', 'Linen Shirts',
                ]],
                ['name' => 'Accessories', 'children' => [
                    'Handwoven Scarves', 'Shawls', 'Eco Bags', 'Handloom Bags',
                ]],
                ['name' => 'Home Textiles', 'children' => [
                    'Cushion Covers', 'Table Runners', 'Bed Linen',
                ]],
            ]],
            ['name' => 'Jewelry & Gemstones', 'children' => [
                ['name' => 'Fine Jewelry', 'children' => [
                    'Sapphire Jewelry', 'Ruby Jewelry', 'Semi-Precious Stones',
                ]],
                ['name' => 'Artisan Jewelry', 'children' => [
                    'Silver Handmade Pieces', 'Brass Jewelry', 'Beaded Jewelry',
                ]],
                ['name' => 'Cultural Designs', 'children' => [
                    'Kandyan Style Jewelry', 'Temple Jewelry Inspired Pieces',
                ]],
            ]],
        ];

        foreach ($categories as $sort1 => $cat1) {
            $id1 = DB::table('product_categories')->insertGetId([
                'name'       => $cat1['name'],
                'slug'       => Str::slug($cat1['name']),
                'parent_id'  => null,
                'level'      => 1,
                'sort_order' => $sort1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cat1['children'] as $sort2 => $cat2) {
                $id2 = DB::table('product_categories')->insertGetId([
                    'name'       => $cat2['name'],
                    'slug'       => Str::slug($cat2['name']),
                    'parent_id'  => $id1,
                    'level'      => 2,
                    'sort_order' => $sort2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($cat2['children'] as $sort3 => $cat3) {
                    DB::table('product_categories')->insert([
                        'name'       => $cat3,
                        'slug'       => Str::slug($cat3),
                        'parent_id'  => $id2,
                        'level'      => 3,
                        'sort_order' => $sort3,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}