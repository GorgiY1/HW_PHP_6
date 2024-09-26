<?
    interface ICamera {
        function make_video() : void;
        function make_photo() : void;
    }
    interface IMessenger {
        function send_message(string $message) : void;
    }
    class Mobile implements ICamera, IMessenger {
        function make_video() : void {
            echo "Writing the video ...<hr>";
        }
        function make_photo() : void {
            echo "Loading the photo ...<hr>";
        }
        function send_message(string $message) : void {
            echo "Sending the $message to e-mail ... <hr>";
        }
    }
    $phone = new Mobile();
    $phone->send_message("Hello, World!");