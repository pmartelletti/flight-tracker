<?php
namespace FlightTracker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Carbon\Carbon;
use FlightTracker\Service\Skyscanner;
use FlightTracker\Model\SuitableItineraries;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class FlightTrackerCommand extends Command
{
    protected $input;
    protected $output;
    protected $trips;
    protected $conditions;

    public function __construct()
    {
        parent::__construct();
        $this->trips = new SuitableItineraries();
    }

    protected function configure()
    {
        $this
            ->setName('flights:find')
            ->setDescription('Find some awesome deals')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        // ask questions
        $helper = $this->getHelper('question');
        // day of week
        $question = new ChoiceQuestion(
          'What day of week do you want to start your trip?',
          array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
          0
        );
        $question->setErrorMessage('Choose a valid day of the week.');
        $dayOfWeek = $helper->ask($input, $output, $question);
        // trip duration
        $question = new Question('How many nights do you want to travel? : ');
        $duration = $helper->ask($input, $output, $question);
        // weeks to look
        $question = new Question('How many weeks (far from now) should we track? : ');
        $howFar = $helper->ask($input, $output, $question);

        // parse condtions
        // $this->parseConditions();
        // DEPARTURE City
        // TODO: ask departure city maybe?
        $departure = "MLA";
        $this->findBestOptions($departure, $dayOfWeek, $duration, $howFar);
        // draw table!
        $this->drawResultsTable();
    }

    private function findBestOptions($departure, $dayOfWeek, $duration, $howFar)
    {

        $this->output->writeln(sprintf('<comment>Searching <info>%s days</info> flights from <info>%s</info> departing <info>every %s</info> for the next <info>%s weeks.</info>', $duration, $departure, $dayOfWeek, $howFar));
        $start = new Carbon('next ' . $dayOfWeek);
        $end = $start->copy()->addWeeks($howFar);
        $dates = new \DatePeriod($start, new \DateInterval( 'P1W'), $end);
        foreach($dates as $date) {
            $currentStart = Carbon::instance($date);
            $currentEnd = $currentStart->copy()->addDays($duration);
            $destinations = ['BCN', 'MAD', 'PAR', 'LON', 'DUB', 'VLC', 'ATH', 'ROM'];
            // $destinations = ['MAD', 'PAR', 'LON', 'DUB', 'VLC'];
            foreach($destinations as $destination) {
                $this->findTripOptions($departure, $destination, $currentStart, $currentEnd);
            }
        }
    }

    /**
    * Find flights for a given city combination in the given days
    **/
    private function findTripOptions($departure, $return, $from, $to)
    {
        $tracker = new Skyscanner('pa348514742769890312717552238498');
        $tracker->trackFlights($departure, $return, $from, $to);
        foreach($tracker->getSuitableItineraries() as $trip) {
            $this->trips->addIfBetter($trip);
        }
    }

    private function drawResultsTable()
    {
        $table = new Table($this->output);
        $table
            ->setHeaders(array('From', 'To', 'Departure', 'Arrival', 'Duration (hs)', 'Stops'))
        ;
        $rows = [];
        foreach($this->trips as $trip) {
            $rows[] = $trip->getOutbound()->getArrayDetails();
            $rows[] = $trip->getInbound()->getArrayDetails();
            $rows[] = array(new TableCell('Price: EUR' . $trip->getPrice(), array('colspan' => 6)));
            // $rows[] = array(new TableCell('Booking Link: ' . $trip->getBookingLinks(), array('colspan' => 3)));
            $rows[] = new TableSeparator();
        }
        $table->addRows($rows);
        $table->render();
    }

    private function satifiesTripConditions($data)
    {

    }

    public function parseConditions()
    {

    }
}
