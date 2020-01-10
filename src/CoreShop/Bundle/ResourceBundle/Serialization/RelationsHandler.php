<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class RelationsHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param array|\Traversable       $relation
     * @param array                    $type
     * @param Context                  $context
     *
     * @return array
     */
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        $manager = $this->manager;

        if ($context->hasAttribute('em') && $context->getAttribute('em') instanceof EntityManagerInterface) {
            $manager = $context->getAttribute('em');
        }

        if (is_array($relation)) {
            return array_map(function ($rel) use ($manager) {
                return $this->getSingleEntityRelation($rel, $manager);
            }, $relation);
        }

        return $this->getSingleEntityRelation($relation, $manager);
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param array                      $relation
     * @param array                      $type
     * @param Context                    $context
     *
     * @return array|object
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        $manager = $this->manager;

        if ($context->hasAttribute('em') && $context->getAttribute('em') instanceof EntityManagerInterface) {
            $manager = $context->getAttribute('em');
        }

        $metadata = $manager->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->findById($relation, $metadata, $manager);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->findById($relation, $metadata, $manager);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->findById($idSet, $metadata, $manager);
        }

        return $objects;
    }

    /**
     * @param mixed                  $relation
     * @param EntityManagerInterface $entityManager
     *
     * @return array
     */
    protected function getSingleEntityRelation($relation, EntityManagerInterface $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    /**
     * @param mixed                  $id
     * @param ClassMetadata          $metadata
     * @param EntityManagerInterface $manager
     *
     * @return object|null
     */
    protected function findById($id, ClassMetadata $metadata, EntityManagerInterface $manager)
    {
        return $manager->find($metadata->getName(), $id);
    }
}
