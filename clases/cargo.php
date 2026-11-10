<?php
class Cargo {
    private $idCargo;
    private $cargo;

    // GET Y SET (Observadores y Modificadores)
    public function getidCargo() {
        return $this->idCargo;
    }

    public function setidCargo($idCargo) {
        $this->idCargo = $idCargo;
    }

    public function getcargo() {
        return $this->cargo;
    }

    public function setcargo($cargo) {
        $this->cargo = $cargo;
    }
}
?>
