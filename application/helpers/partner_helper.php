<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 09/12/2017
 * Time: 11:18
 */

function permission_partner()
{

    if(is_partner()){
        access_denied('Tasks');
    }

}