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
use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;

class PlayerController extends Controller
{
    /**
     *
     * @ApiDoc(description="Récupèrer tous les joueurs")
     *
     * @Rest\View(serializerGroups={"player"})
     * @Rest\Get("/players")
     */
    public function getPlayersAction()
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Player')->findAll();

        if (empty($entities)) {
            return View::create(['message' => 'Players not found'], Response::HTTP_NOT_FOUND);
        }

        return $entities;
    }

    /**
     *
     * @ApiDoc(description="Récupèrer un joueur en particulier")
     *
     * @Rest\View(serializerGroups={"player"})
     * @Rest\Get("/player/{id}")
     */
    public function getPlayerAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Player')->find($id);

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Ce joueur n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     *
     * @ApiDoc(description="Récupérer la partie en cours d'un joueur")
     *
     * @Rest\View(serializerGroups={"player"})
     * @Rest\Get("/currentPlayerGame/{fingerprint}")
     */
    public function getCurrentPlayerGameAction($fingerprint)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Player')->getCurrentPlayerInGame($fingerprint);

        if (!empty($entity)) {
            return $entity;
        }

        return new JsonResponse(['message' => 'Aucune partie en cours'], Response::HTTP_NOT_FOUND);
    }

    /**
     *
     * @ApiDoc(description="Ajouter un joueur")
     *
     * @Rest\View(statusCode=201, serializerGroups={"player"})
     * @Rest\Post("/player/new")
     */
    public function postPlayerAction(Request $request)
    {
        $entity = new Player();
        $form = $this->createForm(PlayerType::class, $entity);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {

            $name = $form->get('name')->getData();
            $game = $form->get('game')->getData();

            $player = $this->getDoctrine()->getRepository('AppBundle:Player')->findOneBy(['name' => $name, 'game' => $game]);
            $game = $this->getDoctrine()->getRepository('AppBundle:Game')->find($game);

            // $nb = count($game->getPlayers());
            if (count($game->getPlayers()) < $game->getNbPlayerMax()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $entity;

            } else {
                return new JsonResponse(['message' => 'Cette partie est complète'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $form;
        }
    }

    /**
     *
     * @ApiDoc(description="Modifier un joueur")
     *
     * @Rest\View()
     * @Rest\Put("/player/update/{id}")
     */
    public function updatePlayerAction(Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Player')->find($request->get('id'));

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlayerType::class, $entity);

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
     * @ApiDoc(description="Supprimer un joueur")
     *
     * @Rest\View(statusCode=204)
     * @Rest\Delete("/player/remove/{id}")
     */
    public function removePlayerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Player')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();

            return new JsonResponse(['message' => 'Player deleted'], Response::HTTP_OK);
        }
        else {
            return new JsonResponse(['message' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }
    }
}