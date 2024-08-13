<?php

namespace Forms;

class AddUser
{
    public function getConfig(): array
    {
        return [
            "config"=> [
                "method"=>"POST",
                "action"=>"register",
                "submit"=>"Ajouter l'utilisateur",
                "class"=>"form",
                "id"=>"form-register"
            ],
            "inputs"=> [
                "Prénom"=>["type"=>"text", "class"=>"input-form", "id"=>"firstname", "placeholder"=>"Prénom", "minlen"=>2, "required"=>true, "error"=>"Le prénom doit faire plus de 2 caractères"],
                "Nom"=>["type"=>"text", "class"=>"input-form","id"=>"lastname", "placeholder"=>"Nom", "minlen"=>2, "required"=>true, "error"=>"Le nom doit faire plus de 2 caractères"],
                "Nom d'utilisateur"=>["type"=>"text", "class"=>"input-form","id"=>"username", "placeholder"=>"Nom d'utilisateur", "minlen"=>2, "required"=>true, "error"=>"Le nom doit faire plus de 2 caractères"],
                "E-mail"=>["type"=>"email", "class"=>"input-form","id"=>"email", "placeholder"=>"E-mail", "required"=>true, "error"=>"Le format de l'email est incorrect"],
                "Mot de passe"=>["type"=>"password", "class"=>"input-form","id"=>"pwd", "placeholder"=>"Mot de passe", "required"=>true, "error"=>"Votre mot de passe doit faire plus de 8 caractères avec minuscule et chiffre"],
                "Confirmation de mot de passe"=>["type"=>"password", "class"=>"input-form","id"=>"pwdConfirm", "confirm"=>"pwd" ,"placeholder"=>"Confirmation mdp", "required"=>true, "error"=>"Votre mot de passe de confirmation ne correspond pas"],
            ]
        ];
    }
}