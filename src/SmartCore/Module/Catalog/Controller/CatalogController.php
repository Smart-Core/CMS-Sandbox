<?php

namespace SmartCore\Module\Catalog\Controller;

use SmartCore\Bundle\CMSBundle\Module\NodeTrait;
use SmartCore\Bundle\UnicatBundle\Model\CategoryModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CatalogController extends Controller
{
    use NodeTrait;

    /**
     * @var int
     */
    protected $repository_id;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->categoryAction();
    }

    /**
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($slug = null)
    {
        if (null === $this->repository_id) {
            return new Response('Module Catalog not yet configured. Node: ' . $this->node->getId() . '<br />');
        }

        $urm = $this->get('unicat')->getRepositoryManager($this->repository_id);

        $requestedCategories = $urm->findCategoriesBySlug($slug);

        foreach ($requestedCategories as $category) {
            $this->get('cms.breadcrumbs')->add($this->generateUrl('smart_module.catalog.category', ['slug' => $category->getSlugFull()]) . '/', $category->getTitle());
        }

        $lastCategory = end($requestedCategories);

        if ($lastCategory instanceof CategoryModel) {
            $this->get('html')->setMetas($lastCategory->getMeta());
            $childenCategories = $urm->getCategoryRepository()->findBy([
                'is_enabled' => true,
                'parent'     => $lastCategory,
                'structure'  => $urm->getDefaultStructure(),
            ]);
        } else {
            $childenCategories = $urm->getCategoryRepository()->findBy([
                'is_enabled' => true,
                'parent'     => null,
                'structure'  => $urm->getDefaultStructure(),
            ]);
        }

        return $this->render('CatalogModule::items.html.twig', [
            'category'          => $lastCategory,
            'childenCategories' => $childenCategories,
            'items'             => $lastCategory ? $urm->findItemsInCategory($lastCategory) : null,
        ]);
    }

    /**
     * @param string $slug
     * @param string $itemSlug
     * @return Response
     */
    public function itemAction($slug, $itemSlug)
    {
        if (null === $this->repository_id) {
            return new Response('Module Catalog not yet configured. Node: ' . $this->node->getId() . '<br />');
        }

        $urm = $this->get('unicat')->getRepositoryManager($this->repository_id);

        $requestedCategories = $urm->findCategoriesBySlug($slug);

        foreach ($requestedCategories as $category) {
            $this->get('cms.breadcrumbs')->add($this->generateUrl('smart_module.catalog.category', ['slug' => $category->getSlugFull()]) . '/', $category->getTitle());
        }

        $lastCategory = end($requestedCategories);

        if ($lastCategory instanceof CategoryModel) {
            $childenCategories = $urm->getCategoryRepository()->findBy([
                'is_enabled' => true,
                'parent'     => $lastCategory,
                'structure'  => $urm->getDefaultStructure(),
            ]);
        } else {
            $childenCategories = $urm->getCategoryRepository()->findBy([
                'is_enabled' => true,
                'parent'     => null,
                'structure'  => $urm->getDefaultStructure(),
            ]);
        }

        $item = $urm->findItem($itemSlug);

        if (empty($item)) {
            throw $this->createNotFoundException();
        }

        $this->get('html')->setMetas($item->getMeta());

        $this->get('cms.breadcrumbs')->add($this->generateUrl('smart_module.catalog.item', [
                'slug' => $lastCategory->getSlugFull(),
                'itemSlug' => $item->getSlug(),
            ]) . '/', $item->getProperty('title'));

        $this->node->setFrontControls([
            'edit' => [
                'title'   => 'Редактировать',
                'uri'     => $this->generateUrl('smart_module.catalog_item_edit_admin', ['repository' => $urm->getRepository()->getName(), 'id' => $item->getId() ]),
                'default' => true,
            ],
        ]);

        return $this->render('CatalogModule::item.html.twig', [
            'category'          => $lastCategory,
            'childenCategories' => $childenCategories,
            'item'              => $item,
        ]);
    }
}
