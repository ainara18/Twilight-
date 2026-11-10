<?php
require_once __DIR__ . '/../clases/conexion.php';
require_once __DIR__ . '/../clases/usuario.php';

class UsuarioModel {
    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Listar todos los usuarios
    public function Listar(): array {
        try {
            $result = [];
            $stm = $this->pdo->prepare("SELECT id_usuario, nombre, direccion, email, clave, idCargo FROM usuario");
            $stm->execute();

            foreach ($stm->fetchAll(PDO::FETCH_OBJ) as $r) {
                $usuario = new Usuario();
                $usuario->setidusuario($r->id_usuario);
                $usuario->setnombre($r->nombre);
                $usuario->setdireccion($r->direccion);
                $usuario->setemail($r->email);
                $usuario->setclave($r->clave);
                if (method_exists($usuario, 'setidcargo')) {
                    $usuario->setidcargo($r->idCargo);
                }
                $result[] = $usuario;
            }
            return $result;
        } catch (Exception $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }

    // Obtener un usuario por su ID
    public function Obtener(int $idusuario): ?Usuario {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
            $stm->execute([$idusuario]);
            $r = $stm->fetch(PDO::FETCH_OBJ);

            if ($r) {
                $usuario = new Usuario();
                $usuario->setidusuario($r->id_usuario);
                $usuario->setnombre($r->nombre);
                $usuario->setdireccion($r->direccion);
                $usuario->setemail($r->email);
                $usuario->setclave($r->clave);
                if (method_exists($usuario, 'setidcargo')) {
                    $usuario->setidcargo($r->idCargo);
                }
                return $usuario;
            }

            return null;
        } catch (Exception $e) {
            die("Error al obtener usuario: " . $e->getMessage());
        }
    }

    // Eliminar un usuario por ID (Dar de baja)
    public function Eliminar(int $idusuario): void {
        try {
            $stm = $this->pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?");
            $stm->execute([$idusuario]);
        } catch (Exception $e) {
            die("Error al eliminar usuario: " . $e->getMessage());
        }
    }

    // Actualizar un usuario
    public function Actualizar(Usuario $data): void {
        try {
            $params = [
                $data->getnombre(),
                $data->getdireccion(),
                $data->getemail()
            ];

            $sql = "UPDATE usuario
                    SET nombre = ?,
                        direccion = ?,
                        email = ?";

            if (!empty($data->getclave())) {
                $sql .= ", clave = ?";
                $params[] = $data->getclave();
            }

            $sql .= " WHERE id_usuario = ?";
            $params[] = $data->getidusuario();

            $stm = $this->pdo->prepare($sql);
            $stm->execute($params);

        } catch (Exception $e) {
            die("Error al actualizar usuario: " . $e->getMessage());
        }
    }

    // Registrar un nuevo usuario
    public function Registrar(Usuario $data): void {
        try {
            $sql = "INSERT INTO usuario (nombre, direccion, email, clave, idCargo) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $idCargoVal = method_exists($data, 'getidcargo') ? $data->getidcargo() : 2;

            $this->pdo->prepare($sql)->execute([
                $data->getnombre(),
                $data->getdireccion(),
                $data->getemail(),
                $data->getclave(),
                $idCargoVal ? $idCargoVal : 2
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Verificar correo y clave
    public function Verificar(Usuario $data): bool {
        try {
            $sql = "SELECT clave FROM usuario WHERE email = ?";
            $stm = $this->pdo->prepare($sql);
            $stm->execute([$data->getemail()]);
            $r = $stm->fetch(PDO::FETCH_OBJ);

            return $r && password_verify($data->getclave(), $r->clave);
        } catch (Exception $e) {
            die("Error al verificar usuario: " . $e->getMessage());
        }
    }

    // Buscar por nombre o email
    public function Buscar(string $termino): array {
        try {
            $result = [];
            $sql = "SELECT * FROM usuario
                    WHERE nombre LIKE :term OR email LIKE :term";
            $stm = $this->pdo->prepare($sql);
            $like = '%' . $termino . '%';
            $stm->bindParam(':term', $like, PDO::PARAM_STR);
            $stm->execute();

            foreach ($stm->fetchAll(PDO::FETCH_OBJ) as $r) {
                $usuario = new Usuario();
                $usuario->setidusuario($r->id_usuario);
                $usuario->setnombre($r->nombre);
                $usuario->setemail($r->email);
                $usuario->setdireccion($r->direccion);
                $usuario->setclave($r->clave);
                if (method_exists($usuario, 'setidcargo')) {
                    $usuario->setidcargo($r->idCargo);
                }
                $result[] = $usuario;
            }

            return $result;
        } catch (Exception $e) {
            die("Error al buscar usuario: " . $e->getMessage());
        }
    }

    // Login de usuario
    public function login(Usuario $data): ?Usuario {
        try {
            $sql = "SELECT id_usuario, nombre, direccion, email, clave, idCargo 
                    FROM usuario 
                    WHERE email = ?";
            
            $stm = $this->pdo->prepare($sql);
            $stm->execute([$data->getemail()]);
            $r = $stm->fetch(PDO::FETCH_OBJ);

            if ($r && password_verify($data->getclave(), $r->clave)) {
                $user = new Usuario();
                $user->setidusuario($r->id_usuario);
                $user->setnombre($r->nombre);
                $user->setdireccion($r->direccion);
                $user->setemail($r->email);
                if (method_exists($user, 'setidcargo')) {
                    $user->setidcargo($r->idCargo);
                }
                return $user;
            }

            return null;
        } catch (Exception $e) {
            die("Error al hacer login: " . $e->getMessage());
        }
    }
}
?>