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
use AppBundle\Entity\Question;
use AppBundle\Form\QuestionType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;

class QuestionController extends Controller
{
    private function serialize($object, $group)
    {
        $classMetadataFactory = new ClassMetadataFactory(new YamlFileLoader(__DIR__.'/../Resources/config/serialization.yml'));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer));

        $data = $serializer->normalize($object, null, array('groups' => array($group)));

        return json_encode($data);
    }

    /**
     *
     * @ApiDoc(description="Récupèrer toutes les questions")
     *
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get("/questions")
     */
    public function getQuestionsAction(Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Question')->findAll();

        if (empty($entities)) {
            return View::create(['message' => 'Questions not found'], Response::HTTP_NOT_FOUND);
        }

        return $entities;
    }

    /**
     *
     * @ApiDoc(description="Récupèrer toutes les questions")
     *
     * @Rest\Get("/randomquestions")
     */
    public function getRandomQuestionsAction($limit = 3)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Question')->findAll();

        shuffle($entities);

        $arrayObj = new \ArrayObject();

        $i = 0;
        foreach($entities as $entity) {
            $arrayObj->append($entity);
            if (++$i == $limit) break;
        }

        if (empty($arrayObj)) {
            return View::create(['message' => 'Aucune question'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serialize($arrayObj, 'question');

        return new Response($data, Response::HTTP_OK);
    }

    /**
     *
     * @ApiDoc(description="Récupèrer une question en particulier")
     *
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get("/question/{id}")
     */
    public function getQuestionAction($id, Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        if (empty($entity)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Question not found');
        }

        return $entity;
    }

    /**
     *
     * @ApiDoc(description="Créer une question")
     *
     * @Rest\View(statusCode=201, serializerGroups={"question"})
     * @Rest\Post("/question/new")
     */
    public function postQuestionAction(Request $request)
    {
        $entity = new Question();
        $form = $this->createForm(QuestionType::class, $entity);

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
     * @ApiDoc(description="Modifier une question")
     *
     * @Rest\View()
     * @Rest\Put("/question/update/{id}")
     */
    public function updateQuestionAction(Request $request)
    {
        $entity = $this->getDoctrine()->getRepository('AppBundle:Question')->find($request->get('id'));

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(QuestionType::class, $entity);

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
     * @ApiDoc(description="Supprimer une question")
     *
     * @Rest\View(statusCode=204)
     * @Rest\Delete("/question/remove/{id}")
     */
    public function removeQuestionAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Question')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();
        }
    }
}