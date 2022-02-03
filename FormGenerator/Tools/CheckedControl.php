<?php
/**
 * @author selcukmart
 * 3.02.2021
 * 13:14
 */

namespace FormGenerator\Tools;


class CheckedControl
{
    private
        $checked = false,
        $row_table = null,
        $control_array,
        $checkeds = [],
        $ckeck_id = 0;

    public function __construct(array $control_array, $row_table = null)
    {
        $this->row_table = $row_table;
        $this->control_array = $control_array;
    }

    public function control($id): bool
    {
        if (empty($id)) {
            $this->checked = false;
            return $this->checked;
        }

        if (isset($this->checkeds[$id])) {
            $this->checked = $this->checked[$id];
            return $this->checked;
        }
        $this->checked = false;
        if (!is_null($this->row_table)) {
            $this->ckeck_id = $id;
            $from = $this->control_array['from'];
            $this->checkeds[$id] = $this->{$from}();
        }elseif(isset($this->control_array['from'])){
            $from = $this->control_array['from'];
            $this->checkeds[$id] = $this->{$from}($id);
        }
        return $this->checked;
    }

    private function key_value_array($id){
        if(in_array($id, $this->control_array['key_value_array'], true)){
            $this->checked = true;
            return $id;
        }
    }

    private function array2sql()
    {
        $this->control_array['array2sql'][$this->control_array['parameters']['this_field']] = $this->row_table['id'];
        $this->control_array['array2sql'][$this->control_array['parameters']['foreign_field']] = $this->ckeck_id;
        $this->checked = (bool)DB::rowCount(DB::select($this->control_array['array2sql'], $this->control_array['parameters']['table'], 1, false));
        return $this->checked;
    }

    private function sql()
    {
        $sql = $this->control_array['sql'];
        $this_field = $this->control_array['parameters']['this_field'];
        $foreign_field = $this->control_array['parameters']['foreign_field'];
        $sql .= " AND " . $this_field . "='" . $this->row_table['id'] . "' AND " . $foreign_field . "=" . $this->ckeck_id . " LIMIT 1";
        $this->checked = (bool)DB::rowCount(DB::query($sql));
        return $this->checked;
    }

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function __destruct()
    {

    }
}