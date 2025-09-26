<?php
namespace App\Modules\Querry\Services;

use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;

class ValidatePreSqlService
{
    private array $errors = [];
    private array $stack_aliases = [];


    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
    * @param array<string, array<string,TableDTO[]>> $roles
    * @param PreSqlDTO $pre_sql
    */
    public function compare(array $roles,PreSqlDTO $pre_sql): ValidatePreSqlService
    { 
        $this->dimensions($roles['dimension'] ?? [],$pre_sql->dimensions);
        
        $this->dimensions($roles['sub-dimension'] ?? [],$pre_sql->subDimensions);

        $this->fact(array_values($roles['fact'])[0] ?? [],$pre_sql->fact);
        
        return $this;
    }

    /**
     * @param array<string, TableDTO> $struct
     * @param  array<DimensionDTO> $dimensions_pre_sql
     * 
    */
    private function dimensions( array $struct, array $dimensions_pre_sql)
    {
        foreach ($dimensions_pre_sql as $pre_sql) {

            if (!isset($struct[$pre_sql->table])) {
                $this->errors["dimension-{$pre_sql->table}"][] = "Table '{$pre_sql->table}' not found in connection struct.";
                continue;
            }

            $table = $struct[$pre_sql->table];
        
            $this->validateTypeField($table,$pre_sql->filter);
   
        }
    }


    private function fact( TableDTO $struct, FactDTO $fact_pre_sql)
    {
        $cols_spect = array_keys($fact_pre_sql->columns);

        if(!$fact_pre_sql->limit)
            $this->errors["fact"]['table'][] = "Limit to Querry is not Found";

        foreach ($cols_spect as $col_spec) {
            
            if(!array_key_exists($col_spec,$struct->columns))
            {
                $this->errors["fact"]['table'][] = "Column '{$col_spec}' not found in fact table.";
                continue;
            }

            $col = $struct->columns[$col_spec];
            $fact_cols_pre_sql = $fact_pre_sql->columns[$col_spec];
            $actions = array_merge(
                $fact_cols_pre_sql->aggregates ?? [],
                $fact_cols_pre_sql->linear ?? []
            );
            
            $this->validateColumnActions( $col,$actions,$col_spec);
           ;
        }
    }

    public function validateColumnActions(string $col_definition, array $actions, string $col_name): void
    {
        $parts = explode(":", $col_definition);
        $col_type = $parts[0];

        $type_ops = [
            'number' => ['sum','avg','min','max','gt','lt','gt-eq','lt-eq','eq','count',':range',':list','df'],
            'string' => ['eq','count','df'],
            'date'   => ['eq','count',':range',':list'],
            'datetime' => ['eq','count',':range',':list'],
            'time' =>  ['eq','count',':range',':list'],
            'bool' => ['eq','df',':list']
        ];

        if (!isset($type_ops[$col_type])) {
            $this->errors['fact']['column'][$col_name][] = "Unknown column type '{$col_type}' for column '{$col_name}'.";
            return;
        }

        $type_ops_for_col = $type_ops[$col_type] ?? [];

        foreach ($actions as $act) {
            if (!in_array($act, $type_ops_for_col)) {
                $this->errors['fact']['column'][$col_name][] = "Action '{$act}' cannot be applied to column '{$col_name}' of type '{$col_type}'.";
            } 
            
        }
    }

    protected function validateTypeField( TableDTO $table, array $filter): void
    {
        foreach ($filter as $col => $condition) {

            if(!array_key_exists($col,$table->columns))
            {
                $this->errors['dimensions']['table'][$table->name][] = "Column '{$col}' not found in table in {$table->name} table.";
                continue;
            }

            $col_table = $table->columns[$col];

            $expected_type = explode(":", $col_table)[0]; // pega só 'number', 'string', 'date', etc.
            $value = $condition['value'] ?? null;

            if (!$this->checkType($expected_type, $value))
                $this->errors['dimensions']['table'][$table->name][] = "Invalid type for column '{$col}'. Expected {$expected_type}, got " . gettype($value);
        }
    }

    private function checkType(string $expected, mixed $value): bool
    {
        return match($expected) {
            'number'   => is_numeric($value),
            'string'   => is_string($value),
            'date' => strtotime($value) !== false,
            'datetime' => strtotime($value) !== false,
            'time'     => preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string)$value),
            'bool'     => is_bool($value) || $value === 0 || $value === 1,
            default    => false // nada passa
        };
    }


}