<?
namespace App\Modules\Querry\Services;

use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;

class ValidatePreSqlService
{
    private array $errors = [];

    /**
    * @param array<string, TableDTO[]> $roles
    * @param PreSqlDTO $pre_sql
    */
    public function compare(array $roles,PreSqlDTO $pre_sql): ValidatePreSqlService
    { 
        $this->dimensions($roles['dimension'] ?? [],$pre_sql->dimensions);
        
        $this->dimensions($roles['sub-dimension'] ?? [],$pre_sql->subDimensions);

        $this->fact(array_values($roles['fact'])[0] ?? [],$pre_sql->fact);
        
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
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
            $this->errors["fact"][] = "Limit to Querry is not Found";

        foreach ($cols_spect as $col_spec) {
            
            if(!array_key_exists($col_spec,$struct->columns))
            {
                $this->errors["fact"][] = "Column '{$col_spec}' not found in fact table.";
                continue;
            }

            $col = $struct->columns[$col_spec];
            $op = $fact_pre_sql->columns[$col_spec]->aggregates;
            
            $this->validateFieldAgregations($col_spec, $col,$op);
        }
    }

    protected function validateTypeField( TableDTO $table, array $filter): void
    {
        foreach ($filter as $col => $condition) {

            if(!array_key_exists($col,$table->columns))
            {
                $this->errors[$table->name][] = "Column '{$col}' not found in table in {$table->name} table.";
                continue;
            }

            $col_table = $table->columns[$col];

            $expectedType = explode(":", $col_table)[0]; // pega só 'number', 'string', 'date', etc.
            $value = $condition['value'] ?? null;

            if (!$this->checkType($expectedType, $value))
                $this->errors[$table->name][] = "Invalid type for column '{$col}'. Expected {$expectedType}, got " . gettype($value);
            
        }
    }

    protected function validateFieldAgregations(string $colName, string $colDefinition, array $aggregates): void
    {
        $parts = explode(":", $colDefinition);
        $colType = $parts[0]; // tipo real da coluna

        foreach ($aggregates as $agg) {
            if (in_array($agg, ['count', ':list'])) continue;
            if (in_array($agg, ['sum','avg']) && $colType !== 'number') {
                $this->errors['fact'][] = "Agregate '{$agg}' cannot be applied to column '{$colName}' of type '{$colType}'.";
            }
        }
    }


    private function checkType(string $expected, mixed $value): bool
    {
        return match($expected) {
            'number'   => is_numeric($value),
            'string'   => is_string($value),
            'date',
            'datetime' => strtotime($value) !== false,
            'time'     => preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string)$value),
            'bool'     => is_bool($value) || $value === 0 || $value === 1,
            default    => false // fallback, deixa passar
        };
    }

}