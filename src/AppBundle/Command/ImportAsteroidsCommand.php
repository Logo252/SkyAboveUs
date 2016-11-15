<?php

namespace AppBundle\Command;
use AppBundle\Entity\Asteroid;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Repository\AsteroidRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: shalifar
 * Date: 16.11.12
 * Time: 14.32
 */

/*
 * Class ImportAsteroidsCommand
 * @package AppBundle\Command
 */
class ImportAsteroidsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import:asteroids')
            ->setDescription('Import incoming asteroids');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting to import asteroid list');

        $date = '2016-11-14';
        $data = $this->getData('https://api.nasa.gov/neo/rest/v1/feed?start_date='
            .$date.'&end_date='.$date.'&detailed=false&api_key=Mb2wUHphygVlLVqIGgYG5FBcrTcSYrc9Gb1XzG8s');

        $count = $data['element_count'];

        $em = $this->getEntityManager();

        for ($i = 0; $i < $count; $i++)
        {
            $asteroid = new \AppBundle\Entity\Asteroid();
            $asteroid
                ->setName($data['near_earth_objects'][$date][$i]['name'])
                ->setDiameter($data['near_earth_objects'][$date][$i]['estimated_diameter']['meters']['estimated_diameter_max'])
                ->setVelocity($data['near_earth_objects'][$date][$i]['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'])
                ->setMissDistance($data['near_earth_objects'][$date][$i]['close_approach_data'][0]['miss_distance']['kilometers']);

            $this->save($em, $asteroid);
        }

        $em->flush();

        $output->writeln('Import successful!');
    }

    public function getData($request)
    {
        $json = file_get_contents($request);
        $data = json_decode($json, true);

        return $data;
    }

    private function save(EntityManager $manager,Asteroid $asteroid)
    {
        $manager->persist($asteroid);
    }

    private function getEntityManager()
    {
        return $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
