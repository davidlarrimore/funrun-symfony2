<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\CampaignHelper;
use App\Entity\Classroom;
use App\Entity\Campaign;
use App\Entity\CampaignAward;
use App\Entity\Team;
use App\Utils\QueryHelper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use \DateTime;
use \DateTimeZone;

/**
 * Manage Campaign controller.
 *
 * @Route("/{campaignUrl}")
 */
class CampaignController extends Controller
{

  /**
   * @Route("/", name="campaign_index")
   */
   public function campaignIndexAction($campaignUrl, LoggerInterface $logger)
   {

     
     $limit = 3;
     $em = $this->getDoctrine()->getManager();

     //CODE TO CHECK TO SEE IF CAMPAIGN EXISTS
     $campaign = $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

     $queryHelper = new QueryHelper($em, $logger);

     $reportDate = $queryHelper->convertToDay(new DateTime());
     $reportDate->modify('-1 day');

     // replace this example code with whatever you need
     return $this->render('campaign/campaign.index.html.twig', array(
       'new_classroom_awards' => $queryHelper->getClassroomAwards(array('campaign' => $campaign, 'limit' => 5, 'order_by' => array('field' => 'donated_at',  'order' => 'asc'))),
       'classroom_rankings' => $queryHelper->getClassroomRanks(array('campaign' => $campaign)),
       'report_date' => $reportDate,
       'ranking_limit' => $limit,
       'campaign_awards' =>  $em->getRepository(CampaignAward::class)->findBy(array('campaign'=>$campaign)),
       'teams' => $em->getRepository(Team::class)->findBy(array('campaign'=>$campaign),array('fundsRaised' => 'DESC')),
       'totals' => $queryHelper->getTotalDonations(array('campaign' => $campaign)),
       'campaign' => $campaign
     ));

  }



  /**
   * @Route("/dashboard", name="campaign_dashboard")
   */
   public function campaignDashboardAction($campaignUrl, LoggerInterface $logger)
   {

     
     $limit = 10;
     $em = $this->getDoctrine()->getManager();

     //CODE TO CHECK TO SEE IF CAMPAIGN EXISTS
     $campaign = $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

     $queryHelper = new QueryHelper($em, $logger);

     $reportDate = $queryHelper->convertToDay(new DateTime());
     $reportDate->modify('-1 day');

     // replace this example code with whatever you need
     return $this->render('campaign/campaign.dashboard.html.twig', array(
       'new_classroom_awards' => $queryHelper->getClassroomAwards(array('campaign' => $campaign, 'limit' => 5, 'order_by' => array('field' => 'donated_at',  'order' => 'asc'))),
       'classroom_rankings' => $queryHelper->getClassroomRanks(array('campaign' => $campaign,'limit'=> $limit)),
       'student_rankings' => $queryHelper->getStudentRanks(array('campaign' => $campaign, 'limit'=> $limit)),
       'report_date' => $reportDate,
       'ranking_limit' => $limit,
       'team_rankings' => $queryHelper->getTeamRanks(array('campaign' => $campaign,'limit'=> $limit)),
       'totals' => $queryHelper->getTotalDonations(array('campaign' => $campaign,)),
       'campaign' => $campaign
     ));

  }




  /**
   * Displays Coming Soon Splash Page
   *
   * @Route("/coming_soon", name="campaign_splash", methods={"GET"})
   * 
   */
  public function spashAction($campaignUrl, LoggerInterface $logger)
  {
      

      $em = $this->getDoctrine()->getManager();

      //CODE TO CHECK TO SEE IF CAMPAIGN EXISTS
      $campaign = $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

      return $this->render('campaign/campaign.splash.html.twig', array(
          'campaign' => $campaign,
      ));
  }


  /**
   * Displays Coming Soon Splash Page
   *
   * @Route("/thank_you", name="campaign_end_splash", methods={"GET"})
   */
  public function endSpashAction($campaignUrl, LoggerInterface $logger)
  {
    
    $limit = 15;
    $em = $this->getDoctrine()->getManager();

    //CODE TO CHECK TO SEE IF CAMPAIGN EXISTS
    $campaign = $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

    $queryHelper = new QueryHelper($em, $logger);

    $reportDate = $queryHelper->convertToDay(new DateTime());
    $reportDate->modify('-1 day');

    // replace this example code with whatever you need
    return $this->render('campaign/campaign.end.splash.html.twig', array(
      'new_classroom_awards' => $queryHelper->getClassroomAwards(array('campaign' => $campaign, 'limit' => 5, 'order_by' => array('field' => 'donated_at',  'order' => 'asc'))),
      'classroom_rankings' => $queryHelper->getClassroomRanks(array('campaign' => $campaign,'limit'=> $limit)),
      'student_rankings' => $queryHelper->getStudentRanks(array('campaign' => $campaign, 'limit'=> $limit)),
      'report_date' => $reportDate,
      'ranking_limit' => $limit,
      'family_team_rankings' => $queryHelper->getTeamRanks(array('campaign' => $campaign,'limit'=> $limit, 'team_type'=>'studentAndFamily')),
      'teacher_team_rankings' => $queryHelper->getTeamRanks(array('campaign' => $campaign,'limit'=> $limit, 'team_type'=>'teacher')),
      'totals' => $queryHelper->getTotalDonations(array('campaign' => $campaign,)),
      'campaign' => $campaign
    ));
  }



    /**
     * Finds and displays a Campaign entity.
     *
     * @Route("/show/{id}", name="campaign_show", methods={"GET"})
     * 
     */
    public function showAction(Campaign $campaign, LoggerInterface $logger)
    {
        
        $entity = 'Campaign';
        $deleteForm = $this->createDeleteForm($campaign);

        return $this->render('campaign/show.html.twig', array(
            'campaign' => $campaign,
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity,
        ));
    }


    /**
     * @Route("/faq", name="campaign_faq")
     */
    public function faqAction($campaignUrl, LoggerInterface $logger)
    {
      
      $em = $this->getDoctrine()->getManager();
      $queryHelper = new QueryHelper($em, $logger);
      $campaign =  $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

      return $this->render('campaign/campaign.faq.html.twig', array(
        'campaign' => $campaign,
      ));
    }


    /**
     * @Route("/email_support", name="email_support")
     */
    public function emailSupportAction(Request $request, $campaignUrl, LoggerInterface $logger)
    {
      
      $em = $this->getDoctrine()->getManager();
      $queryHelper = new QueryHelper($em, $logger);
      $campaign =  $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

      if ($request->isMethod('POST')) {
          $params = $request->request->all();
          $failure = false;

          if(!$failure && empty($params['emailSupport']['email'])){
            $this->addFlash('warning','Email Address is required.');
            $failure = true;
          }


          if(!$failure && empty($params['emailSupport']['subject'])){
            $this->addFlash('warning','Subject is requried');
            $failure = true;
          }

          if(!$failure && empty($params['emailSupport']['description'])){
            $this->addFlash('warning','Body of email is required');
            $failure = true;
          }

          if(!$failure){

              $message = (new \Swift_Message("[FR Manager] Support Request Received"))
                ->setFrom('funrun@lrespto.org')
                ->setTo('funrun@lrespto.org')
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView('email/support.email.twig', array('emailSupport' => $params['emailSupport'], 'campaign' => $campaign))
                );

              $this->get('mailer')->send($message);

              $logger->info("Support Email Sent");
              $this->addFlash('success','Email Successfully sent!');
              return $this->redirectToRoute('campaign_index', array('campaignUrl' => $campaign->getUrl()));
            }
      }



      return $this->render('campaign/email_support.html.twig', array(
        'campaign' => $campaign,
      ));
    }




    /**
     * @Route("/terms_of_service", name="campaign_terms_of_service")
     */
    public function termsOfServiceAction($campaignUrl, LoggerInterface $logger)
    {
      
      $em = $this->getDoctrine()->getManager();

      $campaign =  $em->getRepository(Campaign::class)->findOneByUrl($campaignUrl);

      return $this->render('default/termsOfService.html.twig', array(
        'campaign' => $campaign,
      ));
    }

}
