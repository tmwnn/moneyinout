<?php

use Illuminate\Database\Seeder;
use App\Models\Category;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(array(
            array('id' => '1','name' => 'Продукты'),
            array('id' => '2','name' => 'Бытовые расходы'),
            array('id' => '3','name' => 'Одежда'),
            array('id' => '4','name' => 'Техника'),
            array('id' => '5','name' => 'Авто'),
            array('id' => '6','name' => 'Развлечения'),
            array('id' => '7','name' => 'Коммунальные платежи'),
            array('id' => '8','name' => 'Животные'),
            array('id' => '9','name' => 'Медицина'),
            array('id' => '10','name' => 'Работа'),
        ));
        DB::table('categories')->insert(array(
            array('id' => '11','name' => 'VPS', 'user_id' => 1),
        ));
        DB::table('categories')->insert(array(
            array('id' => '100','name' => 'Без категории'),
        ));
        DB::table('categories')->where('id',100)->update(['id' => 0]);
        //Factory(Category::class, 20)->create();
    }
}
