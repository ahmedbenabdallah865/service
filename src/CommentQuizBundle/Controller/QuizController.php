<?php


namespace CommentQuizBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class QuizController extends Controller
{

    public function DemandeAction(Request $request)
    {
        $user = $this->getUser();
        if($user->getRoles()[0]=="SPECTATEUR")
        {
            return $this->render('@CommentQuiz/Default/demmandes.html.twig');
        }
        else
        {
            return $this->render('default\index.html.twig');
        }
    }
    public function theatreAction(Request $req)
    {
        $em = $this->getDoctrine()->getManager();
        $questions= $em->getRepository("CommentQuizBundle:Question")->findBy(['sujet'=>'theatre']);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($questions);
        return new JsonResponse($formatted);
    }
    public function sportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $questions= $em->getRepository("CommentQuizBundle:Question")->findBy(['sujet'=>'sport']);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($questions);
        return new JsonResponse($formatted);
    }
    public function musiqueAction()
    {
        $em = $this->getDoctrine()->getManager();
        $questions= $em->getRepository("CommentQuizBundle:Question")->findBy(['sujet'=>'musique']);
        return $this->render('@CommentQuiz/Default/question.html.twig',array("questions"=>$questions));
    }
    public function danseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $questions= $em->getRepository("CommentQuizBundle:Question")->findBy(['sujet'=>'danse']);
        return $this->render('@CommentQuiz/Default/question.html.twig',array("questions"=>$questions));
    }
    public function succesAction(Request $request)
    {



            $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
                ->setUsername('ahmedbenabdallah1111@gmail.com')
                ->setPassword('wgcaeyxslreekvns');
            $mailer = new \Swift_Mailer($transport);

            // Create a message
            $message = (new \Swift_Message('vous etes accepter en tant que candidat'))
                ->setFrom('ahmedbenabdallah1111@gmail.com')

               // ->setTo($this->getUser()->getEmail()); current user
        ->setTo("ahmed.benabdallah.1@esprit.tn");

            $mailer->send($message);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($message);
        return new JsonResponse($formatted);

    }
    public function failAction(Request $request)
    {



        $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
            ->setUsername('ahmedbenabdallah1111@gmail.com')
            ->setPassword('wgcaeyxslreekvns');
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message('vous etes pas  accepter .. bonne chance'))
            ->setFrom('ahmedbenabdallah1111@gmail.com')

            // ->setTo($this->getUser()->getEmail()); current user
            ->setTo("ahmed.benabdallah.1@esprit.tn");

        $mailer->send($message);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($message);
        return new JsonResponse($formatted);

    }
}