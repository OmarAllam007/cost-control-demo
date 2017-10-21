<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\PHPExcel;

class DataCleaning extends Command
{
    protected $signature = 'data-cleaning';

    protected $description = 'Clean resources data as per business provided Excel';

    /** @var PHPExcel */
    protected $excel;

    /** @var  Collection */
    protected $types;

    public function handle()
    {
//        ini_set('memory_limit', '4G');
        $file = storage_path('app/data-cleaning-2.xlsx');
        if (!is_readable($file)) {
            $this->output->error('Cleaning file does not exist');
            return 1;
        }

        $this->excel = \PHPExcel_IOFactory::load($file);
        $this->buildTypes();
    }

    private function buildTypes()
    {
        $sheet = $this->excel->getSheetByName('Structure');

        // Clean resource types
        \DB::table('resource_types')->where('parent_id', '!=',0)->delete();

        // Cache basic types
        $this->types = collect();
        collect(\DB::table('resource_types')->whereParentId(0)->get())->each(function($type) {
            $this->types->put(strtolower($type->name), $type->id);
        });

        //Iterate over sheet rows starting from row 2
        $iterator = $sheet->getRowIterator(2);
        foreach ($iterator as $row_num => $row) {
            $cells = $row->getCellIterator('A', 'G');
            $typeDef = ['type' => [], 'discipline' => '', 'code'];
            foreach ($cells as $column => $cell) {
                // This column represents type discipline
                if ($column == 'G') {
                    $typeDef['discipline'] = ucfirst(strtolower($cell->getValue()));
                    continue;
                }

                // This column represents the code
                if ($column == 'A') {
                    $typeDef['code'] = $cell->getValue();
                    continue;
                }

                // Collect type path so we can use in the tree later
                $typeDef['type'][] = $cell->getValue();
            }

            //Remove empty values
            $typeDef['type'] = array_filter($typeDef['type']);

            $this->buildType($typeDef);
        }
    }

    /**
     * @param $typeDef
     */
    private function buildType($typeDef)
    {
        $lastIndex = count($typeDef['type']) - 1;
        $path = [];
        $type_id = 0;
        $code_tokens = explode('.', $typeDef['code']);
        foreach ($typeDef['type'] as $idx => $typeName) {
            $path[] = strtolower($typeName);
            $canonical = implode('.', $path);
            if ($this->types->has($canonical)) {
                $type_id = $this->types->get($canonical);
            } else {
                $type = [
                    'name' => $typeName,
                    'code' => implode('.', array_slice($code_tokens, 0, $idx + 1)),
                    'parent_id' => $type_id,
                ];

                if ($idx == $lastIndex) {
                    $type['discipline'] = $typeDef['discipline'];
                }

                $type_id = \DB::table('resource_types')->insertGetId($type);
                $this->types->put($canonical, $type_id);
            }
        }
    }
}
