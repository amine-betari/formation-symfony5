<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GetData;
use DateTime;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DepartmentController extends AbstractController
{
    /**
     * @Route("/department/{department}", name="app_department")
     */
    public function index(string $department, GetData $callApiService, ChartBuilderInterface $chartBuilder): Response
    {
       // dd($callApiService->getDepartmentData($department));

        $label = [];
        $hospitalisation = [];
        $rea = [];

        for ($i=1; $i < 8; $i++) {
            $date = New DateTime('- '. $i .' day');
            $datas = $callApiService->getAllDataByDate($date->format('Y-m-d'));

            foreach ($datas['allFranceDataByDate'] as $data) {
                if( $data['nom'] === $department) {
                    $label[] = $data['date'];
                    $hospitalisation[] = $data['nouvellesHospitalisations'];
                    $rea[] = $data['nouvellesReanimations'];
                    break;
                }
            }
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => array_reverse($label),
            'datasets' => [
                [
                    'label' => 'Nouvelles Hospitalisations',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => array_reverse($hospitalisation),
                ],
                [
                    'label' => 'Nouvelles entrées en Réa',
                    'borderColor' => 'rgb(46, 41, 78)',
                    'data' => array_reverse($rea),
                ],
            ],
        ]);

        $chart->setOptions([/* ... */]);
        return $this->render('department/index.html.twig', [
            'data' => $callApiService->getDepartmentData($department),
            'chart' => $chart,
        ]);
    }
}
