<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 11:41
 */

namespace IdImage\Interfaces;


interface ActionInterfaces
{
    /**
     * Create action getList
     * @param  callable  $callback
     * @return array
     */
    public function getList($callback);
}
