<?php

namespace TVS\Login\Service;

use Doctrine\ORM\EntityManager;
use TVS\Login\Entity\User;
use TVS\Base\Service\AbstractService;
use TVS\Application;
use TVS\Base\Lib\ResizeImage;

class LoginService extends AbstractService {

    public function __construct(EntityManager $em, User $user, Application $app) {
        parent::__construct($em, $app);
        $this->object = $user;
        $this->entity = "TVS\Login\Entity\User";
    }

    public function ajustaData(array $data = array()) {
        if ($data["password"] == '') {
            unset($data["password"]);
        }

        unset($data["image"]);

        if (!isset($data["ativo"])) {
            return $data;
        }

        if ($data["ativo"]) {
            $data["ativo"] = 1;
            return $data;
        }
        $data["ativo"] = 0;
        return $data;
    }

    public function setPrivilegesDefault($user) {
        $servicePrivilege = $this->app['PrivilegeService'];
        $serviceUser = $this->app['LoginService'];
        $default = $serviceUser->findOneBy(['username' => 'default']);
        $privileges = $servicePrivilege->findBy(['user' => $default]);
        foreach ($privileges as $privilegio) {
            $privilegio_novo = new \TVS\Login\Entity\Privilege();
            $privilegio_novo->setUser($user)
                    ->setRoute($privilegio->getRoute())
                    ->setDisplay($privilegio->getDisplay())
                    ->setNew($privilegio->getNew())
                    ->setEdit($privilegio->getEdit())
                    ->setDelete($privilegio->getDelete());
            $this->em->persist($privilegio_novo);
        }
        $this->em->flush();
    }

    public function findByUsernameAndPassword($username, $password) {
        $repo = $this->em->getRepository($this->entity);
        $ldap = $this->app['LDAP'];
        if (!$ldap->start()) {
            return $repo->findByUsernameAndPassword($username,$password);
        }
        if (!$ldap->checkLogin($username)) {
            $user = $repo->findByUsernameAndPassword($username,$password);
            if(!$user){
                return false;
            }
            if($user->getAD()){
                return false;
            }
            return $user;
        }
        if (!$ldap->checkLoginAndPassword($username, $password)) {
            return false;
        }

        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            $this->insert(['username' => $username, 'password' => $password, 'email' => $username . '@tsaengenharia.com.br', 'ativo' => true, 'ad'=> true]);
            $user = $repo->findOneBy(['username' => $username]);
            $this->setPrivilegesDefault($user);
        }
        return $user;
    }

    public static function uploadImage(array $files = array(), $username) {
        $types = ["jpeg"]; //Extensoes validas
        $completePath = __DIR__ . "/../../../../data";

        if (empty($files["tmp_name"]["image"])) {
            return false;
        }

        if (!is_dir("{$completePath}/profile")) {
            mkdir("{$completePath}/profile");
        }

        if (!is_dir("{$completePath}/profile/{$username}")) {
            mkdir("{$completePath}/profile/{$username}");
        }
        foreach ($types as $type) {
            if (strstr($files["type"]["image"], $type)) {
                $imagemPath = "/profile/{$username}/{$username}_" . time() . "." . $type;

                //----------------------------------------------------------------------------
                //UTILIZANDO RESIZE//
                $image = new ResizeImage();
                $image->setAttribute('DEFAULT_WIDTH', 100);
                $image->setAttribute('DEFAULT_HEIGHT', 100);
                $image->setAttribute('LOCAL_IMAGE', $files["tmp_name"]["image"]);
                $image->setAttribute('LOCAL_NEW_IMAGE', $completePath . $imagemPath);
                if ($image->processImageAndWriteToCache()) {
                    return $imagemPath;
                }
                //----------------------------------------------------------------------------
                // NÃO UTILIZANDO RESIZE//
                //if (move_uploaded_file($files["tmp_name"]["image"], $completePath . $imagemPath)) {
                //return $imagemPath;
                //}
                //----------------------------------------------------------------------------
                return false;
            }
        }
        return false;
    }

    publiC static function removeImage($path) {
        $completePath = __DIR__ . "/../../../../data";
        if (unlink($completePath . $path)) {
            return true;
        }
        return false;
    }

}
