<?php

namespace App\Repository;

use App\Entity\ScrepnetEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method ScrepnetEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScrepnetEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScrepnetEntity[]    findAll()
 * @method ScrepnetEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScrepnetEntityRepository extends ServiceEntityRepository
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(RegistryInterface $registry, ContainerInterface $container, SessionInterface $session)
    {
        parent::__construct($registry, ScrepnetEntity::class);
        $this->container = $container;
        $this->session = $session;
    }

    public function getDistinctDomains()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT DISTINCT(url), escape_url FROM screpnet_entity;';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    public function getByDomainOrText($domain, $text)
    {
        $conn = $this->getEntityManager()->getConnection();

        $page = $this->container->getParameter('pagination');

        $sql = '';

        if(isset($domain) && isset($text)){
            $sql = 'SELECT * FROM screpnet_entity WHERE escape_url = "'.$domain.'" AND MATCH (title,description) AGAINST ("'.$text.'" IN NATURAL LANGUAGE MODE) ORDER BY date';
        }elseif (isset($domain)){
            $sql = 'SELECT * FROM screpnet_entity WHERE escape_url = "'.$domain.'" ORDER BY date';
        }elseif (isset($text)){
            $sql = 'SELECT * FROM screpnet_entity WHERE MATCH (title,description) AGAINST ("'.$text.'" IN NATURAL LANGUAGE MODE) ORDER BY date';
        }else{
            $sql = 'SELECT * FROM screpnet_entity ORDER BY date';
        }

        $this->session->set('lastQuery', $sql);

        $sql .= " LIMIT $page;";

        //var_dump($sql);die;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    public function lastQuery($page)
    {
        $conn = $this->getEntityManager()->getConnection();
        $pagination = $this->container->getParameter('pagination');

        $sql = $this->session->get('lastQuery');

        $sql .= " LIMIT $pagination  OFFSET " . $page * $pagination . ";";

        //var_dump($page);die;

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}
