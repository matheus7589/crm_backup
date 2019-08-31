<?php 
//echo $service;

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

$this->load->model('tickets_model');

$servicos = $this->tickets_model->getSegundosServicos((int)$service);
$servicos = array_sort($servicos, "name", SORT_ASC);
foreach($servicos as $key => $a){
    if($a['name'] == 'ND'){
        //$aux = $a;
        unset($servicos[$key]);
        array_unshift($servicos, $a);      
    }
}
//print_r($servicos);
//echo $servicos["name"];
$result = '<option></option>';
foreach($servicos as $servico){
    //echo $servico["name"];
    $result = $result . '<option value=' . $servico["secondServiceid"] . '>' . $servico["name"] . '</option>';
}
//print_r($servicos);
echo $result;

?>



