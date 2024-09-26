<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Agency</title>
    <style>
        body {
            background: linear-gradient(135deg, #000428, #004e92);
            color: #fff;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border: 2px solid lightblue;
            box-shadow: 0 0 75px rgba(0, 255, 255, 0.5);
            border-radius: 30px;
            text-align: center;
            color: #fff;
            max-height: 90vh;
            overflow-y: auto;
        }
        form {
            margin-bottom: 20px;
        }
        input, select, button {
            margin: 5px 0;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
        }
        button {
            background-color: #00aaff;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #0077cc;
        }
        hr {
            border: 0;
            border-top: 1px solid #00aaff;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transport Agency</h1>
        <form method="post">
            <h2>Create Order</h2>
            <label for="weight">Weight:</label>
            <input type="number" name="weight" id="weight" step="0.1" required>
            <label for="from">From:</label>
            <select name="from" id="from">
                <option value="CityA">CityA</option>
                <option value="CityB">CityB</option>
                <option value="CityC">CityC</option>
            </select>
            <label for="to">To:</label>
            <select name="to" id="to">
                <option value="CityA">CityA</option>
                <option value="CityB">CityB</option>
                <option value="CityC">CityC</option>
            </select>
            <label for="preference">Preference:</label>
            <select name="preference" id="preference">
                <option value="speed">Speed</option>
                <option value="cost">Cost</option>
            </select>
            <button type="submit" name="createOrder">Create Order</button>
        </form>

        <form method="post">
            <button type="submit" name="processOrders">Process Orders</button>
        </form>

        <h2>Orders</h2>
        <?php
            class City {
                public string $name;
                public string $type;

                public function __construct(string $name, string $type) {
                    $this->name = $name;
                    $this->type = $type;
                }
            }

            class Weather {
                public array $weather_in_cities;

                public function __construct(array $cities) {
                    foreach ($cities as $city) {
                        $this->weather_in_cities[$city->name] = (bool)rand(0, 2);
                    }
                }

                public function is_good_weather_in_city(City $city): bool {
                    return $this->weather_in_cities[$city->name];
                }
            }

            abstract class Transport {
                public string $type;
                protected float $speed;
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

                public function add_branch(Branch $branch): void {
                    $this->branches[] = $branch;
                }
                public function receive_order(Order $order): void {
                    $this->orders[] = $order;
                    foreach ($this->branches as $branch) {
                        if ($branch->city->name === $order->from->name) {
                            $branch->receive_order($order);
                            return;
                        }
                    }
                }

                public function processOrders(Weather $weather, array $transports): void {
                    foreach ($this->orders as $order) {
                        if ($order->get_status() === "Pending") {
                            foreach ($this->branches as $branch) {
                                if ($branch->city->name === $order->from->name) {
                                    $branch->organizeTransport($order, $transports, $weather);
                                    break;
                                }
                            }
                        }
                        else {
                            $order->set_status("Delivered");
                        }
                    }
                }
            }

            class Branch {
                public City $city;
                private array $orders;

                function __construct(City $city) {
                    $this->city = $city;
                    $this->orders = [];
                }

                public function receive_order(Order $order) {
                    $this->orders[] = $order;
                }

                public function organizeTransport(Order $order, array $transports, Weather $weather): void {
                    foreach ($transports as $transport) {
                        if ($transport instanceof Air_Transport) 
                        {
                            $order->transport_type = $transport->type;
                            $order->update_status("In Transit");
                            return;
                        }
                        if ($transport instanceof Rail_Transport) 
                        {
                            $order->transport_type = $transport->type;
                            $order->update_status("In Transit");
                            return;
                        }
                        if ($transport instanceof Air_Transport) {
                            if ($transport->can_fly($order->from, $order->to, $weather)) {
                                $order->transport_type = $transport->type;
                                $order->update_status("In Transit");
                                return;
                            }
                            else {
                                $order->transport_type = $transport->type;
                                $order->update_status("Caneled");
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
                public string $preference;
                public string $status;
                public string $transport_type = "None";

                public function __construct(float $weight, City $from, City $to, string $preference) {
                    $this->weight = $weight;
                    $this->from = $from;
                    $this->to = $to;
                    $this->preference = $preference;
                    $this->status = "Pending";
                }

                public function get_status(): string {
                    return $this->status;
                }

                public function update_status(string $status_): void {
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
                new Car_Transport(),
                new Rail_Transport(),
                new Air_Transport(),
            ];

            // Инициализация транспортного агентства и филиалов
            $agency = new Transport_Agency();

            foreach ($cities as $city) {
                $branch = new Branch($city);
                $agency->add_branch($branch);
            }

            // Обработка формы создания заказа
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['createOrder'])) {
                $weight = floatval($_POST['weight']);
                $from = $_POST['from'];
                $to = $_POST['to'];
                $preference = $_POST['preference'];

                $from_city_array = array_values(array_filter($cities, fn($city) => $city->name === $from));
                $to_city_array = array_values(array_filter($cities, fn($city) => $city->name === $to));

                $from_city = $from_city_array[0] ?? null;
                $to_city = $to_city_array[0] ?? null;

                if ($from_city && $to_city) {
                    $order = new Order($weight, $from_city, $to_city, $preference);
                    $agency->receive_order($order);
                } else {
                    echo "Invalid city selection.<br>";
                }
                $agency->processOrders($weather, $transports);
            }

            // Обработка формы обработки заказов
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['processOrders'])) {
                $agency->processOrders($weather, $transports);
            }

            // Вывод состояния заказов
            foreach ($agency->orders as $order) {
                echo "Order from {$order->from->name} to {$order->to->name} - Status: {$order->get_status()} - Transport: {$order->transport_type}<hr>";
            }

            // $agency = new Transport_Agency();

            // foreach ($cities as $city) {
            //     $branch = new Branch($city);
            //     $agency->add_branch($branch);
            // }

            // // Создание и обработка заказов
            // $order1 = new Order(10.0, $cities[0], $cities[1], "speed");
            // $agency->receive_order($order1);

            // $order2 = new Order(20.0, $cities[1], $cities[2], "cost");
            // $agency->receive_order($order2);

            // $agency->processOrders($weather, $transports);

            // // Вывод состояния заказов
            // foreach ($agency->orders as $order) {
            //     echo "Order from {$order->from->name} to {$order->to->name} - Status: {$order->get_status()} - Transport: {$order->transport_type}<hr>";
            // }
        ?>
    </div>
</body>
</html>
