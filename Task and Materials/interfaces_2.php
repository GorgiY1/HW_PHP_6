<?
 interface ICounterInterface {
    public function increment() : void;
    public function decrement() : void;
    public function get_value() : int;
}
class DecimalCounter implements ICounterInterface {
    public function __construct(
        private int $value = 0,
        private int $min = 0,
        private int $max = 100
    ) {
        if($min >= $max) throw new InvalidArgumentException("Error value"); 
        $this->value = $value;
        $this->min = $min;
        $this->max = $max;
    }
    public function increment() : void {
        if($this->value < $this->max) $this->value++;
    }
    public function decrement() : void {
        if($this->value > $this->min) $this->value--;
    }
    public function get_value() : int {
        return $this->value;
    }
    public function demonstrate_counter(ICounterInterface $counter) : void {
        $counter->increment();
        echo "<p>After increment: ".$counter->get_value()."</p>";
        $counter->decrement();
        echo "<p>After decrement: ".$counter->get_value()."</p>";
    }
}
$counter = new DecimalCounter();
for ($i=0; $i < 50; $i++) {
    $counter->increment();
}
$counter->demonstrate_counter($counter);