<?php

use App\Models\Operation;
use Illuminate\Database\Seeder;

class OperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('operations')->insert(array(
            array('id' => '1','date' => '2020-05-31', 'summ' => 1000, 'comment' => 'Магнит', 'search' => 'Продукты: Магнит', 'category_id' => 1, 'user_id' => 1),
            array('id' => '2','date' => '2020-06-01', 'summ' => 900, 'comment' => 'Тусовка на реп. точке', 'search' => 'Развлечения: Тусовка на реп. точке', 'category_id' => 6, 'user_id' => 1),
            array('id' => '3','date' => '2020-06-01', 'summ' => 12000, 'comment' => 'Кредит', 'search' => 'Авто: кредит', 'category_id' => 5, 'user_id' => 1),
        ));
        Factory(Operation::class, 50)->create();
    }
}
