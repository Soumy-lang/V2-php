<?php

namespace Core;

class Verificator
{

    public function checkForm($config, $data, &$errors): bool
    {

        //Est-ce qu'on a le bon nb d'inputs
        if(count($config["inputs"]) != count($data)){
            echo "<pre>";
            print_r($config["inputs"]);
            echo "</pre>";
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            die("Tentative de Hack1");
        }else{
            //CSRF ???
            foreach ($config["inputs"] as $name=>$input){
                $submitName = str_replace(' ', '_', $name); //On remplace les espaces par des _
                if(!isset($data[$submitName])){ //Est-ce que le champ existe dans le formulaire
                    echo $submitName;
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                    die("Tentative de Hack2"); //Si non, on arrete tout

                }
                if($input["type"]=="email" && !self::checkEmail($data[$submitName])){ //Est-ce que l'email est valide
                    $errors[]="Email incorrect";
                }
                if($input["type"]=="password" && !self::checkPassword($data[$submitName])){ //Est-ce que le password est valide
                    $errors[]="Le mot de passe est invalide";
                }
                if($name == "Confirmation de mot de passe" && $data[$submitName] !== $data["Mot_de_passe"]){ //Est-ce que les mots de passe correspondent
                    $errors[]="Les mots de passe ne correspondent pas";
                }
                if($name == "Prénom" && !self::checkName($data[$submitName])){ //Est-ce que le prenom est valide
                    $errors[]="Prenom incorrect";
                }
                if($name == "Nom" && !self::checkName($data[$submitName])){ //Est-ce que le nom est valide
                    $errors[]="Nom incorrect";
                }
                if($name == "Nom d'utilisateur" && !self::checkUsername($data[$submitName])){ //Est-ce que le username est valide
                    $errors[]="Nom d'utilisateur invalide";
                }
            }

        }

        return empty($errors);
    }

    public static function checkPassword(String $password): bool
    {
        return (
            strlen($password) >= 8 &&
            preg_match("#[a-z]#", $password) &&
            preg_match("#[A-Z]#", $password) &&
            preg_match("#[0-9]#", $password)
        );
    }

    public static function checkEmail(String $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function checkName(String $name): bool
    {
        return preg_match("#[a-zA-Z]#", $name);
    }

    public static function checkUsername(String $username): bool
    {
        return preg_match("#^[a-zA-Z0-9_]+$#", $username);
    }

}
