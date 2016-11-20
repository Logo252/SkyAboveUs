<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $planets = $em->getRepository('AppBundle:Planet')->findAll();
        $nasa_api = new NasaAPI();
        $news = $nasa_api->getNews();
//        $nasa_api->saveNasaData($news);
        return $this->render('default/index.html.twig', [
            'planets' => $planets,
            'articles' => $news
        ]);
    }

    /**
     * @Route("/videos/{currentPage}", name="viewing_all_videos")
     */
    public function viewingAllVideosAction($currentPage)
    {
        $videos = $this->getDoctrine()
            ->getRepository('AppBundle:Video')
            ->getAllVideos($currentPage);
        $iterator = $videos->getIterator();
        $videosPerPage = 6;
        $maxPages = ceil($videos->count()/$videosPerPage);
        return $this->render('videos/view_all_videos.html.twig',
            [
                'maxPages' => $maxPages,
                'videos' => $iterator,
                'currentPage' => $currentPage
            ]
        );
    }

//    /**
//     * @Route("/videos/{planetName}/{currentPage}", name="show_videos_by_name")
//     * @param $planetName
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function planetsAction($planetName, $currentPage)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $video = $em->getRepository('AppBundle:Video')->findBykeyName($planetName);
//        $planet = $em->getRepository('AppBundle:Planet')->findOneBy(['name' => $planetName]);
//        $planets = $em->getRepository('AppBundle:Planet')->findAll();
//        $videos = $em->getRepository('AppBundle:Video')
//            ->getAllVideos($currentPage);
//        $iterator = $videos->getIterator();
//        $videos = 6;
//        $maxPages = ceil($videos->count()/$videos);
//        return $this->render('services/upcoming_events.html.twig',
//            [
//                'maxPages' => $maxPages,
//                'videos' => $iterator,
//                'currentPage' => $currentPage
//                'video' => $video,
//                'planet' => $planet,
//                'planetsList' => $planets
//            ]
//        );
//    }

    /**
     * @Route("/channels/{channelName}", name="show_videos_by_channel")
     * @param $channelName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function channelAction($channelName)
    {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository('AppBundle:Video')->findBychannelName($channelName);
        $planets = $em->getRepository('AppBundle:Planet')->findAll();


        return $this->render('planet/planet_channel_list.html.twig',
            [
                'video' => $video,
                'planetsList' => $planets,
                'channelName' => $channelName
            ]
        );
    }


    /**
     * @Route("/planets/{planetName}", name="show_planet")
     * @param $planetName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPlanet($planetName)
    {
        $em = $this->getDoctrine()->getManager();
        $planets = $em->getRepository('AppBundle:Planet')->findAll();
        $planet = $em->getRepository('AppBundle:Planet')->findOneBy(['name' => $planetName]);
        $video = $em->getRepository('AppBundle:Video')->findOneBy(['keyName' => $planetName]);
        if (!$planet) {
            throw $this->createNotFoundException('Ups! No planet found!');
        }
        return $this->render('planet/planet.html.twig', [

            'planet' => $planet,
            'planetsList' => $planets,
            'video' => $video
        ]);
    }

    /**
     * @Route("/news/{articleID}", name="show_article")
     * @param $articleID
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showArticle($articleID)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->findOneBy(['id' => $articleID]);
        if (!$article) {
            throw $this->createNotFoundException('Ups! No article found!');
        }

        return $this->render('newsFeed/article.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/news", name="news_list")
     */
    public function showNews()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findAll();

        return $this->render('newsFeed/news.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/test")
     */
    public function showTest()
    {
        $nasa_api = new NasaAPI();
        $text = $nasa_api->getNews();

        return $this->render(
            'nasaApi/test.html.twig',
            [
                'text' => $text,
            ]
        );
    }

    /**
     * @Route("/events", name="upcoming_events")
     */
    public function upcomingEventsAction()
    {
        return $this->render('services/upcoming_events.html.twig');
    }

    /**
     * @Route("/videos", name="astronomical_videos")
     */
    public function astronomicalVideosAction()
    {

        return $this->render('services/videos.html.twig');
    }

    /**
     * @Route("/articles", name="astronomical_articles")
     */
    public function astronomicalArticlesAction()
    {

        return $this->render('services/articles.html.twig');
    }

    /**
     * @Route("/astronomy-picture", name="astronomy_picture")
     */
    public function astronomyPictureAction()
    {

        return $this->render('services/picture_of_the_day.html.twig');
    }

    /**
     * @Route("/solar-system-display", name="solar_system_display")
     */
    public function solarSystemDisplayAction()
    {

        return $this->render('services/solar_system_display.html.twig');
    }

    /**
     * @Route("/constellation-display", name="constellation_display")
     */
    public function constellationDisplayAction()
    {
        return $this->render('services/constellation.html.twig');
    }

    /**
     * @Route("/constellation-position-calculator", name="constellation_position_calculator")
     */
    public function constellationPositionCalculatorAction()
    {

        return $this->render('services/constellation_calculator.html.twig');
    }

    /**
     * @Route("/planet-position-calculator", name="planet_position_calculator")
     */
    public function planetPositionCalculatorAction()
    {

        return $this->render('services/planet_calculator.html.twig');
    }
}
