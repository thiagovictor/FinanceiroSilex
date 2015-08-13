<?php

namespace TVS\Base\Lib;

class ConnectionLDAP {

    private $basedn;
    private $uid;
    private $server;
    private $port;
    private $protocol;
    private $binddn;
    private $password;
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function start() {
        $serviceConfig = $this->app['ConfigService'];
        $config = $serviceConfig->findConfig('ActiveDirectory');
        if (!$config) {
            return false;
        }
        if (!$config->getParametro('ativo')) {
            return false;
        }
        
        $properties = ['basedn', 'uid', 'server', 'port', 'protocol', 'binddn', 'password'];
        foreach ($properties as $value) {
            if (!$config->getParametro($value)) {
                return false;
            }
            $this->$value = $config->getParametro($value);
        }
        return true;
    }

    function checkProperties($login) {
        $ds = ldapConnect();
        $justthese = array("ou", "givenname", "sn", "mail", "description");
        $sr = @ldap_search($ds, $this->basedn, "{$this->uid}={$login}", $justthese);
        $info = @ldap_get_entries($ds, $sr);
        if ($info['count'] != 1) {
            return false;
        }
        $return = [];
        foreach ($justthese as $value) {
            if (!isset($info[0][$value][0])) {
                $return[$value] = '';
                continue;
            }
            $return[$value] = $info[0][$value][0];
        }
        return $return;
    }

    function checkLogin($login) {
        $ds = $this->ldapConnect();
        $sr = @ldap_search($ds, $this->basedn, "{$this->uid}={$login}");
        $info = @ldap_get_entries($ds, $sr);
        if ($info['count'] != 1) {
            return false;
        }
        return true;
    }

    function checkLoginAndPassword($login, $password) {
        $ds = $this->ldapConnect();
        $sr = @ldap_search($ds, $this->basedn, "$this->uid=$login");
        if (@ldap_count_entries($ds, $sr) == 0) {
            return false;                                       //User either does not exist or more than 1 users found
        }
        $dn = @ldap_get_dn($ds, ldap_first_entry($ds, $sr));
        $b =  @ldap_bind($ds, $dn, $password);
        if (!$b) {
            return false;                                       //login / password values don't match
        }
        return true;
    }

    function getValues($filter, $attributes) {
        $ds = $this->ldapConnect();
        $sr = @ldap_search($ds, $this->basedn, $filter, $attributes);
        $result = @ldap_get_entries($ds, $sr);
        return $result;
    }

    function ldapConnect() {
        $ds = @ldap_connect($this->server, $this->port);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $this->protocol);
        ldap_set_option($ds, LDAP_OPT_TIMELIMIT, 30);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        @ldap_bind($ds, $this->binddn, $this->password);
        return $ds;
    }

}
