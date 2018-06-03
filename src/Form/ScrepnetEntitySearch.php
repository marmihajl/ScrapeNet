<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/29/18
 * Time: 10:24 PM
 */

namespace App\Form;

use App\Repository\ScrepnetEntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ScrepnetEntitySearch extends AbstractType
{

    /**
     * @var ScrepnetEntityRepository
     */
    private $repository;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(ScrepnetEntityRepository $repository, SessionInterface $session)
    {
        $this->repository = $repository;
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $r = $this->repository->getDistinctDomains();

        //var_dump($r);die;

        $svi = [];

        $svi[''] = null;
        for ($i = 0; $i < sizeof($r); $i++) {
            $obj = $r[$i];
            $svi[$obj['url']] = $obj['escape_url'];
        }

        $svi = array_unique($svi);

        //var_dump($svi);die;

        $builder->add('domain', ChoiceType::class, array(
            'choices'  => $svi, 'label'=>'Domena:', 'data' => $this->session->get('domain'), 'required'=>false
        ))->add('url', TextType::class, ['label'=>'Title/description:', 'required'=>false])->add('save', SubmitType::class, ['label'=>'Pretraži']);
    }
}
