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

    /** @var  Collection */
    protected $units;

    /** @var Collection */
    protected $code_serial;

    public function handle()
    {
        ini_set('memory_limit', '4G');
        $file = storage_path('app/data-cleaning-2.xlsx');
        if (!is_readable($file)) {
            $this->output->error('Cleaning file does not exist');
            return 1;
        }

        $this->excel = \PHPExcel_IOFactory::load($file);
        $this->buildTypes();
        $this->buildResources();
    }

    protected function buildTypes()
    {
        $this->output->block("Updating resource types", 'note');
        $this->output->newLine();

        $start = microtime(1);
        $sheet = $this->excel->getSheetByName('Structure');

        // Clean resource types
        \DB::table('resource_types')->where('parent_id', '!=', 0)->delete();

        // Cache basic types
        $this->types = collect();
        collect(\DB::table('resource_types')->whereParentId(0)->get())->each(function ($type) {
            $this->types->put(strtolower($type->name), (array) $type);
        });

        //Iterate over sheet rows starting from row 2
        $iterator = $sheet->getRowIterator(2);
        foreach ($iterator as $row_num => $row) {
            $cells = $row->getCellIterator('A', 'G');
            $typeDef = ['type' => [], 'discipline' => '', 'code' => ''];
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
                $typeDef['type'][] = trim($cell->getValue());
            }

            //Remove empty values
            $typeDef['type'] = array_filter($typeDef['type'], function($type) {
                return strlen($type);
            });

            $this->buildType($typeDef);
        }

        $this->output->newLine();
        $this->output->block("Completed in: " . round(microtime(1) - $start, 4), 'note');
        $this->output->newLine();
    }

    /**
     * @param $typeDef
     */
    protected function buildType($typeDef)
    {
        $lastIndex = count($typeDef['type']) - 1;
        $path = [];
        $parent = [];
        $code_tokens = explode('.', $typeDef['code']);
        foreach ($typeDef['type'] as $idx => $typeName) {
            $path[] = strtolower(trim($typeName));
            $canonical = implode('.', $path);
            if ($this->types->has($canonical)) {
                $parent = $this->types->get($canonical);
            } else {
                $type = [
                    'name' => $typeName,
                    'code' => implode('.', array_slice($code_tokens, 0, $idx + 1)),
                    'parent_id' => $parent['id'] ?? 0,
                ];

                if ($idx == $lastIndex) {
                    $type['discipline'] = $typeDef['discipline'];
                }

                $type_id = \DB::table('resource_types')->insertGetId($type);
                $type['id'] = $type_id;
                $this->types->put($canonical, $type);
                $parent = $type;
            }
        }
    }

    protected function buildResources()
    {
        $time = microtime(1);

        $this->output->block('Updating resources', 'note');

        $this->units = collect(\DB::table('units')->get(['id', 'type']))->map(function($unit) {
            return ['id' => $unit->id, 'name' => strtolower($unit->type)];
        })->pluck('id', 'name');

        $this->code_serial = collect();

        $sheet = $this->excel->getSheetByName('Resources');

        $bar = $this->output->createProgressBar($sheet->getHighestRow('A') - 1);

        $rows = $sheet->getRowIterator(2);
        $resources = collect();


        foreach ($rows as $row) {
            $cells = $row->getCellIterator('A', 'AE');
            $row = [];
            foreach ($cells as $column => $cell) {
                $row[$column] = $cell->getValue();
            }

            $resources->put($row['A'], $row);
        }

        \DB::beginTransaction();
        $resources->filter(function($row) {
            return $row['W'] == 'Deleted';
        })->each(function($row) use ($bar) {
            $this->handleDeleteResource($row);
            $bar->advance();
        });
        \DB::commit();

        \DB::beginTransaction();
        $counter = 0;
        $resources->reject(function($row) {
            return $row['W'] == 'Deleted';
        })->each(function($row) use ($bar, $counter) {
            $this->handleModifyResource($row);
            $bar->advance();
            if ($bar->getProgressPercent() % 2) {
                \DB::commit();
                \DB::beginTransaction();
            }
        });
        \DB::commit();

        $bar->advance();
        $bar->finish();

        $this->output->newLine(2);

        $this->output->block("Completed in " . round(microtime(1) - $time, 4) . "s", 'note');
    }

    protected function handleDeleteResource($row)
    {
        $id = intval($row['A']);

        if ($row['AE']) {
            //Todo: implement this part
            \DB::table('resources')->where('resource_id', $id)->update(['resource_id' => $row['AE']]);
            \DB::table('std_activity_resources')->where('resource_id', $id)->update(['resource_id' => $row['AE']]);
            \DB::table('breakdown_resources')->where('resource_id', $id)->update(['resource_id' => $row['AE']]);
            \DB::table('break_down_resource_shadows')->where('resource_id', $id)->update(['resource_id' => $row['AE']]);
            \DB::table('master_shadows')->where('resource_id', $id)->update(['resource_id' => $row['AE']]);
        }

        \DB::table('resources')->where('id', $id)->delete();
        \DB::table('resources')->where('resource_id', $id)->delete();
        \DB::table('std_activity_resources')->where('resource_id', $id)->delete();
        \DB::table('breakdown_resources')->where('resource_id', $id)->delete();
        \DB::table('break_down_resource_shadows')->where('resource_id', $id)->delete();
        \DB::table('master_shadows')->where('resource_id', $id)->delete();
    }

    protected function handleModifyResource($row)
    {
        $id = intval(trim($row['A']));
        $name = trim($row['Y']);

        $typeNames = [];
        foreach (['Z', 'AA', 'AB', 'AC', 'AD'] as $c) {
            $typeName = trim($row[$c]);
            if (strlen($typeName)) {
                $typeNames[] = strtolower($typeName);
            }
        }

        $canonicalType = implode('.', $typeNames);
        if (!$this->types->has($canonicalType)) {
            dd($row, $typeNames, $canonicalType);
            $this->output->warning("Cannot find type for resource: [$id] $name");
            return 1;
        }

        $type = $this->types->get($canonicalType);

        $resource_type_id = $type['id'];
        $code_partial = $this->code_serial->get($resource_type_id, 0) + 1;
        $this->code_serial->put($resource_type_id, $code_partial);

        $resource_code = $type['code'] . '.' . sprintf('%03d', $code_partial);
        $discipline = $type['discipline'] ?? '';

        $attributes = compact('name', 'resource_type_id', 'resource_code', 'discipline');
        if ($id) {
            \DB::table('resources')->where('id', $id)->update($attributes);
        } else {
            $attributes['rate'] = $row['F'];
            $attributes['unit'] = $this->units->get(strtolower($row['G']));
            if (!$attributes['unit']) {
                return false;
            }
            $attributes['waste'] = floatval($row['H']);
            $id = \DB::table('resources')->insertGetId($attributes);
            unset($attributes['rate'], $attributes['unit'], $attributes['waste']);
        }

        $related_resource_ids = collect(
            \DB::table('resources')->where('resource_id', $id)->get(['id'])
        )->pluck('id')->toArray();

        if (trim($row['AE'])) {
            $also_include = collect(explode(',', $row['AE']))->filter()->map('trim')->toArray();
            $related_resource_ids = array_unique(array_merge($related_resource_ids, $also_include));
            $attributes['resource_id'] = $id;
        }

        \DB::table('resources')->whereIn('id', $related_resource_ids)->update($attributes);

        $resource_type = $row['Z'];
        $resource_type_id = $this->types->get($typeNames[0])['id'];
        \DB::table('break_down_resource_shadows')
            ->whereIn('resource_id', $related_resource_ids)
            ->update(['resource_name' => $name, 'resource_type' => $resource_type, 'resource_type_id' => $resource_type_id, 'resource_code' => $resource_code]);

        \DB::table('master_shadows')
            ->whereIn('resource_id', $related_resource_ids)
            ->update(['resource_name' => $name, 'resource_type_id' => $resource_type_id, 'resource_code' => $resource_code]);
    }
}
