<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Service\LoginService;

/**
 * @ORM\Entity(repositoryClass="TVS\Login\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user")
 */
class User {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=false)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     */
    protected $ativo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="is_ad", type="boolean", nullable=true)
     */
    protected $ad;

    /**
     * @ORM\PrePersist
     * @ORM\preUpdate
     */
    public function uploadImage() {
        $temp = $this->image;
        $forms = ["UserForm","UserFormEdit","ProfileForm"];
        $file = null;
        foreach ($forms as $value) {
            if (!isset($_FILES[$value])) {
                continue;
            }
            $file = $_FILES[$value];
        }
        if(!$file){
            return false;
        }
        $result = LoginService::uploadImage($file,  $this->username);
        if ($result) {
            $this->image = $result;
            if (!empty($temp)) {
                LoginService::removeImage($temp);
            }
        }
    }
    
    function getImage() {
        return $this->image;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getAtivo() {
        return $this->ativo;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setPassword($password) {
        if ($this->salt == NULL) {
            $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        }
        $this->password = $this->encryptedPassword($password);
        return $this;
    }

    public function encryptedPassword($password) {
        $hashSenha = hash('sha512', $password . $this->salt);
        for ($i = 0; $i < 64000; $i++) {
            $hashSenha = hash('sha512', $hashSenha);
        }
        return $hashSenha;
    }

    public function setAtivo($ativo) {
        $this->ativo = $ativo;
        return $this;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }
    
    function getAd() {
        return $this->ad;
    }

    function setAd($ad) {
        $this->ad = $ad;
        return $this;
    }
    
    public function __toString() {
        return $this->username;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'salt' => $this->getSalt(),
            'email' => $this->getEmail(),
            'ativo' => $this->getAtivo(),
            'ad' => $this->getAd()
        ];
    }

}
