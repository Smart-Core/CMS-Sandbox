<?php

namespace SmartCore\Bundle\MediaBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SmartCore\Bundle\MediaBundle\Entity\Category;
use SmartCore\Bundle\MediaBundle\Entity\Collection;
use SmartCore\Bundle\MediaBundle\Entity\File;
use SmartCore\Bundle\MediaBundle\Entity\FileTransformed;
use SmartCore\Bundle\MediaBundle\Provider\LocalProvider;
use SmartCore\Bundle\MediaBundle\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CollectionService
{
    use ContainerAwareTrait;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $collectionsRepo;

    /**
     * @var EntityRepository
     */
    protected $filesRepo;

    /**
     * @var EntityRepository
     */
    protected $filesTransformedRepo;

    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @param ContainerInterface $container
     * @param int $id
     */
    public function __construct(ContainerInterface $container, $id)
    {
        $this->em               = $container->get('doctrine.orm.entity_manager');
        $this->collectionsRepo  = $this->em->getRepository('SmartMediaBundle:Collection');
        $this->collection       = $this->collectionsRepo->find($id);
        $this->filesRepo        = $this->em->getRepository('SmartMediaBundle:File');
        $this->filesTransformedRepo = $this->em->getRepository('SmartMediaBundle:FileTransformed');

        // @todo разные провайдеры.
        $this->provider = new LocalProvider($container);
    }

    /**
     * Получить ссылку на файл.
     *
     * @param integer $id
     * @param string|null $filter
     * @return string|null
     */
    public function get($id, $filter = null)
    {
        return $this->provider->get($id, $filter);
    }

    /**
     * @param UploadedFile $file
     * @param Category|int $category
     * @param array $tags
     * @return int - ID файла в коллекции.
     */
    public function upload(UploadedFile $uploadedFile, $category = null, array $tags = null)
    {
        $file = new File();
        $file
            ->setCollection($this->collection)
            ->setFile($uploadedFile)
        ;

        $newFile = $this->provider->upload($file);

        $this->em->persist($file);
        $this->em->flush($file);

        return $file->getId();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        if (empty($id)) {
            return null;
        }

        $this->provider->remove($id);

        $file = $this->filesRepo->find($id);

        $this->em->remove($file);
        $this->em->flush($file);
    }

    /**
     * Получить список файлов.
     *
     * @param int|null $categoryId
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return File[]|null
     */
    public function findBy($categoryId = null, array $orderBy = null, $limit = null, $offset = null)
    {
        // @todo
    }
}