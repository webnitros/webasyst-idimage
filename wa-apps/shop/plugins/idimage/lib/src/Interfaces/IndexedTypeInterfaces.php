<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 11:41
 */

namespace IdImage\Interfaces;


use idImage;
use IdImage\Ai\CategoryTree;
use IdImage\Ai\ProductIndexer;
use IdImage\Ai\Similar;

interface IndexedTypeInterfaces
{
    public function build(ProductIndexer $productIndexer, CategoryTree $categoryTree): ProductIndexer;

    public function process(idImage $idImage): ProductIndexer;

    public function comparison(Similar $similar, ProductIndexer $productIndexer): Similar;

}
