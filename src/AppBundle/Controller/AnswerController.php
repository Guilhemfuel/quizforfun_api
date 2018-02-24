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
use AppBundle\Entity\Answer;
use AppBundle\Form\AnswerType;

class AnswerController extends Controller
{
    /**
     *
     * @ApiDoc(description="Récupère toutes les réponses")
     *
     * @Rest\View(serializerGroups={"answer"})
     * @Rest\Get("/allanswers")
     */
    public function getAllAnswersAction(Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Answer')->findAll();

        if (empty($entities)) {
            return View::create(['message' => 'Answers not found'], Response::HTTP_NOT_FOUND);
        }

        return $entities;
    }

    /**
     *
     * @ApiDoc(description="Récupère toutes les réponses d'une question")
     *
     * @Rest\View(serializerGroups={"answer"})
     * @Rest\Get("/answers/{id}")
     */
    public function getAnswersAction($id, Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Answer')->findByQuestion($id);

        if (empty($entities)) {
            return View::create(['message' => 'Answers not found'], Response::HTTP_NOT_FOUND);
        }

        return $entities;
    }

    /**
     *
     * @ApiDoc(description="Récupèrer une seule réponse")
     *
     * @Rest\View(serializerGroups={"answer"})
     * @Rest\Get("/answer/{id}")
     */
    public function getAnswerAction($id, Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Answer')->find($id);

        if (empty($entity)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Answer not found');
        }

        return $entity;
    }

    /**
     *
     * @ApiDoc(description="Ajouter une réponse")
     *
     * @Rest\View(statusCode=201, serializerGroups={"answer"})
     * @Rest\Post("/answer/new")
     */
    public function postAnswerAction(Request $request)
    {
        $entity = new Answer();
        $form = $this->createForm(AnswerType::class, $entity);

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
     * @ApiDoc(description="Modifier une réponse")
     *
     * @Rest\View()
     * @Rest\Put("/answer/update/{id}")
     */
    public function updateAnswerAction(Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Answer')->find($request->get('id'));

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Answer not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(AnswerType::class, $entity);

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
     * @ApiDoc(description="Supprimer une réponse")
     *
     * @Rest\View(statusCode=204)
     * @Rest\Delete("/answer/remove/{id}")
     */
    public function removeAnswerAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Answer')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();
        }
    }
}