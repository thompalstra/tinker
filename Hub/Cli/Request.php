<?php
namespace Hub\Cli;

use Frame;

class Request extends \Hub\Base\Request
{
    public function process(array $options = [])
    {
        array_shift($options);

        if(count($options) > 0){
            $path = str_replace(":", "/", $options[0]);
            $this->setPath($path);
            array_shift($options);

            foreach($options as $idx => $option){
                preg_match('/--(.*)=(.*)/', $option, $matches);
                if(count($matches) == 3){
                    $this->addParameter($matches[1], $matches[2]);
                } else {
                    $this->addParameter("name", $option);
                }
            }
        }


        $this->setMethod("get");
    }

    public function getRoutes()
    {
        return Frame::path(["routes", "cli.php"]);
    }

    public function getControllerPath()
    {
        return Frame::path(["App","Cli","Controllers"]);
    }
}
