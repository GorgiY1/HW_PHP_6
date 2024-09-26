<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>oop 2</title>
</head>
<body>
    <?php 
    trait PrinterTraits {
        public int $id;
        public function __construct(int $id) {
            $this->id = $id;
        }
        public function send_image($image) : void {
            echo "Printing the $image<hr>";
        }
        public function send_text(string $text) : void {
            echo "Printing the $text<hr>";
        }
    }
    trait TestTrait {
        public function send_text_2(string $text) : void {
            echo "Printing the $text<hr>";
        }
    }
    class Printer {
        use PrinterTraits;
        use TestTrait;
        public string $vender;

    }

    class Message extends Printer {}
    $message = new Message(5);
    $message->send_text("Hello");
    $message->send_text_2("Hello");
    ?>
</body>
</html>