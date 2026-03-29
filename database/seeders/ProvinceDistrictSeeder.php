<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceDistrictSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'name' => 'Western',       'slug' => 'western',       'districts' => [
                ['name' => 'Colombo',   'slug' => 'colombo'],
                ['name' => 'Gampaha',   'slug' => 'gampaha'],
                ['name' => 'Kalutara',  'slug' => 'kalutara'],
            ]],
            ['id' => 2, 'name' => 'Central',        'slug' => 'central',       'districts' => [
                ['name' => 'Kandy',         'slug' => 'kandy'],
                ['name' => 'Matale',        'slug' => 'matale'],
                ['name' => 'Nuwara Eliya',  'slug' => 'nuwara-eliya'],
            ]],
            ['id' => 3, 'name' => 'Southern',       'slug' => 'southern',      'districts' => [
                ['name' => 'Galle',         'slug' => 'galle'],
                ['name' => 'Matara',        'slug' => 'matara'],
                ['name' => 'Hambantota',    'slug' => 'hambantota'],
            ]],
            ['id' => 4, 'name' => 'Northern',       'slug' => 'northern',      'districts' => [
                ['name' => 'Jaffna',        'slug' => 'jaffna'],
                ['name' => 'Kilinochchi',   'slug' => 'kilinochchi'],
                ['name' => 'Mannar',        'slug' => 'mannar'],
                ['name' => 'Vavuniya',      'slug' => 'vavuniya'],
                ['name' => 'Mullaitivu',    'slug' => 'mullaitivu'],
            ]],
            ['id' => 5, 'name' => 'Eastern',        'slug' => 'eastern',       'districts' => [
                ['name' => 'Trincomalee',   'slug' => 'trincomalee'],
                ['name' => 'Batticaloa',    'slug' => 'batticaloa'],
                ['name' => 'Ampara',        'slug' => 'ampara'],
            ]],
            ['id' => 6, 'name' => 'North Western',  'slug' => 'north-western', 'districts' => [
                ['name' => 'Kurunegala',    'slug' => 'kurunegala'],
                ['name' => 'Puttalam',      'slug' => 'puttalam'],
            ]],
            ['id' => 7, 'name' => 'North Central',  'slug' => 'north-central', 'districts' => [
                ['name' => 'Anuradhapura',  'slug' => 'anuradhapura'],
                ['name' => 'Polonnaruwa',   'slug' => 'polonnaruwa'],
            ]],
            ['id' => 8, 'name' => 'Uva',            'slug' => 'uva',           'districts' => [
                ['name' => 'Badulla',       'slug' => 'badulla'],
                ['name' => 'Monaragala',    'slug' => 'monaragala'],
            ]],
            ['id' => 9, 'name' => 'Sabaragamuwa',   'slug' => 'sabaragamuwa',  'districts' => [
                ['name' => 'Ratnapura',     'slug' => 'ratnapura'],
                ['name' => 'Kegalle',       'slug' => 'kegalle'],
            ]],
        ];

        foreach ($data as $province) {
            DB::table('provinces')->insert([
                'id'   => $province['id'],
                'name' => $province['name'],
                'slug' => $province['slug'],
            ]);

            foreach ($province['districts'] as $district) {
                DB::table('districts')->insert([
                    'province_id' => $province['id'],
                    'name'        => $district['name'],
                    'slug'        => $district['slug'],
                ]);
            }
        }
    }
}