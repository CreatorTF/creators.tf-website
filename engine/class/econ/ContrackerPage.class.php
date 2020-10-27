<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ContrackerPage extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hOwner = $data["owner"];
        $this->m_hConfig = $data["config"];
    }

    function toDOM($tags = [], $brackets = [], $template = "tree")
    {
        return render("prefabs/contracker/pages/".$template,
            array_merge([
                "background" => $this->getValue("background"),
                "title" => $this->getTitle(),
                "parent" => $this->getParent() !== NULL ? $this->getParent()->getTitle() : NULL,
                "dir" => $this->getValue("dir"),

                "nodes" => join("", array_map(function($Node) {
                    $Params = $this->getNodeParams($Node);
                    return $Node->toDOMNode([
                        "posX" => $Params["position"]["x"] ?? "50%",
                        "posY" => $Params["position"]["y"] ?? "50%"
                    ]);
                }, $this->getNodes())),

                "quests" => join("", array_map(function($Quest) {
                    $Params = $this->getQuestParams($Quest);
                    return $Quest->toDOM([
                        "posX" => $Params["position"]["x"] ?? "50%",
                        "posY" => $Params["position"]["y"] ?? "50%",

                        "connect" => htmlentities(json_encode($Params["connect"] ?? []))
                    ]);
                }, $this->getQuests()))
            ], $tags),
            array_merge([
                "VISIBLE" => false
            ], $brackets)
        );
    }

    function isUnlocked()
    {
        return count($this->getUncompletedDependencies()) == 0;
    }

    function getUncompletedDependencies()
    {
        return array_filter($this->getDependencies(), function($Quest) {
            return !$Quest->isCompleted();
        });
    }

    function getDependencies()
    {
        $Page = $this->getParent();
        if(!isset($Page)) return [];

        $List = [];
        foreach ($Page->getNodeParams($this)["required"] ?? [] as $title) {
            $Quest = $this->getOwner()->getContracker()->getQuest($title);
            if(isset($Quest))
            {
                array_push($List, $Quest);
            }
        }

        return $List;
    }

    function toDOMNode($tags = [], $brackets = [])
    {
        return render("prefabs/contracker/pages/node",
            array_merge([
                "name" => $this->getName(),
                "count" => $this->getQuestCount(),
                "title" => $this->getTitle()
            ], $tags),
            array_merge([
                "INACTIVE" => !$this->isUnlocked()
            ], $brackets)
        );
    }

    function getParent()
    {
        foreach ($this->getOwner()->getContracker()->getPages() as $Page) {
            if($Page->getNode($this->getTitle()) !== NULL)
                return $Page;
        }
    }

    function getQuestParams($Quest)
    {
        foreach ($this->m_hConfig["quests"] as $quest) {
            if($quest["title"] == $Quest->getTitle())
                return $quest;
        }
    }

    function getNodeParams($Node)
    {
        foreach ($this->m_hConfig["nodes"] as $node) {
            if($node["title"] == $Node->getTitle())
                return $node;
        }
    }

    function getQuestCount()
    {
        return $this->getQuestCount_STEP($this->m_hConfig);
    }

    private function getQuestCount_STEP($node)
    {
        $count = count($node["quests"] ?? []);
        foreach (($node["nodes"] ?? []) as $node) {
            $count += $this->getQuestCount_STEP($node);
        }
        return $count;
    }

    function getNodes()
    {
        $Return = [];
        foreach (($this->m_hConfig["nodes"] ?? []) as $node)
        {
            $Page = $this->getOwner()->getContracker()->getPage($node["title"]);
            if($Page !== NULL)
            {
                array_push($Return, $Page);
            }
        }
        return $Return;
    }

    function getQuests()
    {
        $Return = [];
        foreach (($this->m_hConfig["quests"] ?? []) as $quest)
        {
            $Quest = $this->getOwner()->getContracker()->getQuest($quest["title"]);
            if($Quest !== NULL)
            {
                array_push($Return, $Quest);
            }
        }
        return $Return;
    }

    function getQuest($name)
    {
        foreach ($this->getQuests() as $Quest) {
            if($Quest->m_iIndex == $name || $Quest->m_hConfig["title"] == $name)
                return $Quest;
        }

        return NULL;
    }

    function getNode($name)
    {
        foreach ($this->getNodes() as $Page) {
            if($Page->getTitle() == $name)
                return $Page;
        }

        return NULL;
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }

    function getName()
    {
        return $this->getValue("name");
    }

    function getTitle()
    {
        return $this->getValue("title");
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function isDefault()
    {
        return ($this->m_hConfig["default"] ?? false) === true;
    }
}

?>
