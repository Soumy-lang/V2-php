<?php
namespace Core;

class View
{
    private String $templateName;
    private String $viewName;
    private array $data = [];

    public function __construct(string $viewName, string $templateName = "neutral")
    {
        $this->setViewName($viewName);
        $this->setTemplateName($templateName);
    }

    /**
     * @param String $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        if(!file_exists("Views/components/Templates/".$templateName.".tpl.php"))
        {
            die("Le template Views/components/Templates/".$templateName.".tpl.php n'existe pas");
        }
        $this->templateName = "Views/components/Templates/".$templateName.".tpl.php";
    }

    /**
     * @param String $viewName
     */
    public function setViewName(string $viewName): void
    {
        if(!file_exists("Views/components/".$viewName.".view.php"))
        {
            die("La vue Views/components/".$viewName.".view.php n'existe pas");
        }
        $this->viewName = "Views/components/".$viewName.".view.php";
    }

    public function assign(string $key, $value): void
    {
        $this->data[$key]=$value;
    }

    public function includeComponent(string $component, array $config, /*array $dataError = [], array $dataSuccess = [],*/ string $styleButton): void
    {
        if(!file_exists("Views/components/Components/".$component.".php"))
        {
            die("Le composant Views/components/Components/".$component.".php n'existe pas");
        }
        $submitButtonClass = $styleButton;
        include "Views/components/Components/".$component.".php";
    }

    public function __destruct()
    {
        extract($this->data);
        include $this->templateName;
    }

}
