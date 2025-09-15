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

        $this->fact($roles['fact'] ?? [],$pre_sql->fact);
        
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
                $this->errors[] = "Table '{$pre_sql->table}' not found in connection struct.";
                continue;
            }
             $cols = array_merge(
                $pre_sql->columns,
                array_keys($pre_sql->filter ?? []),
                array_keys($pre_sql->order ?? [])
            );

            $table = $struct[$pre_sql->table];
            $table_cols = array_keys($table->columns);
            $this->validateField($cols, $table_cols);
            $this->validateTypeField($pre_sql->filter,$table->columns);
        }
    }


    private function fact( array $struct, FactDTO $fact_pre_sql):bool
    {
        return true;
    }

    protected function validateField(array $cols_espec,array $table_cols)
    {
       $missing = array_diff($cols_espec, $table_cols);

        foreach ($missing as $col) {
            $this->errors[] = "Column '{$col}' not found in table.";
        }
    }

    protected function validateTypeField(array $filter, array $table_cols): void
    {
        foreach ($filter as $col => $condition) {
           
            $expectedType = explode(":", $table_cols[$col])[0]; // pega só 'number', 'string', 'date', etc.
            $value = $condition['value'] ?? null;

            if (!$this->checkType($expectedType, $value)) {
                $this->errors[] = "Invalid type for column '{$col}'. Expected {$expectedType}, got " . gettype($value);
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