<?php
class Usuario {
    private $idusuario;
    private $nombre;
    private $direccion;
    private $email;
    private $clave;
    private $idcargo;
    private $cargo;


    // GET Y SET (Observadores y Modificadores)
    public function getidusuario() {
        return $this->idusuario;
    }

    public function setidusuario($idusuario) {
        $this->idusuario = $idusuario;
    }

    public function getnombre() {
        return $this->nombre;
    }

    public function setnombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getdireccion() {
        return $this->direccion;
    }

    public function setdireccion($direccion) {
        $this->direccion = $direccion;
    }
    
    public function getemail() {
        return $this->email;
    }

    public function setemail($email) {
        $this->email = $email;
    }


    public function getclave() {
        return $this->clave;
    }

    public function setclave($clave) {
        $this->clave = $clave;
    }

    public function getidcargo() {
        return $this->idcargo;
    }

    public function setidcargo($idcargo) {
        $this->idcargo = $idcargo;
    }

    public function getcargo() {
        return $this->cargo;
    }

    public function setcargo($cargo) {
        $this->cargo = $cargo;
    }

}
?>
