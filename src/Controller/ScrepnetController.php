<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/29/18
 * Time: 7:24 PM
 */

namespace App\Controller;

use App\Entity\ScrepnetEntity;
use App\Form\ScrepenetEntityType;
use App\Form\ScrepnetEntitySearch;
use App\Repository\ScrepnetEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Embed\Embed;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ScrepnetController extends Controller
{

    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var ScrepnetEntityRepository
     */
    private $repository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        \Twig_Environment $twig,
        ScrepnetEntityRepository $repository,
        FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager,
        RouterInterface $router,
        SessionInterface $session,
        ContainerInterface $container
    ) {
        $this->twig = $twig;
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->session = $session;
        $this->container = $container;
    }

    /**
     * @Route("/", name="screpenet_index")
     */
    public function index(Request $request)
    {
        $screpnetEntity = new ScrepnetEntity();

        $form = $this->formFactory->create(ScrepenetEntityType::class);

        $form->handleRequest($request);

        if ($form->getClickedButton() && 'redirect' === $form->getClickedButton()->getName()) {
            $data = $form->getData();
            return $this->redirect($data['url']);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $info = Embed::create($data['url']);



            //var_dump(base64_encode(parse_url($info->url)['host']));
            //die;

            $screpnetEntity->setSlug(md5($info->url));
            $screpnetEntity->setPath($info->url);
            $screpnetEntity->setUrl(parse_url($info->url)['host']);
            $screpnetEntity->setEscapeUrl(md5(parse_url($info->url)['host']));
            $screpnetEntity->setDescription($info->description);
            $screpnetEntity->setTitle($info->title);
            $screpnetEntity->setDate(new \DateTime());
            $screpnetEntity->setImage($info->image);

            try {
                $this->entityManager->persist($screpnetEntity);
                $this->entityManager->flush();
            } catch (\Exception $e) {
            }
        }

        return new Response($this->twig->render('screpenet/index.html.twig', ['form'=>$form->createView(), 'scrapes'=>$this->repository->getDistinctDomains()]));
    }

    /**
     * @Route("/{name}", name="screpenet_details")
     */
    public function details($name)
    {
        $r = $this->repository->findOneBy(['slug'=>$name]);

        $starost = $r->getDate();
        $danas = new \DateTime();

        $razlika = $starost->diff($danas);

        $razlikaString = "Godina: " . $razlika->y . ", mjeseci: " . $razlika->m . ", dana: " . $razlika->d;

        return new Response($this->twig->render('screpenet/details.html.twig', ['page'=>$r, 'razlika' => $razlikaString]));
    }

    /**
     * @Route("/search/{name}", name="screpenet_add_domain")
     */
    public function addSessionDomain($name, Request $request)
    {
        $this->session->set('domain', $name);

        //return $this->redirect('https://www.google.com/');

        return $this->redirect('/search/', 308);
    }

    /**
     * @Route("/search/", name="screpenet_SERP")
     */
    public function searchScrepnet(Request $request)
    {
        if (!empty($this->session->get('page'))) {
            $page = intval($this->session->get('page'));
            $this->session->set('page', '');

            $r = null;

            $form = $this->formFactory->create(ScrepnetEntitySearch::class);

            $form->handleRequest($request);

            $r = $this->repository->lastQuery($page-1);

            $broj = count($r);

            if ($broj == 0) {
                $page = $page-1;
                $r = $this->repository->lastQuery($page-1);

                $broj = count($r);
            }

            $nextPage = true;
            if ($broj < $this->container->getParameter('pagination')) {
                $nextPage = false;
            }
        } else {
            $r = null;

            $form = $this->formFactory->create(ScrepnetEntitySearch::class);

            $form->handleRequest($request);

            //$r = $this->repository->getByDomainOrText();

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                //echo 'x'; die;

                if (isset($data["url"]) && isset($data["domain"])) {
                    $r = $this->repository->getByDomainOrText($data["domain"], $data["url"]);
                } elseif (isset($data["domain"])) {
                    $r = $this->repository->getByDomainOrText($data["domain"], null);
                } elseif (isset($data["url"])) {
                    $r = $this->repository->getByDomainOrText(null, $data["url"]);
                } else {
                    $r = $this->repository->getByDomainOrText(null, null);
                }
            } else {
                if (!empty($this->session->get('domain'))) {
                    $r = $this->repository->findOneBy(['escapeUrl'=>$this->session->get('domain')]);
                    $r = $this->repository->getByDomainOrText($this->session->get('domain'), null);
                //$this->session->set('domain', '');
                } else {
                    $r = $this->repository->getByDomainOrText(null, null);
                }
            }

            $this->session->set('domain', '');
            $this->session->set('url', '');

            $broj = count($r);

            $nextPage = true;
            if ($broj < $this->container->getParameter('pagination')) {
                $nextPage = false;
            }

            $page = 1;
        }
        return new Response($this->twig->render('screpenet/search.html.twig', ['form'=>$form->createView(), 'result'=>$r, 'nextPage' => $nextPage, 'page'=>$page]));
    }

    /**
     * @Route("/search/page/{number}", name="screpenet_SERP_offset")
     */
    public function offsetScrepnet($number)
    {
        $this->session->set('page', $number);
        return $this->redirect('/search/', 308);
    }
}
