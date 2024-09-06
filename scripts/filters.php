<?php
class Filter
{
    private $conexion;
    private $table;
    private $filters;

    public function __construct($conexion, $table, $filters = [])
    {
        $this->conexion = $conexion;
        $this->table = $table;
        $this->filters = $filters;
    }

    public function applyFilters()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";

        foreach ($this->filters as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $sql .= " AND (";
                    foreach ($value as $index => $subvalue) {
                        if ($index > 0) $sql .= " OR ";
                        $sql .= "$key LIKE '%$subvalue%'";
                    }
                    $sql .= ")";
                } else {
                    $sql .= " AND $key LIKE '%$value%'";
                }
            }
        }

        return $this->conexion->query($sql);
    }
}
