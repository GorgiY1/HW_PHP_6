<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         body {
            background: linear-gradient(135deg, #000428, #004e92); /* Gradient background */
            color: #fff; /* White text */
            font-family: 'Roboto', sans-serif; /* Modern font */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height */
            overflow: hidden; /* Hide overflow */
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
            padding: 30px; /* Larger padding */
            border: 2px solid lightblue;
            box-shadow: 0 0 75px rgba(0, 255, 255, 0.5);
            border-radius: 30px; /* More rounded corners */
            text-align: center; /* Center text */
            color:#fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php 
            class City {
                public string $name;
                public string $type; // major, medium, minor

                public function __construct(string $name, string $type)
                {
                    $this->name = $name;
                    $this->type = $type;
                }
            }
            class Weather {
                public array $weather_in_cities;
                public function __construct(array $cities) {
                    foreach ($cities as $city) {
                        $this->weather_in_cities[$city->name] = (bool)rand(0,1);
                    }
                }
                public function is_good_weather_in_city(City $city) : bool {
                    return $this->weather_in_cities[$city->name];
                }
            }
            abstract class Transport {
                public string $type;
                protected float $speed;
                protected float $cost;
                protected float $cost_per_unit_weight;
                
                public function __construct(string $type, float $cost, float $speed) {
                    $this->type = $type;
                    $this->cost_per_unit_weight = $cost;
                    $this->speed = $speed;
                }
                public function calculateCost(float $weight, float $distance): float {
                    return $this->cost_per_unit_weight * $weight * $distance;
                }
            
                public function calculateTime(float $distance): float {
                    return $distance / $this->speed;
                }
            }
            class Transport_Agency {
                private array $branches;
                public array $orders;
                public function __construct() {
                    $this->branches = [];
                    $this->orders = [];
                }
                public function add_branch(Branch $branch) : void
                {
                    $this->branches[] = $branch;
                }

                public function receive_order(Order $order) : void {
                    $this->orders[] = $order;
                }
                public function receiveOrder(Order $order): void {
                    $this->orders[] = $order;
                    foreach ($this->branches as $branch) {
                        if ($branch->getCity() === $order->from) {
                            $branch->receiveOrder($order);
                            return;
                        }
                    }
                }
            
                public function processOrders(Weather $weather, array $transports): void {
                    foreach ($this->orders as $order) {
                        if ($order->get_status() === "Pending") {
                            foreach ($this->branches as $branch) {
                                if ($branch->city === $order->from) {
                                    $branch->organizeTransport($order, $transports, $weather);
                                    break;
                                }
                            }
                        }
                    }
                }

            }

            class Branch {
                public City $city;
                private array $orders;
                function __construct(City $city)
                {
                    $this->city = $city;
                    $this->orders = [];
                }

                public function receive_order(Order $order){
                    $this->orders[] = $order;
                }

                public function organizeTransport(Order $order, array $transports, Weather $weather): void {
                    foreach ($transports as $transport) {
                        if ($transport instanceof Air_Transport) {
                            $order->transport_type = $transport->type;
                                $order->update_status("In Transit");
                                return;
                            if ($transport->can_fly($order->from, $order->to, $weather)) {
                                $order->transport_type = $transport->type;
                                $order->update_status("In Transit");
                                return;
                            }
                        } else {
                            $order->transport_type = $transport->type;
                            $order->update_status("In Transit");
                            return;
                        }
                    }
                    $order->update_status("Delayed");
                }
            }

            class Car_Transport extends Transport {
                public function __construct() {
                    parent::__construct("Car", 1.0, 60.0);
                }
            }
            class Rail_Transport extends Transport {
                public function __construct() {
                    parent::__construct("Rail", 0.5, 80.0);
                }
            }
            class Air_Transport extends Transport {
                public function __construct() {
                    parent::__construct("Air", 3.0, 500.0);
                }
                public function can_fly(City $from, City $to, Weather $weather): bool {
                    return $weather->is_good_weather_in_city($from) && $weather->is_good_weather_in_city($to);
                }
            }

            class Order {
                public float $weight;
                public City $from;
                public City $to;
                public string $preference; //speed or coast 
                public string $status;
                public string $transport_type;

                public function __construct(float $weight, City $from, City $to, string $preference) {
                    $this->weight = $weight;
                    $this->from = $from;
                    $this->to = $to;
                    $this->preference = $preference;
                    $this->status = "Pending";
                }
                function get_status():string {
                    return $this->status;
                }
                function update_status(string $status_):void {
                    $this->status = $status_;
                }
            }
            // Инициализация городов, погоды и транспорта
            $cities = [
                new City("CityA", "major"),
                new City("CityB", "medium"),
                new City("CityC", "minor"),
            ];

            $weather = new Weather($cities);

            $transports = [
                new Car_Transport()
            ];

            // Инициализация транспортного агентства и филиалов
            $agency = new Transport_Agency();

            foreach ($cities as $city) {
                $branch = new Branch($city);
                $agency->add_branch($branch);
            }

            // Создание и обработка заказов
            $order1 = new Order(10.0, $cities[0], $cities[1], "speed");
            $order2 = new Order(20.0, $cities[1], $cities[2], "cost");
            $order3 = new Order(25.0, $cities[0], $cities[2], "speed");

            $agency->receive_order($order1);
            $agency->processOrders($weather, $transports);
            
            $transports = [
                new Rail_Transport()
            ];
            

            
            $agency->receive_order($order2);
            $agency->processOrders($weather, $transports);

            $transports = [
                new Air_Transport()
            ];
            
            
            $agency->receive_order($order3);

            $agency->processOrders($weather, $transports);

            foreach ($agency->orders as $order) {
                echo "Order from {$order->from->name} to {$order->to->name} - Status: Pending - Transport: {$order->transport_type}<hr>";
            } 
            echo "<br>";
            // Вывод состояния заказов
            foreach ($agency->orders as $order) {
                echo "Order from {$order->from->name} to {$order->to->name} - Status: {$order->get_status()} - Transport: {$order->transport_type}<hr>";
            } 
            echo "<br>";
            $order1->update_status("Delayed");
            $order2->update_status("Delayed");
            $order3->update_status("Delayed");

            foreach ($agency->orders as $order) {
                echo "Order from {$order->from->name} to {$order->to->name} - Status: {$order->get_status()} - Transport: {$order->transport_type}<hr>";
            } 
        ?>
    </div>
</body>
</html> 

