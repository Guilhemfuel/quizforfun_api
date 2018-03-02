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

class GameController extends Controller
{
    /**
     *
     * @ApiDoc(description="Récupérer toutes les parties en cours", output= { "class"=Game::class })
     *
     * @Rest\View(serializerGroups={"game"})
     * @Rest\Get("/games")
     */
    public function getGamesAction(Request $request)
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
    public function getGameAction($code, Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findByCode($code);

        if (empty($entity)) {
            return View::create(['message' => 'Cette partie n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
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
        $form = $this->createForm(GameType::class, $entity);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
    public function removeGameAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Game')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();
        }
    }
}