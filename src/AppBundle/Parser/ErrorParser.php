<?php

namespace AppBundle\Parser;

use Doctrine\ORM\EntityManager;
use AppBundle\Errors\ApiErrorInterface;
use AppBundle\Entity\Error;
use AppBundle\Entity\ApiUser;
use AppBundle\Entity\ErrorMeta;

class ErrorParser
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ApiUser
     */
    private $user;

    public function __construct(EntityManager $manager, ApiUser $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }

    private function generateResponse($id, ApiErrorInterface $error)
    {
        $std = new \stdClass();

        $this->addStdProperty($std, 'id', $id)
            ->addStdProperty($std, 'links', $error->getLinks())
            ->addStdProperty($std, 'status', $error->getStatus())
            ->addStdProperty($std, 'code', $error->getCode())
            ->addStdProperty($std, 'title', $error->getTitle())
            ->addStdProperty($std, 'detail', $error->getDetails())
            ->addStdProperty($std, 'source', $error->getSource())
            ->addStdProperty($std, 'meta', $error->getMeta());

        return $std;
    }

    private function storeError(ApiErrorInterface $error)
    {
        $errorEntity = new Error();

        $errorEntity->setCode($error->getCode());
        $errorEntity->setDate(new \DateTime());
        $errorEntity->setMessage($error->getTitle());
        $errorEntity->setUser($this->user);

        $sourceMeta = new ErrorMeta();
        $sourceMeta->setName('source');
        $sourceMeta->setContent(json_encode($error->getSource()));
        $errorEntity->addMeta($sourceMeta);

        $detailMeta = new ErrorMeta();
        $detailMeta->setName('detail');
        $detailMeta->setContent($error->getDetails());
        $errorEntity->addMeta($detailMeta);

        foreach ($error->getLinks() as $link) {
            $meta = new ErrorMeta();
            $meta->setName('link');
            $meta->setContent(json_encode($link));

            $errorEntity->addMeta($meta);
            $this->manager->persist($meta);
        }

        foreach ($error->getMeta() as $key => $value) {
            $meta = new ErrorMeta();
            $meta->setName($key);
            $meta->setContent($value);

            $errorEntity->addMeta($meta);
            $this->manager->persist($meta);
        }

        $this->manager->persist($sourceMeta);
        $this->manager->persist($detailMeta);
        $this->manager->persist($errorEntity);
        $this->manager->flush();

        return $errorEntity->getId();
    }

    private function addStdProperty($std, $property, $value)
    {
        if ($value !== null) {
            $std->$property = $value;
        }

        return $this;
    }

    public function parse(ApiErrorInterface $error)
    {
        return $this->generateResponse(
            $this->storeError($error),
            $error
        );
    }

}
