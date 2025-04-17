<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use idImage;
use IdImage\Ai\CategoryTree;
use IdImage\Ai\ComparisonCosineSimilarity;
use IdImage\Ai\ProductIndexer;
use IdImage\Ai\Similar;

abstract class IndexedTypeAbstract
{
    private ProductIndexer $productIndexer;
    protected CategoryTree $categoryTree;
    protected ComparisonCosineSimilarity $comparisonCosineSimilarity;

    public function __construct(?ProductIndexer $productIndexer = null, ?CategoryTree $categoryTree = null)
    {
        $this->productIndexer = $productIndexer ?? new ProductIndexer();
        $this->categoryTree = $categoryTree ?? new CategoryTree();
        $this->comparisonCosineSimilarity = new ComparisonCosineSimilarity();
    }

    public function process(idImage $idImage): ProductIndexer
    {
        $this->productIndexer->build($idImage);
        $this->categoryTree->build($idImage);

        return $this->build($this->productIndexer, $this->categoryTree);
    }


    public function getSecondLevelParent( $childId)
    {
        // Создаем карту категорий для быстрого поиска
        $categories = $this->categoryTree->all();
        // Создаем карту категорий для быстрого поиска
        $categoryMap = [];

        foreach ($categories as $category) {
            $categoryMap[$category['id']] = [
                'id' => $category['id'],
                'parent' => $category['parent'],
                'children' => $category['children'] ?? []
            ];

            // Добавляем дочерние категории в карту
            if (!empty($category['children'])) {
                $this->mapChildren($category['children'], $categoryMap);
            }
        }

        // Проверяем, существует ли искомая категория
        if (!isset($categoryMap[$childId])) {
            return null; // Категория не найдена
        }

        // Поднимаемся вверх по дереву
        while (isset($categoryMap[$childId])) {
            $parentId = $categoryMap[$childId]['parent'];

            // Если родитель не существует в карте категорий, прерываем
            if (!isset($categoryMap[$parentId])) {
                return null;
            }

            // Если родитель - категория первого уровня (parent = 0), значит, текущая категория была второго уровня
            if ($categoryMap[$parentId]['parent'] == 0) {
                return $categoryMap[$childId]; // Возвращаем родителя второго уровня
            }

            $childId = $parentId; // Поднимаемся на уровень выше
        }

        return null; // Если ничего не найдено
    }

// Вспомогательная функция для рекурсивного добавления вложенных категорий в карту
    private function mapChildren(array $children, array &$categoryMap)
    {
        foreach ($children as $child) {
            $categoryMap[$child['id']] = [
                'id' => $child['id'],
                'parent' => $child['parent'],
                'children' => $child['children'] ?? []
            ];

            if (!empty($child['children'])) {
                $this->mapChildren($child['children'], $categoryMap);
            }
        }
    }
}
