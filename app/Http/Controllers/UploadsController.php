<?php

namespace App\Http\Controllers;

use App\Equipment;
use App\OfferGroup;
use Illuminate\Support\Facades\DB;
use App\Type;
use Illuminate\Support\Facades\Input;
use PHPExcel_IOFactory;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class UploadsController extends Controller
{

    public function excelUploadEquipment()
    {
        $inputFileName = Input::file('file')->getPathname();

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($inputFileName);

        $excelData = [];
        for ($i = 0; $i < $objPHPExcel->getSheetCount(); $i++){
            $objPHPExcel->setActiveSheetIndex($i);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            //now do whatever you want with the active sheet
            foreach( $objWorksheet->getRowIterator() as $key => $row ){
                foreach( $row->getCellIterator() as $cell ){
                    $excelData[$i][$key][] = $cell->getCalculatedValue();
                }
            }
        }

        foreach ($excelData as $sheet) {
            $typeNameFromSheet = $sheet[3][1];

            $consumables = [
                'кабели и провода',
                'расходники',
                'монтажные и расходные материалы',
            ];

            if(in_array(mb_strtolower($typeNameFromSheet), $consumables)) {
                $type = Type::where('slug', 'rashodnye-materialy')->first();
            } else {
                $type = Type::where('name', $typeNameFromSheet)->first();
            }


            if(!$type) {
                $type = new Type([
                    'name' => $typeNameFromSheet,
                    'class' => 'equipment',
                ]);
                $type->save();
            }

            foreach (array_slice($sheet, 6) as $row) {
                if(
                    !empty($row[2])
                    && !empty($row[3])
                    && !empty($row[5])
                    && !empty($row[6])
                    && !empty($row[7])
                    && !empty($row[8])
                ) {
                    if(DB::table('equipment')->where('code', $row[2])->exists()) {
                        $toUpdate = [
                            'type_id' => $type->id,
                            'name' => $row[3],
                            'description' => $row[4],
                            'points' => $row[5],
                            'price_trade' => $row[6],
                            'price_small_trade' => $row[7],
                            'price_special' => $row[8],
                            'price' => 0,
                            'class' => $type->class,
                        ];
                        DB::table('equipment')->where('code', $row[2])->update($toUpdate);
                    } else {
                        $toInsert = [
                            'type_id' => $type->id,
                            'code' => $row[2],
                            'name' => $row[3],
                            'description' => $row[4],
                            'points' => $row[5],
                            'price_trade' => $row[6],
                            'price_small_trade' => $row[7],
                            'price_special' => $row[8],
                            'price' => 0,
                            'class' => $type->class,
                            'slug' => SlugService::createSlug(OfferGroup::class, 'slug', $row[3])
                        ];
                        DB::table('equipment')->insert($toInsert);
                    }
                }
            }
        }

        return redirect()->back();
    }
}
