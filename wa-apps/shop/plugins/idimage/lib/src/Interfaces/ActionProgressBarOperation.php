<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 11:41
 */

namespace IdImage\Interfaces;

use IdImage\Support\xPDOQueryIdImage;

interface ActionProgressBarOperation
{
    public function operation(): string;

    public function criteria(xPDOQueryIdImage $query): void;
}
