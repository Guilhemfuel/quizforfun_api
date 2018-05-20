<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Game;
use AppBundle\Form\GameType;
use Pusher\Pusher;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;

class GameController extends Controller
{

    private function pusher($channel, $event, $data)
    {
        $options = array(
            'cluster' => 'eu'
        );

        require(dirname(__FILE__).'/../../../vendor/autoload.php');

        $pusher = new Pusher(
            'b1ed0160cc1033ce4f54',
            'b8c985250b64b569c5c3',
            '464485',
            $options
        );

        $pusher->trigger($channel, $event, $data);
    }

    private function serialize($object, $group)
    {
        $classMetadataFactory = new ClassMetadataFactory(new YamlFileLoader(__DIR__.'/../Resources/config/serialization.yml'));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        $data = $serializer->normalize($object, null, array('groups' => array($group)));

        return json_encode($data);
    }

    /**
     * @Rest\Get("/iswebview")
     */
    public function isWebViewAction(Request $request)
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "com.guilhem.quizforfun") {
            $webview = true;
        } else {
            $webview = false;
        }

        return new JsonResponse(['webview' => $webview], Response::HTTP_OK);
    }

    /**
     *
     * @ApiDoc(description="Actualiser les infos de tout les joueurs")
     *
     * @Rest\Get("/refreshGame/{code}")
     */
    public function refreshGameAction($code)
    {
        $game = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array('code' => $code));

        $data = $this->serialize($game, 'game');
        $this->pusher($game->getCode(), 'game', $data);

        return new JsonResponse(['message' => 'Refresh'], Response::HTTP_OK);
    }

    /**
     *
     * @ApiDoc(description="Récupérer toutes les parties en cours", output= { "class"=Game::class })
     *
     * @Rest\View(serializerGroups={"game"})
     * @Rest\Get("/games")
     */
    public function getGamesAction()
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Game')->findAll();

        if (empty($entities)) {
            return View::create(['message' => 'Games not found'], Response::HTTP_NOT_FOUND);
        }

        return $entities;
    }

    /**
     *
     * @ApiDoc(description="Récupèrer une partie en cours")
     *
     * @Rest\View(serializerGroups={"game"})
     * @Rest\Get("/game/{code}")
     */
    public function getGameAction($code)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findByCode($code);

        if (empty($entity)) {
            return View::create(['message' => 'Cette partie n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     *
     * @ApiDoc(description="Lancer une partie")
     *
     * @Rest\View(serializerGroups={"game"})
     * @Rest\Get("/game/start/{code}")
     */
    public function startGameAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneByCode($code);

        $entity->setIsStarted(true);
        $em->persist($entity);
        $em->flush();

        if (empty($entity)) {
            return View::create(['message' => 'Cette partie n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     *
     * @ApiDoc(description="Finir une partie")
     *
     * @Rest\View(serializerGroups={"game"})
     * @Rest\Get("/game/end/{code}")
     */
    public function endGameAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneByCode($code);

        $entity->setIsFinished(true);
        $em->persist($entity);
        $em->flush();

        if (empty($entity)) {
            return View::create(['message' => 'Cette partie n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    private function nextQuestionAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneByCode($code);

        $count = 0;
        $count = count(json_decode($entity->getQuestions(), true));

        // Aligner l'array qui commence à 0 avec le nb total
        $count = ($count > 0) ? $count - 1 : 0;

        $currentQuestion = $entity->getCurrentQuestion();

        if ($currentQuestion < $count)
        {
            $entity->setTimerIsStarted(0);
            $entity->setCurrentQuestion($currentQuestion + 1);
        }
        else
        {
            $entity->setIsFinished(true);
        }

        $em->persist($entity);
        $em->flush();

        sleep(5);

        $this->refreshGameAction($code);
    }

    /**
     *
     * @ApiDoc(description="Déclencher le timer")
     *
     * @Rest\Get("/game/startTimer/{code}/{id}")
     */
    public function startTimerAction($code, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $game = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array('code' => $code));

        // Si c'est la première fois qu'un joueur répond à la question on lance le chrono pour tout le monde sinon on ne fait rien
        if (!$game->getTimerIsStarted())
        {
            $message = null;

            $player = $this->getDoctrine()->getRepository('AppBundle:Player')->find($id);

            if ($player) {
                $message = $player->getName() . ' a répondu en premier !';

                $player->setIsFirstToAnswer(1);
                $em->persist($player);
            }

            $game->setTimerIsStarted(1);
            $em->persist($game);
            $em->flush();

            $this->pusher($game->getCode(), 'timer', array('status' => true, 'message' => $message));

            //On attend la fin du timer
            sleep(6);

            $this->nextQuestionAction($code);

            return new JsonResponse(['message' => 'Timer Started'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Timer already started'], Response::HTTP_OK);
    }

    /**
     *
     * @ApiDoc(description="Finir une partie")
     *
     * @Rest\Get("/game/submitAnswer/{idPlayer}/{idAnswer}")
     */
    public function submitAnswerAction($idPlayer, $idAnswer)
    {
        $em = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository('AppBundle:Player')->findOneById($idPlayer);
        $answer = $this->getDoctrine()->getRepository('AppBundle:Answer')->findOneById($idAnswer);

        if (empty($player)) {
            return View::create(['message' => 'Ce joueur n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        if (empty($answer)) {
            return View::create(['message' => 'Cette réponse n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        if ($answer->getGoodAnswer())
        {
            // On donne 1 point bonus si le joueur répond le premier
            if ($player->getIsFirstToAnswer())
            {
                $player->setScore($player->getScore() + 2);
            }
            else {
                $player->setScore($player->getScore() + 1);
            }

            $player->setIsFirstToAnswer(0);

            $em->persist($player);
            $em->flush();

            return new JsonResponse(['answer' => true], Response::HTTP_OK);
        }
        else {
            // Le joueur perd 1 point pour une mauvaise réponse
            if ($player->getIsFirstToAnswer())
            {
                $player->setScore($player->getScore() - 1);
                $player->setIsFirstToAnswer(0);

                $em->persist($player);
                $em->flush();
            }
        }

        return new JsonResponse(['answer' => false], Response::HTTP_OK);
    }

    /**
     *
     * @ApiDoc(description="Créer une partie")
     *
     * @Rest\View(statusCode=201, serializerGroups={"game"})
     * @Rest\Post("/game/new")
     */
    public function postGameAction(Request $request)
    {

        $entity = new Game();

        // Générer un code pour la partie
        $entity->setCode($this->randomCode());

        $form = $this->createForm(GameType::class, $entity);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $data = $this->forward('AppBundle:Question:getRandomQuestions', array('limit'  => 10));

            $entity->setQuestions($data->getContent());
            $em->persist($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     *
     * @ApiDoc(description="Modifier une partie en cours")
     *
     * @Rest\View()
     * @Rest\Put("/game/update/{id}")
     */
    public function updateGameAction(Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->find($request->get('id'));

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(GameType::class, $entity);

        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     *
     * @ApiDoc(description="Supprimer une partie")
     *
     * @Rest\View(statusCode=204)
     * @Rest\Delete("/game/remove/{id}")
     */
    public function removeGameAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Game')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();

            return new JsonResponse(['message' => 'Game deleted'], Response::HTTP_OK);
        }
    }

    private function randomCode($length = 4) {
        $str = "";
        $characters = array_merge(range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    /**
     *
     * @Rest\Get("/downloadApp")
     */
    public function downloadAppAction()
    {
        $filename = 'QuizForFun.apk';

        $path = $this->get('kernel')->getRootDir(). "/../web/downloads/";
        $content = file_get_contents($path.$filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

        $response->setContent($content);
        return $response;
    }
}