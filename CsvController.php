<?php



namespace AppBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\StreamedResponse;

use Goodby\CSV\Export\Standard\Exporter;

use Goodby\CSV\Export\Standard\ExporterConfig;

use Goodby\CSV\Export\Standard\Collection\PdoCollection;





use PDO;



class CsvController extends Controller

{

	/**

	 * @Route("/csv/{startDate}/{endDate}")

	 */

    public function csvExportAction($startDate, $endDate)

    {

        $conn = $this->get('database_connection');

        $stmt = $conn->prepare('SELECT * FROM hostel_unit_logs_older;');

		/*

		$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');

		

		$file = 'pdo.ini';

		

        if(!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

       

        $dns = $settings['database']['driver'] .

        ':host=' . $settings['database']['host'] .

        ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .

        ';dbname=' . $settings['database']['schema'];

		

		

		$pdo = new PDO($dns, $settings['database']['username'], $settings['database']['password']);



        $stmt = $pdo->prepare("SELECT * FROM alert;");

		*/

		

        $stmt->execute();

		$fileName = "report.csv";

		

        $response = new StreamedResponse();

        $response->setStatusCode(200);

        $response->headers->set('Content-Type', 'text/csv');

		$response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        $response->setCallback(function() use($stmt) {

            $config = new ExporterConfig();

            $exporter = new Exporter($config);



            $exporter->export('php://output', new PdoCollection($stmt->getIterator()));

        });

        $response->send();



        return $response;

    }

}
