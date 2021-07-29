<?php

use App\Models\Operation;
use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Services\Operations\OperationsService;

class OperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(OperationsService $operationsService)
    {
        $fake = 0;
        $fromDate = '2011-10-01';

        $this->info("== import data (fake = {$fake}, fromDate = {$fromDate}) ==");
        $this->info(__DIR__);

        $this->info('== import incomes ==');
        $handle = fopen(__DIR__ . '/price.csv', "r");
        while ($csvLine = fgetcsv($handle, 1000, ";")) {
            if ($csvLine[1] >= $fromDate) {
                //$csvLine[3] = iconv("cp1251", "UTF-8", $csvLine[3]);
                $date = $csvLine[1];
                $summ = $csvLine[2];
                $comment = trim($csvLine[3]);
                $category_id = 0;
                if (preg_match('/cbonds/', $comment)) {
                    $category_id = 10;
                }
                if ($comment == 'aviasales' || preg_match('/google/', $comment)) {
                    $category_id = 11;
                }

                $item = [
                    'date' => $date,
                    'summ' => $summ,
                    'category_id' => $category_id,
                    'comment' => $comment,
                    'user_id' => 1,
                ];
                $operationsService->store($item);
            }
        }
        fclose($handle);

        $this->info('== import outcomes ==');
        $handle = fopen(__DIR__ . '/costs.csv', "r");
        while ($csvLine = fgetcsv($handle, 1000, ";")) {
            if ($csvLine[4] >= $fromDate) {
                //$this->info(implode(' ', $csvLine));
                //$csvLine[1] = iconv("cp1251", "UTF-8", $csvLine[1]);
                $date = $csvLine[4];
                $summ = $csvLine[2] * -1;
                $comment = trim($csvLine[1]);
                $type = $csvLine[3];
                $category_id = 0;
                $category_name = '';


                $cat = Category::where('name', $comment)->first();
                if (!empty($cat->id)) {
                    $category_id = $cat->id;
                    $category_name = $comment;
                    $comment = '';
                }
                if (preg_match('/(.+):(.+)/', $comment, $tmpArr)) {
                    $cat = Category::where('name', $tmpArr[1])->first();
                    if (!empty($cat->id)) {
                        $category_id = $cat->id;
                        $category_name = $tmpArr[1];
                        $comment = trim($tmpArr[2]);
                    } else {
                        DB::table('categories')->insert(array(
                            ['name' => $tmpArr[1], 'user_id' => 1],
                        ));
                        $category_name = $tmpArr[1];
                        $category_id = Category::where('name', $tmpArr[1])->first()->id;
                        $comment = trim($tmpArr[2]);
                    }
                }
                $tags = '';
                if (preg_match('/(.*)\((.+)\)/' , $comment, $tmpArrT)) {
                    $comment = trim($tmpArrT[1]);
                    $tags = '#' . trim($tmpArrT[2]);
                }
                if (preg_match('/(.*)(wildberries|bonprix)/', $comment, $tmpArrT)) {
                    $comment = trim($tmpArrT[1]);
                    $tags = '#' . trim($tmpArrT[2]);
                }

                $item = [
                    'date' => $date,
                    'summ' => $summ,
                    'category_id' => $category_id,
                    'comment' => $comment,
                    'tags' => $tags,
                    'search' => "{$category_name}: {$comment} {$tags}",
                    'type' => $type,
                    'user_id' => 1,
                ];
                //dump($item);
                $operationsService->store($item);
            }
        }

    }

    public function info($str) {
        echo "$str\n";
    }
}
