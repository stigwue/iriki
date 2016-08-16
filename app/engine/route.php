<?php
namespace mongovc\engine;

class route extends config
{
    private $_routes;
    
    public function initialise($json_path)
    {
        $json = $this->load_json_file($json_path);
    }
}

?>
