<?php


namespace CommentQuizBundle\Controller;


use  CommentQuizBundle\Entity\Commentaire;


use Esprit\ApiBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class CommentaireController extends Controller
{
    //============================Métier:Commentaire======================================================================

    public function affichecommentaireAction($publication_id){
        $em = $this->getDoctrine()->getManager();
        $publication = $em
            ->getRepository('CommentQuizBundle:Publication')
            ->find($publication_id);
        $commentaires = $em->getRepository("CommentQuizBundle:Commentaire")->findBy(["publication" => $publication , "actif" => 1]);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($commentaires);
        return new JsonResponse($formatted);
    }

    public function newAction($contenu,$actif,$idu,$idp)
    {
        $em = $this->getDoctrine()->getManager();
        $Commentaire = new Commentaire();


        $Publication =$em->getRepository("CommentQuizBundle:Publication")->find($idp);
        $Commentaire->setPublication($Publication);
        $User=$em->getRepository("UserBundle:User")->find($idu);
        $Commentaire->setUser($User);
        $Commentaire->setContenu($contenu);
        $Commentaire->setActif($actif);
        $Commentaire->setCreatedAt(new \DateTime());
        $em->persist($Commentaire);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Commentaire);
        return new JsonResponse($formatted);
    }

    public function ajoutercommentAction($id,Request $request)
    {
        $commentaire = new Commentaire();
        $commentaire->setContenu($request->get("contenu"));
        $commentaire->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository("CommentQuizBundle:Publication")->find($id);
        $commentaire->setPublication($publication);
        $commentaire->setActif(0);
        $commentaire->setUser($this->getUser());
        $em->persist($commentaire);
        $em->flush();
        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('afficheCommentaire', ["id" => $id]));
    }

    public function supprimercommentAction($id)
    {
        //recuperer le commentaire à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository("CommentQuizBundle:Commentaire")->find($id);
        $idpub = $commentaire->getPublication()->getId();
        //remove $commentaire
        $em->remove($commentaire);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($commentaire);
        return new JsonResponse($formatted);
    }

    public function updateAction( $id,$contenu)
    {
        $em=$this->getDoctrine()->getManager();
        $commentaire = $em->getRepository("CommentQuizBundle:Commentaire")->find($id);
        $commentaire->setContenu($contenu);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($commentaire);
        return new JsonResponse($formatted);
    }



















    public function affichecommentairedashboardAction(Request $request){

        //afficher tous les commentaire non comfirmer
        $em = $this->getDoctrine()->getManager();
        $commentaires = $em->getRepository("CommentQuizBundle:Commentaire")->findBy(["actif" => 0]);

        $pagination  = $this->get('knp_paginator')->paginate(
            $commentaires,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            3/*nbre d'éléments par page*/
        );
        return $this->render('@CommentQuiz\Commentaire\affichedashboard.html.twig' , [  "commentaires" => $pagination]);
    }
    public function changeretatAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository("CommentQuizBundle:Commentaire")->find($id);
        $commentaire->setActif(1);
        $em->persist($commentaire);
        $em->flush();
        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Commentaire_list_dashboard'));
    }

}