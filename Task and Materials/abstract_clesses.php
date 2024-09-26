<?    abstract class Messenger {
        protected string $name;
        public abstract function send(string $message) : void;
        public function __construct(string $name) {
            $this->name = $name;
        }
        public function close() {
            echo "Closed <hr>";
        }
    }
    class TelegramMessenger extends Messenger {
        public function send(string $message) : void {
            echo "Web telegram sending the $message ...<hr>";
        }
        public function print_name() : void {
            echo "<h3>$this->name</h3>";
        }
    }
    class TelegramBot extends TelegramMessenger {
        //polimorf
        public function send(string $message) : void {
            echo "Bot telegram sending the $message ...<hr>";
        }
    }
    $web_telegram = new TelegramMessenger("Web telegram");
    $bot_telegram = new TelegramBot("Bot telegram");
    $web_telegram->send("Hello");
    $bot_telegram->send("Hello");
    $bot_telegram->print_name();
    $web_telegram->print_name();